<?php

session_start();

if (!isset($_SESSION["username"])) {
	header("Location: login.php");
}

// Untuk menghubungkan dengan file koefisien.php agar hasil perhitungan bisa digunakan di file ini.
include "koefisien.php";
// Untuk menghubungkan dengan file arima.php agar hasil perhitungan bisa digunakan di file ini.
include "arima.php";

$querytampil = "SELECT * FROM tb_data";
$queryp = "SELECT * FROM tb_p WHERE id_p = 'A1'";
$queryd = "SELECT * FROM tb_d WHERE id_d = 'A1'";
$queryq = "SELECT * FROM tb_q WHERE id_q = 'A1'";
$pesan_error = "";

if (isset($_GET['pesan_sukses'])) {
	$pesan_sukses = $_GET['pesan_sukses'];
}

if (isset($_POST["submit"])) {
	$pesan_error = "";
	switch ($_POST["submit"]) {
		case "p":
			$id_p = htmlentities(strip_tags(trim($_POST['id_p'])));
			$n_p = htmlentities(strip_tags(trim($_POST['nilai_p'])));
			if (!isset($n_p) || strlen($n_p) === 0) {
				$pesan_error = "Nilai p belum di isi!";
			}
			if ($pesan_error === "") {
				$id_p = mysqli_real_escape_string($koneksi, $id_p);
				$n_p = mysqli_real_escape_string($koneksi, $n_p);
				$queryupdatep = "UPDATE tb_p SET nilai_p = '$n_p' WHERE id_p = '$id_p'";
				$resultquery = mysqli_query($koneksi, $queryupdatep);
				if ($resultquery) {
					$pesan_sukses = "p berhasil diupdate!<br>";
					$pesan_sukses = urlencode($pesan_sukses);
					header("Location: forecast.php?pesan_sukses={$pesan_sukses}");
				} else {
					die("Query gagal dijalankan: " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi));
				}
				mysqli_free_result($resultquery);
			}
			break;
		case "d":
			$id_d = htmlentities(strip_tags(trim($_POST['id_d'])));
			$n_d = htmlentities(strip_tags(trim($_POST['nilai_d'])));
			if (!isset($n_d) || strlen($n_d) === 0) {
				$pesan_error = "Nilai d belum di isi!";
			}
			if ($pesan_error === "") {
				$id_d = mysqli_real_escape_string($koneksi, $id_d);
				$n_d = mysqli_real_escape_string($koneksi, $n_d);
				$queryupdated = "UPDATE tb_d SET nilai_d = '$n_d' WHERE id_d = '$id_d'";
				$resultquery = mysqli_query($koneksi, $queryupdated);
				if ($resultquery) {
					$pesan_sukses = "d berhasil diupdate!<br>";
					$pesan_sukses = urlencode($pesan_sukses);
					header("Location: forecast.php?pesan_sukses={$pesan_sukses}");
				} else {
					die("Query gagal dijalankan: " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi));
				}
				mysqli_free_result($resultquery);
			}
			break;
		case "q":
			$id_q = htmlentities(strip_tags(trim($_POST['id_q'])));
			$n_q = htmlentities(strip_tags(trim($_POST['nilai_q'])));
			if (!isset($n_q) || strlen($n_q) === 0) {
				$pesan_error = "Nilai q belum di isi!";
			}
			if ($pesan_error === "") {
				$id_q = mysqli_real_escape_string($koneksi, $id_q);
				$n_q = mysqli_real_escape_string($koneksi, $n_q);
				$queryupdateq = "UPDATE tb_q SET nilai_q = '$n_q' WHERE id_q = '$id_q'";
				$resultquery = mysqli_query($koneksi, $queryupdateq);
				if ($resultquery) {
					$pesan_sukses = "q berhasil diupdate!<br>";
					$pesan_sukses = urlencode($pesan_sukses);
					header("Location: forecast.php?pesan_sukses={$pesan_sukses}");
				} else {
					die("Query gagal dijalankan: " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi));
				}
				mysqli_free_result($resultquery);
			}
			break;
	}
}

$nama_bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

