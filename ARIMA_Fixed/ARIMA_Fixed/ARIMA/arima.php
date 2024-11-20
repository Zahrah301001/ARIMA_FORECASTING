<?php  
    include "koneksi.php";
    // PERLU DIPERHATIKAN !
    // FILE ARIMA.PHP DAN KOEFISIEN.PHP INCLUDE DI FILE FORECAST.PHP, ITU BERARTI FILE ARIMA.PHP DAN KOEFISIEN.PHP TERHUBUNG
    // SEHINGGA ARIMA.PHP BISA MENGGUNAKAN VARIABEL YANG ADA PADA KOEFISIEN.PHP

    // ============== ARIMA ==============
    // Langkah - langkah dari perhitungan ARIMA ini sebagai berikut :
    // LANGKAH A
    // 0. CEK NILAI P D Q
    // 1. DATA AKTUAL
    // 2. DEF1 / DIFERENSIASI
    // 3. DELTA YT
    // 4. FORECAST
    // 5. E
    // 6. E^2
    // 7. MAPE
    // SETELAH SEMUA NILAI DIATAS SUDAH DIDAPATKAN, MAKA SELANJUTNYA MENCARI DELTA YT DAN FORECAST 12 BULAN KEDEPAN, SERTA PERSENTASE MAPE
    // LANGKAH B
    // 1. DELTA YT 12 BULAN KEDEPAN
    // 2. FORECAST 12 BULAN KEDEPAN
    // 3. PERSENTASE MAPE
    // 4. VARIAN RESIDUAL
    // 5. LOG-LIKEHOOD
    // 6. AIC
    


    // LANGKAH A

    // 0. CEK NILAI P D Q
    $queryp = "SELECT nilai_p FROM tb_p WHERE id_p = 'A1'";
    $queryd = "SELECT nilai_d FROM tb_d WHERE id_d = 'A1'";
    $queryq = "SELECT nilai_q FROM tb_q WHERE id_q = 'A1'";

    // Variabel dibawah ini akan digunakan saat pengkondisian di delta yt
    $resultquery_p = mysqli_fetch_row(mysqli_query($koneksi, $queryp));
    $resultquery_d = mysqli_fetch_row(mysqli_query($koneksi, $queryd));
    $resultquery_q = mysqli_fetch_row(mysqli_query($koneksi, $queryq));

    // 1. DATA AKTUAL
    // Data aktual tidak perlu dicari lagi
    // Data aktual sudah dicari pada file koefisien.php dan bisa digunakan di dalam file ini karena pada file forecast.php, file ini dan file koefisien.php sama-sama di-includekan, sehingga bisa dibilang saat ini kedua file tersebut terhubung

    // 2. DEF1 / DIFERENSIASI
    // Data Diferensiasi ini juga tidak perlu dicari lagi, alasannya sama seperti data aktual

    // 3. DELTA YT
    // Pada DELTA YT ini dilakukan percabangan atau pengkondisian sesuai dengan nilai P D Q yang diinputkan
    $array_delta_yt = []; // variabel ini menyimpan semua delta yt
    $index_del = 0;
    while($index_del < $total_data_aktual){
        if($resultquery_p[0] == 1 && $resultquery_d[0] == 1 && $resultquery_q[0] == 1){
            if($index_del > 0){
                $hasil_delta_yt = ($nilai_slope_ar * $array_diferensiasi[$index_del - 1]) + ($nilai_slope_ma * $array_diferensiasi[$index_del - 1]);
                $array_delta_yt[] = $hasil_delta_yt;
    
                $index_del++;
            } else {
                $array_delta_yt[] = 0;
    
                $index_del++;
            }
        } elseif($resultquery_p[0] == 1 && $resultquery_d[0] == 1 && $resultquery_q[0] == 0){
            if($index_del > 0){
                $hasil_delta_yt = $nilai_slope_ar * $array_diferensiasi[$index_del - 1];
                $array_delta_yt[] = $hasil_delta_yt;
    
                $index_del++;
            } else {
                $array_delta_yt[] = 0;
    
                $index_del++;
            }
        } elseif($resultquery_p[0] == 0 && $resultquery_d[0] == 1 && $resultquery_q[0] == 1){
            if($index_del > 0){
                $hasil_delta_yt = $nilai_slope_ma * $array_diferensiasi[$index_del - 1];
                $array_delta_yt[] = $hasil_delta_yt;
    
                $index_del++;
            } else {
                $array_delta_yt[] = 0;
    
                $index_del++;
            }
        }
    }

    // 4. FORECAST
    $array_forecast = []; // variabel ini menyimpan semua nilai forecast
    $index_fc = 0;
    while($index_fc < $total_data_aktual){
        if($index_fc > 0){
            $hasil_forecast = $array_delta_yt[$index_fc] + $array_data_aktual[$index_fc];
            $array_forecast[] = $hasil_forecast;

            $index_fc++;
        } else {
            $array_forecast[] = 0;

            $index_fc++;
        }
    }

    // codingan dibawah dibuat agar nilai forecast tidak memiliki nilai dibelakang koma
    // karena jika menggunakan nilai forecast yang ada pada array_forecast diatas, nilai dibelkang koma sangat panjang
    // agar pada tampilan grafik angkanya tidak terlalu panjang, maka dibuatlah codingan dibawh ini.
    $array_forecast_less_decimal = array_map(function($num) {
        return round($num, 0); // round() berguna untuk membulatkan nilai dan angka 0 berarti membulatkan nilai agar tidak ada angka dibelakang koma
    }, $array_forecast);
    
    // 5. E
    $array_e = []; // variabel ini menyimpan semua nilai E
    $index_e = 0;
    while($index_e < $total_data_aktual){
        if($index_e > 0){
            $hasil_e = $array_data_aktual[$index_e] - $array_forecast[$index_e];
            $array_e[] = $hasil_e;

            $index_e++;
        } else {
            $array_e[] = 0;

            $index_e++;
        }
    }

    // 6. E^2
    $array_ekuadrat = []; // variabel ini menyimpan semua nilai E kuadrat
    $index_ekuadrat = 0;
    while($index_ekuadrat < count($array_e)){
        $hasil_ekuadrat = $array_e[$index_ekuadrat]**2;
        $array_ekuadrat[] = $hasil_ekuadrat;

        $index_ekuadrat++;
    }

    // 7. MAPE
    $array_mape = []; // variabel ini menyimpan semua nilai MAPE
    $index_mape = 0;
    while($index_mape < $total_data_aktual){
        $hasil_mape = abs(($array_data_aktual[$index_mape] - $array_forecast[$index_mape]) / $array_data_aktual[$index_mape]);
        $array_mape[] = $hasil_mape;

        $index_mape++;
    }


    // LANGKAH B
    // 1. DELTA YT 12 BULAN KEDEPAN
    // Pada DELTA YT 2 ini dilakukan percabangan atau pengkondisian sesuai dengan nilai P D Q yang diinputkan
    $array_delta_yt_2 = []; // variabel ini menyimpan semua delta yt 2
    $index_del_2 = 0;
    while($index_del_2 < 12){
        if($resultquery_p[0] == 1 && $resultquery_d[0] == 1 && $resultquery_q[0] == 1){
            if($index_del_2 > 0){
                $hasil_delta_yt_2 = ($nilai_slope_ar * $array_delta_yt_2[$index_del_2 - 1]) + ($nilai_slope_ma * $array_delta_yt_2[$index_del_2 - 1]);
                $array_delta_yt_2[] = $hasil_delta_yt_2;
    
                $index_del_2++;
            } else {
                $hasil_delta_yt_2 = ($nilai_slope_ar * $array_delta_yt[count($array_delta_yt) - 1]) + ($nilai_slope_ma * $array_delta_yt[count($array_delta_yt) - 1]);
                $array_delta_yt_2[] = $hasil_delta_yt_2;
    
                $index_del_2++;
            }
        } elseif ($resultquery_p[0] == 1 && $resultquery_d[0] == 1 && $resultquery_q[0] == 0){
            if($index_del_2 > 0){
                $hasil_delta_yt_2 = $nilai_slope_ar * $array_delta_yt_2[$index_del_2 - 1];
                $array_delta_yt_2[] = $hasil_delta_yt_2;
    
                $index_del_2++;
            } else {
                $hasil_delta_yt_2 = $nilai_slope_ar * $array_delta_yt[count($array_delta_yt) - 1];
                $array_delta_yt_2[] = $hasil_delta_yt_2;
    
                $index_del_2++;
            }
        } elseif (($resultquery_p[0] == 0 && $resultquery_d[0] == 1 && $resultquery_q[0] == 1)){
            if($index_del_2 > 0){
                $hasil_delta_yt_2 = $nilai_slope_ma * $array_delta_yt_2[$index_del_2 - 1];
                $array_delta_yt_2[] = $hasil_delta_yt_2;
    
                $index_del_2++;
            } else {
                $hasil_delta_yt_2 = $nilai_slope_ma * $array_delta_yt[count($array_delta_yt) - 1];
                $array_delta_yt_2[] = $hasil_delta_yt_2;
    
                $index_del_2++;
            }
        }
    }

    // 2. FORECAST 12 BULAN KEDEPAN
    $array_forecast_2 = []; // variabel ini menyimpan semua nilai forecast 2
    $index_fc_2 = 0;
    while($index_fc_2 < 12){
        if($index_fc_2 > 0){
            $hasil_forecast_2 = $array_delta_yt_2[$index_fc_2] + $array_forecast_2[$index_fc_2 - 1];
            $array_forecast_2[] = $hasil_forecast_2;

            $index_fc_2++;
        } else {
            $hasil_forecast_2 = $array_delta_yt_2[$index_fc_2] + $array_data_aktual[count($array_data_aktual) - 1];
            $array_forecast_2[] = $hasil_forecast_2;

            $index_fc_2++;
        }
    }

    // codingan dibawah dibuat agar nilai forecast 2 tidak memiliki nilai dibelakang koma
    // karena jika menggunakan nilai forecast yang ada pada array_forecast_2 diatas, nilai dibelkang koma sangat panjang
    // agar pada tampilan grafik angkanya tidak terlalu panjang, maka dibuatlah codingan dibawh ini.
    $array_forecast_2_less_decimal = array_map(function($num) {
        return round($num, 0); // round() berguna untuk membulatkan nilai dan angka 0 berarti membulatkan nilai agar tidak ada angka dibelakang koma
    }, $array_forecast_2);

    // 3. PERSENTASE MAPE
    $total_mape = array_sum($array_mape);
    $hasil_mape = ($total_mape / count($array_mape)) * 100;

    // 4. VARIAN RESIDUAL
    $total_ekuadrat = array_sum($array_ekuadrat);
    $hasil_varian_residual = $total_ekuadrat / count($array_ekuadrat);
    
    // 5. LOG-LIKEHOOD
    // Rumus pada excel adalah =-K5/2*LN(2*PI())-K5/2*LN(K6)-H99/(2*K6)
    $hasil_log_lh = (-(count($array_data_aktual)) / 2 * log(2*pi())) - (count($array_data_aktual) / 2 * log($hasil_varian_residual)) - ($total_ekuadrat / (2 * $hasil_varian_residual));

    // 6. AIC
    $hasil_aic = (2 * 2) - (2 * $hasil_log_lh);

?>