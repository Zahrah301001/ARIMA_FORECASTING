<?php 
    include "koneksi.php";

    // ============== COEF / KOEFISIEN ==============
    // Langkah - langkah dari perhitungan koefisien ini sebagai berikut :
    // 1. DATA AKTUAL
    // 2. DATA DIFERENSIASI / Diff
    // 3. DATA LAG DIFERENSIASI 1 / Lag Diff 1
    // 4. MA
    // 5. SLOPE AR / REGRESI LINEAR AR
    // 6. SLOPE MA / REGRESI LINEAR MA
    // NB: Setiap langkah ada step by step nya lagi, mohon diperhatikan dan dibaca dengan seksama

    // 1. DATA AKTUAL
    // Mengambil data aktual dari database
    $query = "SELECT * FROM tb_data";
    $result = mysqli_query($koneksi, $query);
    // Menyimpan hasil looping DATA AKTUAL dari database ke array
    $array_data_aktual = []; // variabel ini menyimpan semua nilai dari 96 data aktual
    while($row = mysqli_fetch_assoc($result)){
        $array_data_aktual[] = $row['d_aktual'];
    }
    $total_data_aktual = count($array_data_aktual); // variabel ini menyimpan total data aktual yaitu 96, variabel ini nanti akan sering digunakan untuk digunakan pada perulangan while

    // 2. DATA DIFERENSIASI / Diff
    // Mencari dan menyimpan DATA DIFERENSIASI
    $array_diferensiasi = []; // variabel ini menyimpan semua nilai dif / diferensiasi
    $index_dif = 0;
    while($index_dif < $total_data_aktual){
        if($index_dif > 0){
            $hasil_diferensiasi = $array_data_aktual[$index_dif] - $array_data_aktual[$index_dif - 1];
            $array_diferensiasi[] = $hasil_diferensiasi;

            $index_dif++;
        } else {
            $array_diferensiasi[] = 0;

            $index_dif++;
        }
    }

    // 3. DATA LAG DIFERENSIASI 1 / Lag Diff 1
    // Mencari dan menyimpan DATA DIFERENSIASI 1
    // Rumus dibawah hampir sama dengan rumus diferensiasi diatas, perbedaannya ada pada kondisi di dalam looping yang dimulai jika index lebih dari 1 dan pada rumus di line 47
    $array_lag_diferensiasi = []; // variabel ini menyimpan semua nilai diff 1 / lag_diferensiasi 1
    $index_lag_dif = 0;
    while($index_lag_dif < $total_data_aktual){
        if($index_lag_dif > 1){
            $hasil_lag_diferensiasi = $array_data_aktual[$index_lag_dif - 1] - $array_data_aktual[$index_lag_dif - 2];
            $array_lag_diferensiasi[] = $hasil_lag_diferensiasi;

            $index_lag_dif++;
        } else {
            $array_lag_diferensiasi[] = 0;

            $index_lag_dif++;
        }
    }

    // 4. MA
    $array_ma = []; // variabel ini menyimpan semua nilai MA
    $index_ma = 0;
    while($index_ma < $total_data_aktual){
        if($index_ma > 0){
            $avg_y_kolom = ($array_data_aktual[$index_ma - 1] + $array_data_aktual[$index_ma]) / 2;
            $array_ma[] = $avg_y_kolom;

            $index_ma++;
        } else {
            $array_ma[] = 0;

            $index_ma++;
        }
    }

    // 5. SLOPE AR / REGRESI LINEAR AR
    // Pada excel kolom F3, rumus yang digunakan adalah =SLOPE(B4:B98;C4:C98) atau SLOPE(kolom_y, kolom_x)
    // SLOPE pada excel berguna untuk menghitung kemiringan garis regresi linier pada serangkaian titik data
    // Untuk titik data yang digunakan pada rumus ini adalah kolom B4 - B98 (y) dan C4 - C98 (x)
    // Langkah langkah codingannya sebagai berikut :
    // A. Mencari total atau jumlah dari kolom C (x) dan B (y)
    $avg_x_ar = array_sum(array_slice($array_lag_diferensiasi, 2));
    $avg_y_ar = array_sum(array_slice($array_diferensiasi, 2));

    // B. Mencari nilai xy dan x kuadrat
    // Untuk xy = kolom x * kolom y
    // Untuk x kuadrat = kolom x^2
    $hasil_xy_ar = 0;
    $index_xy_ar = 0;
    while($index_xy_ar < count(array_slice($array_diferensiasi, 2))){
        $hasil_xy_ar = $hasil_xy_ar + ($array_lag_diferensiasi[$index_xy_ar + 2] * $array_diferensiasi[$index_xy_ar + 2]);

        $index_xy_ar++;
    }

    $hasil_xkuadrat_ar = 0;
    $index_xkuadrat_ar = 0;
    while($index_xkuadrat_ar < count(array_slice($array_diferensiasi, 2))){
        $hasil_xkuadrat_ar = $hasil_xkuadrat_ar + ($array_lag_diferensiasi[$index_xkuadrat_ar + 2]**2);

        $index_xkuadrat_ar++;
    }

    // C. Mencari nilai SLOPE
    // Rumusnya 
    $nilai_slope_ar = ((count(array_slice($array_diferensiasi, 2)) * $hasil_xy_ar) - ($avg_x_ar * $avg_y_ar)) / ((count(array_slice($array_diferensiasi, 2)) * $hasil_xkuadrat_ar) - ($avg_x_ar**2));

    // 6. SLOPE MA / REGRESI LINEAR MA
    // Pada excel kolom F3, rumus yang digunakan adalah =SLOPE(A3:A98;D3:D98)/10
    // SLOPE pada excel berguna untuk menghitung kemiringan garis regresi linier pada serangkaian titik data 
    // Untuk titik data yang digunakan pada rumus ini adalah kolom A3 - A98 (y) dan D3 - D98 (x)
    // Rumusnya sama seperti slope ar, perbedaannya hanya pada kolom yang dicari dan hasil akhirnya dibagi 10
    // Langkah langkah codingannya sebagai berikut :
    $avg_x_ma = array_sum($array_ma);
    $avg_y_ma = array_sum($array_data_aktual);

    $hasil_xy_ma = 0;
    $index_xy_ma = 0;
    while($index_xy_ma < count($array_ma)){
        $hasil_xy_ma = $hasil_xy_ma + ($array_ma[$index_xy_ma] * $array_data_aktual[$index_xy_ma]);

        $index_xy_ma++;
    }

    $hasil_xkuadrat_ma = 0;
    $index_xkuadrat_ma = 0;
    while($index_xkuadrat_ma < count($array_ma)){
        $hasil_xkuadrat_ma = $hasil_xkuadrat_ma + ($array_ma[$index_xkuadrat_ma]**2);

        $index_xkuadrat_ma++;
    }

    $nilai_slope_ma = (((count($array_ma) * $hasil_xy_ma) - ($avg_x_ma * $avg_y_ma)) / ((count($array_ma) * $hasil_xkuadrat_ma) - ($avg_x_ma**2))) / 10;

?>