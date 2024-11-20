<?php 
    include "koneksi.php";

    // ============== ACF ==============
    // Langkah - langkah dari perhitungan preprocessing ACF ini sebagai berikut :
    // 1. DATA AKTUAL
    // 2. DATA DIFERENSIASI
    // 3. DEVISIASI KUADRAT
    // 4. AUTOCOV
    // 5. ACF
    // 6. 2SE
    // 7. G-
    // 8. G+
   

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

    // 2. DATA DIFERENSIANSI
    // Mencari dan menyimpan DATA DIFERENSIASI
    $array_diferensiasi = []; // variabel ini menyimpan semua nilai dif / diferensiasi
    $index_dif = 0;
    while($index_dif < $total_data_aktual){
        if($index_dif > 0){
            $hasil_diferensiasi = $array_data_aktual[$index_dif] - $array_data_aktual[$index_dif - 1];
            $array_diferensiasi[] = $hasil_diferensiasi;

            $index_dif++;
        } else {
            $array_diferensiasi[] = null;

            $index_dif++;
        }
    }

    // 3. DEVISIASI KUADRAT
    // Mencari DEVISIASI KUADRAT / 10
    // Pada excel, rumusnya sebagai berikut =(DEVSQ(C3:C98))/10
    // Jika diubah ke perhitungan manual (tanpa menggunakan devisiasi kuadrat / DEVSQ) = ((data aktual - rata rata data aktual)^2)/10
    // Langkah - langkah sebagai berikut (A-C):
    // A. Mencari rata rata
    $avg_data_aktual = array_sum($array_data_aktual) / $total_data_aktual; // variabel ini untuk menyimpan nilai rata rata dari data aktual

    // B. Mencari hasil devisiasi kuadrat / melakukan operasi pengurangan data aktual dengan nilai rata rata data aktual lalu dipangkatkan 2 
    $nilai_devisiasi = 0;
    $index_dev = 0;
    while($index_dev < $total_data_aktual){
        $hasil_devisiasi = ($array_data_aktual[$index_dev] - $avg_data_aktual)**2;
        $nilai_devisiasi = $nilai_devisiasi + $hasil_devisiasi;

        $index_dev++;
    }

    // C. Membagi nilai devisiasi dengan 10
    // variabel dibawah ini menampung nilai akhir dari perhitungan devisiasi (pada excel ada di kolom E2). 
    $devisiasi = $nilai_devisiasi / 10; 

    // 4. AUTOCOV
    // Mencari nilai Autocov
    // Rumus pada excel = SUMPRODUCT($D$3:INDEX($D$3:$D$98;ROWS(D4:$D$98));D4:$D$98)
    // Rumus diatas menggunakan nilai yang ada pada kolom diferensiasi / Dif1
    // Nilai D4 diatas bersifat dinamis, jadi bisa berubah ubah
    // Karena nilainya ada yang dinamis, maka nanti hasil perhitungannya akan disimpan pada array, dan untuk data yang diambil pada Autocov ini adalah 20 data.
    // Kita harus memecah rumus diatas menjadi beberapa bagian =
    // - BAGIAN 1, a = ROWS(D4:$D$98) => ROWS ini berfungsi untuk mendapatkan jumlah baris yang ada dari rentang D4 - D98, yang berarti ada 95 baris
    // - BAGIAN 2, b = INDEX($D$3:$D$98;ROWS(D4:$D$98)) = INDEX($D$3:$D$98;a) => INDEX berfungsi untuk mendapatkan nilai tertentu dari suatu array berdasarkan indeks yang diberikan. Untuk contoh disamping jika kita ubah menjadi sebuah kalimat, maka menjadi "carikan data dari kolom D3 - D98 pada indeks a yang mana nilai a sudah kita cari sebelumnya yaitu 95". Jadi indeks ke 95 adalah kolom D97.
    // - BAGIAN 3, c = $D$3:INDEX($D$3:$D$98;ROWS(D4:$D$98)) = $D$3:b => Setelah mendapatkan nilai b diatas, maka rentang pada nilai c disini adalah D3 - D97
    // - BAGIAN 4, d = SUMPRODUCT($D$3:INDEX($D$3:$D$98;ROWS(D4:$D$98));D4:$D$98) = SUMPRODUCT(c;D4:$D$98) => SUMPRODUCT() berguna untuk mendapatkan jumlah hasil berkalian dari 2 array data. Untuk contoh disamping berarti yang akan dijumlahkan adalah hasil perkalian dari baris D3 - D97 dengan D4 - D98
    // Setalah mengetahui bagian bagiannya, kita ubah setiap bagian diatas menjadi codingan. 
    $array_autocov = []; // variabel ini untuk menyimpan data perhitungan autocov yang mana ada 20 data, sesuai dengan yang ada pada excel
    $index_cov = 0;
    // Karena yang diambil untuk autocov ini hanya 20 data, maka semua perhitungan tiap bagian dimasukkan ke looping dengan index <= 
    // kondisi > 0 karena indeks 0 bernilai null / kosong
    while($index_cov <= 20){
        if($index_cov > 0){
            // A. MENCARI BAGIAN 1
            $nilai_rows = count(array_slice($array_diferensiasi, $index_cov)); // variabel ini untuk menyimpan hasil ROWS(D4:$D$98, dst). Nilai yang ditampung berupa angka yang dijadikan sebagai indeks pada BAGIAN 2, indeks tsb sebagaia contoh 95, 94, dst

            // B. MENCARI BAGIAN 2
            $nilai_index = count(array_slice($array_diferensiasi, 0, $nilai_rows)); // variabel ini untuk menyimpan hasil INDEX($D$3:$D$98;ROWS(D4:$D$98), dst). Nilai yang ditampung berupa angka yang dijadikan sebagai indeks pada BAGIAN 3, indeks tsb sebagaia contoh 95, 94, dst

            // Sebagai CATATAN, hasil dari BAGIAN 1 DAN 2 SAMA. saya tetap membuatnya walaupun hasilnya sama, karena saya mengikuti langkah langkah dari rumus di excel.
            
            // C. MENCARI BAGIAN 3
            // pada bagian 3 ini, yaitu $D$3:INDEX($D$3:$D$98;ROWS(D4:$D$98)) atau $D$3:b berarti kolom D3 sampai hasil dari $nilai_index diatas, sebagai contoh 95 yang berarti jika diumpamakan sebagai kolom adalah D97, dst.
            // Pada bagian ini tidak perlu perhitungan karena hanya perlu dapat rentang kolom atau indeksnya saja, yang mana sudah kita dapatkan dari BAGIAN 2

            // D MENCARI BAGIAN 4
            // pada bagian ini, yang dicari adalah SUMPRODUCT($D$3:INDEX($D$3:$D$98;ROWS(D4:$D$98));D4:$D$98) atau SUMPRODUCT(c;D4:$D$98).
            // pada bagian sebelumnya didapat inndeks 95 yang berarti nilai c.
            // maka jika (c;D4:$D$98) dijadikan ke dalam program, maka c adalah indeks 0 - 95 kemudian D4:$D$98 adalah indeks 1 - 96. begitu seterusnya.
            $arr_kolom_satu = array_slice($array_diferensiasi, 0, $nilai_index);
            $arr_kolom_dua = array_slice($array_diferensiasi, $index_cov);
            
            $hasil_kali_kolom = array_map(function($a, $b) {
                return $a * $b;
            }, $arr_kolom_satu, $arr_kolom_dua);
            
            $hasil_jumlah_kolom = array_sum($hasil_kali_kolom);
            $array_autocov[] =  $hasil_jumlah_kolom;
        }
        
        $index_cov++;
    }
    
    // 5. ACF
    $array_acf = []; // variabel ini untuk menyimpan semua nilai acf
    $index_acf = 0;
    while($index_acf < count($array_autocov)){
        $hasil_acf = $array_autocov[$index_acf] / $devisiasi;
        $array_acf[] = $hasil_acf;

        $index_acf++;
    }

    // codingan dibawah dibuat agar nilai acf memiliki 3 angka dibelakang koma
    // karena jika menggunakan nilai acf yang ada pada array_acf diatas, nilai dibelkang koma ada lebih dari 3
    // agar pada tampilan grafik angkanya tidak terlalu panjang, maka dibuatlah codingan dibawh ini.
    $array_acf_less_decimal = array_map(function($num) {
        return round($num, 3); // round() berguna untuk membulatkan nilai dan angka 3 berarti membulatkan nilai tapi menyisakan 3 angka dibelakang koma
    }, $array_acf);

    // 6. 2SE
    // Pada excel, rumusnya sebagai berikut = SQRT((1/COUNT($D$3:$D$98))*(1+2*SUMSQ($F$3:F3)))
    // Dari rumus di atas, akan saya pecah menjadi seperti ini :
    // hasil dari perhitungan (1/COUNT($D$3:$D$98)) saya simpan pada variabel hasil_x
    // hasil dari perhitungan (1+2*SUMSQ($F$3:F3)) saya simpan pada variabel hasil_y
    // maka, rumusnya bisa diumpamakan menjadi SQRT(hasil_x * hasil_y) yang akan saya simpan pada variabel hasil_se
    $array_se_acf = []; // variabel ini untuk menyimpan semua nilai 2SE ACF
    $index_se_acf = 0;
    while($index_se_acf < count($array_acf)){
        $nilai_x = 1 / ((count($array_diferensiasi) - 1)); // -1 karena indeks 0 berisi null, jadi -1 agar indeks yang didapat 95

        // karena pada (1+2*SUMSQ($F$3:F3)) terdapat SUMSQ() yang berarti mencari jumlah kuadrat dari rentang yang diberikan,
        // maka perlu dilakukan looping
        $hasil_jumlah_kuadrat = 0;
        $index_jk = 0;
        while($index_jk <= $index_se_acf){
            $hasil_jumlah_kuadrat = $hasil_jumlah_kuadrat + ($array_acf[$index_jk]**2);

            $index_jk++;
        }
        $nilai_y = 1 + 2 * $hasil_jumlah_kuadrat;

        $hasil_se_acf = sqrt($nilai_x * $nilai_y);
        $array_se_acf[] = $hasil_se_acf;

        $index_se_acf++;
    }

    // 7. G-
    $array_gminus_acf = []; // variabel ini untuk menyimpan semua nilai G-
    $index_gm_acf = 0;
    while($index_gm_acf < count($array_se_acf)){
        $hasil_acf = -1.96 * $array_se_acf[$index_gm_acf];
        $array_gminus_acf[] = round($hasil_acf, 3); // round() berguna untuk membulatkan nilai dan angka 3 berarti membulatkan nilai tapi menyisakan 3 angka dibelakang koma

        $index_gm_acf++;
    }
    
    // 8. G+
    $array_gplus_acf = []; // variabel ini untuk menyimpan semua nilai G+
    $index_gp_acf = 0;
    while($index_gp_acf < count($array_se_acf)){
        $hasil_gplus_acf = 1.96 * $array_se_acf[$index_gp_acf];
        $array_gplus_acf[] = round($hasil_gplus_acf, 3); // round() berguna untuk membulatkan nilai dan angka 3 berarti membulatkan nilai tapi menyisakan 3 angka dibelakang koma

        $index_gp_acf++;
    }


    // ============== PACF ==============
    // Langkah - langkah dari perhitungan preprocessing ACF ini sebagai berikut :
    // 1. NILAI K
    // 2. PACF
    // 3. 2SE
    // 4. G-
    // 5. G+
    

    // 1. NILAI K
    $array_k = []; // variabel ini digunakan untuk menyimpan seluruh data nilai K. Array disamping menyimpan data yang memiliki key sehingga menjadi array multi dimensi.
    $index_k = 0;
    while($index_k < count($array_acf)){
        if($index_k > 1){
            // Mencari nilai teratas / baris pertama setiap kolom
            $index_polasatu_start = 0;
            $index_polasatu_end = $index_k - 1;
            $nilai_polasatu_a = 0;
            $nilai_polasatu_b = 0;
            while($index_polasatu_start < $index_k){
                $x = $array_k[$index_polasatu_start][$index_polasatu_end] * $array_acf[$index_polasatu_end];
                $nilai_polasatu_a = $nilai_polasatu_a + $x;

                $y = $array_k[$index_polasatu_start][$index_polasatu_end] * $array_acf[$index_polasatu_start];
                $nilai_polasatu_b = $nilai_polasatu_b + $y;

                $index_polasatu_start++;
                $index_polasatu_end--;

                if($index_polasatu_start == $index_k){
                    $array_k[$index_k][] = (($array_acf[$index_k] - ($nilai_polasatu_a)) / (1 - ($nilai_polasatu_b)));
                }
            }

            // Mencari nilai setelah baris pertama pada kolom
            $index_poladua_start = 0;
            $index_poladua_end = $index_k - 1;
            while($index_poladua_start < $index_k){
                $array_k[$index_poladua_start][] = $array_k[$index_poladua_start][$index_poladua_end] - ($array_k[$index_poladua_end][$index_poladua_start] * $array_k[$index_k][0]);

                $index_poladua_start++;
                $index_poladua_end--;
            }

        } elseif ($index_k == 1){
            $array_k[$index_k][] = (($array_acf[1] - ($array_k[0][0] * $array_acf[0])) / (1 - ($array_k[0][0] * $array_acf[0])));

            $array_k[0][] = $array_k[0][0] - ($array_k[0][0] * $array_k[1][0]);
        } else {
            $array_k[$index_k][] = $array_acf[0];
        }

        $index_k++;
    }

    // 2. PACF
    $array_pacf = []; // variabel ini untuk menyimpan semua nilai pacf
    $index_pacf = 0;
    while($index_pacf < count($array_k)){
        $array_pacf[] = $array_k[$index_pacf][0];

        $index_pacf++;
    }

    // codingan dibawah dibuat agar nilai pacf memiliki 3 angka dibelakang koma
    // karena jika menggunakan nilai pacf yang ada pada array_pacf diatas, nilai dibelakang koma ada lebih dari 3
    // agar pada tampilan grafik angkanya tidak terlalu panjang, maka dibuatlah codingan dibawh ini.
    $array_pacf_less_decimal = array_map(function($num) {
        return round($num, 3); // round() berguna untuk membulatkan nilai dan angka 3 berarti membulatkan nilai tapi menyisakan 3 angka dibelakang koma
    }, $array_pacf);

    // 3. 2SE
    // Pada excel, rumusnya sebagai berikut = = 1/SQRT(COUNT($D$3:$D$98))
    $array_se_pacf = []; // variabel ini untuk menyimpan semua nilai 2SE PACF
    $index_se_pacf = 0;
    while($index_se_pacf < count($array_pacf)){
        $hasil_se_pacf = 1 / (sqrt(count($array_data_aktual) - 1)); // -1 karena indeks 0 berisi null, jadi -1 agar indeks yang didapat 95
        $array_se_pacf[] = $hasil_se_pacf;

        $index_se_pacf++;
    }

    // 4. G-
    $array_gminus_pacf = []; // variabel ini untuk menyimpan semua nilai G-
    $index_gm_acf = 0;
    while($index_gm_acf < count($array_se_pacf)){
        $hasil_acf = -1.96 * $array_se_pacf[$index_gm_acf];
        $array_gminus_pacf[] = round($hasil_acf, 3); // round() berguna untuk membulatkan nilai dan angka 3 berarti membulatkan nilai tapi menyisakan 3 angka dibelakang koma

        $index_gm_acf++;
    }
    
    // 5. G+
    $array_gplus_pacf = []; // variabel ini untuk menyimpan semua nilai G+
    $index_gp_pacf = 0;
    while($index_gp_pacf < count($array_se_pacf)){
        $hasil_gplus_acf = 1.96 * $array_se_pacf[$index_gp_pacf];
        $array_gplus_pacf[] = round($hasil_gplus_acf, 3); // round() berguna untuk membulatkan nilai dan angka 3 berarti membulatkan nilai tapi menyisakan 3 angka dibelakang koma

        $index_gp_pacf++;
    }

    // die;

?>