// Fungsi untuk mendapatkan 12 bulan ke depan
function get12BulanKedepan($bulan_awal, $tahun_awal) {
    global $nama_bulan;

    // Cari indeks bulan berdasarkan nama
    $bulan_awal = array_search($bulan_awal, $nama_bulan);
    if ($bulan_awal === false) {
        return "Bulan tidak valid.";
    }

    $bulan_tahun = [];
    $bulan = $bulan_awal;
    $tahun = $tahun_awal;

    for ($i = 0; $i < 12; $i++) {
        $bulan++;
        if ($bulan > 12) {
            $bulan = 1;
            $tahun++;
        }
        $bulan_tahun[] = $nama_bulan[$bulan] . ' ' . $tahun;
    }

    return $bulan_tahun;
}

$query_bulan_tahun = mysqli_query($koneksi, "SELECT bln_thn FROM tb_data ORDER BY id DESC LIMIT 1");
$result_bulan_tahun = mysqli_fetch_assoc($query_bulan_tahun);

// Memisahkan bulan dan tahun, karena pada database bulan dan tahun gabung
$bulan_tahun = explode(" ", $result_bulan_tahun['bln_thn']);

$bulan_terakhir = $bulan_tahun[0];
$tahun_terakhir = $bulan_tahun[1];

$bulan_tahun_terbaru = get12BulanKedepan($bulan_terakhir, $tahun_terakhir);

// Merge array untuk digunakan di grafik
// 1. Array bulan tahun dari db dengan bulan tahun terbaru
$query_periode = mysqli_query($koneksi, "SELECT bln_thn FROM tb_data");
$result_periode = [];
while($row_periode = mysqli_fetch_row($query_periode)){
	$result_periode[] = $row_periode[0];
}

$merge_periode = array_merge($result_periode, $bulan_tahun_terbaru);

// 2. Array forecast dengan array forecast 2
$merge_forecast = array_merge($array_forecast_less_decimal, $array_forecast_2_less_decimal);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

	<title>Forecasting</title>

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
	<link rel="stylesheet" href="css/style.css">

</head>
<body>
	<div class="fluid-container">

		<div style="display: flex;">
			<div>
			<?php include "sidebar.php" ?>
			</div>
			<div class="dashboard-parent text-white" style="position: fixed; overflow: auto; height: 100vh; background-color:#4682B4;">
				<div class="col-12 dashboard px-4">
					<div class="jumbotron">
						<h2 class="fw-bold" style="margin-bottom: 25px;">HASIL PERAMALAN</h2>
						<hr>
						<div style="margin-bottom: 10px;">
							<?php
							if ($pesan_error !== "") {
								echo "<div class='alert alert-danger d-flex justify-content-between align-items-center'>
								<div><span class='fw-bold'>Gagal! </span>". $pesan_error ."</div>
								<button class='alert-dismissible border border-0 p-0 bg-transparent' data-bs-dismiss='alert' aria-label='close'>
								<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' class='bi bi-x' viewBox='0 0 16 16'>
								<path d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708'/>
								</svg>
								</button>
								</div>";
							}

							if (isset($pesan_sukses)) {
								echo "<div class='alert alert-success d-flex justify-content-between align-items-center'>
								<div><span class='fw-bold'>Berhasil! </span>". $pesan_sukses ."</div>
								<button class='alert-dismissible border border-0 p-0 bg-transparent' data-bs-dismiss='alert' aria-label='close'>
								<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' class='bi bi-x' viewBox='0 0 16 16'>
								<path d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708'/>
								</svg>
								</button>
								</div>";
							}

							$resultquery = mysqli_query($koneksi, $queryp);
							if (!$resultquery) {
								die("Query Error : " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi));
							}
							$data_p = mysqli_fetch_assoc($resultquery);

							$resultquery_d = mysqli_query($koneksi, $queryd);
							if (!$resultquery_d) {
								die("Query Error : " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi));
							}
							$data_d = mysqli_fetch_assoc($resultquery_d);

							$resultquery_q = mysqli_query($koneksi, $queryq);
							if (!$resultquery_q) {
								die("Query Error : " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi));
							}
							$data_q = mysqli_fetch_assoc($resultquery_q);
							?>

							<div class="row">
								<div class="col-sm-5">
									<h6 class="alert alert-info">Model ARIMA (p, d, q) saat ini adalah : [<b><?php echo $data_p['nilai_p']; ?>, <?php echo $data_d['nilai_d']; ?>, <?php echo $data_q['nilai_q']; ?></b>]</h6>
								</div>
								<div class="col-sm-7">
									<form action="forecast.php" method="post" name="ubah_p">
										<div class="form-check form-check-inline d-flex">
											<input class="form-control" type="text" name="nilai_p" placeholder="Ganti Nilai p" style="width:244px;">
											<input type="hidden" name="id_p" value="<?php echo $data_p['id_p']; ?>">
											<input class="btn btn-primary" type="submit" name="submit" value="p" style="margin-left:10px;">
										</div>
									</form>
									<form action="forecast.php" method="post" name="ubah_d">
										<div class="form-check form-check-inline d-flex">
											<input class="form-control" type="text" name="nilai_d" placeholder="Ganti Nilai d" style="width:244px;">
											<input type="hidden" name="id_d" value="<?php echo $data_d['id_d']; ?>">
											<input class="btn btn-primary" type="submit" name="submit" value="d" style="margin-left:10px;">
										</div>
									</form>
									<form action="forecast.php" method="post" name="ubah_q">
										<div class="form-check form-check-inline d-flex">
											<input class="form-control" type="text" name="nilai_q" placeholder="Ganti Nilai q" style="width:244px;">
											<input type="hidden" name="id_q" value="<?php echo $data_q['id_q']; ?>">
											<input class="btn btn-primary" type="submit" name="submit" value="q" style="margin-left:10px;">
										</div>
									</form>
								</div>
							</div>
						</div>
						<?php
						$id_p = $data_p["id_p"];
						$n_p = $data_p["nilai_p"];
						?>
					</div>
				
					<div class="row">
						<div class="col-sm-12">
							<table class="table table-bordered bg-dark">
								<tr style="background-color: #23AAE1; color: #FFFFFF; padding-top: 1000px;">
									<th>Bulan - Tahun</th>
									<th>Hasil Forecasting</th>
								</tr>
								<?php $index = 0; ?>
								<?php while($index < count($array_forecast_2)){ ?>
									<tr style="color: #FFFFFF;">
										<td><?php echo $bulan_tahun_terbaru[$index]; ?></td>
										<td class="text-warning fw-semibold"><?php echo round($array_forecast_2[$index], 0); ?></td>
									</tr>
									<?php $index++; ?>
								<?php } ?>
							</table>
							<div class="d-flex justify-content-between my-3">
								<div class="pb-2 col-sm-3 alert alert-warning">
									<div class="d-flex justify-content-center">
										<h5>MAPE : </h5>
										<h5 class="ms-2 fw-bold"><?php echo number_format($hasil_mape, 3, ',', ''); ?>%</h5>
									</div>
								</div>
								<div class="pb-2 col-sm-3 alert alert-warning">
									<div class="d-flex justify-content-center">
										<h5>AIC : </h5>
										<h5 class="ms-2 fw-bold"><?php echo number_format($hasil_aic, 3, ',', ''); ?></h5>
									</div>
								</div>
							</div>
							<h2 class="fw-bold" style="margin-bottom: 25px; margin-top: 20px;">Grafik Perbandingan Data Aktual dan Forecasting</h2>
							<hr>

							<!-- Grafik -->
							<canvas id="forecastChart" width="400" height="150" class="mb-5"></canvas>
						</div>
					</div>
				</div>
			</div>
    	</div>

	</div>

  <!-- BOOTSTRAP JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

  <script>
        var ctx = document.getElementById('forecastChart').getContext('2d');
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($merge_periode); ?>,
                datasets: [
                    {
                        label: 'Data Aktual',
                        data: <?php echo json_encode($array_data_aktual); ?>,
                        fill: false,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Forecasting',
                        data: <?php echo json_encode($merge_forecast); ?>,
                        fill: false,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan - Tahun',
                            color: 'black',
                            font: {
                                size: 14
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah',
                            color: 'black',
                            font: {
                                size: 14
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: 'black'
                        }
                    },
                    title: {
                        display: true,
                        text: 'Grafik Forecasting',
                        color: 'black',
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });

        ctx.canvas.style.backgroundColor = 'white';
    </script>

</body>
</html>