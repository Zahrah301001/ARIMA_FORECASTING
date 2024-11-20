<?php

include_once("koneksi.php");

$bulan = "";
$tahun = "";
$data_aktual = "";
$pesan_error = array();

	// Check If form submitted, insert form data into users table.
if(isset($_POST["submit"])) {
	$bulan = htmlentities(strip_tags(trim($_POST['bulan'])));
	$tahun = htmlentities(strip_tags(trim($_POST['tahun'])));
	$data_aktual = htmlentities(strip_tags(trim($_POST['d_aktual'])));

	$pesan_error = array();

	if ($bulan === "Bulan") {
		$pesan_error[]= "Bulan belum dipilih!";
	}

	if ($tahun === "Tahun") {
		$pesan_error[]= "Tahun belum dipilih!"; 
	}

	if (empty($data_aktual)) {
		$pesan_error[]= "Data aktual belum di isi!";
	}

	$bln_thn = $bulan." ".$tahun;


	if (!$pesan_error) {
		$bln_thn = mysqli_real_escape_string($koneksi,$bln_thn);
		$data_aktual = mysqli_real_escape_string($koneksi,$data_aktual);

		$querytambah = "INSERT INTO tb_data(bln_thn,d_aktual) ";
		$querytambah .= "VALUES('$bln_thn','$data_aktual')";

		$resultquery = mysqli_query($koneksi,$querytambah);

		if ($resultquery) {
			$pesan_sukses = "Data penjualan bulan \"<b>$bln_thn</b>\" berhasil ditambahkan!";
			$pesan_sukses = urlencode($pesan_sukses);
			header("Location: data.php?pesan_sukses={$pesan_sukses}");
		}
		else {
			die("Query gagal dijalankan: ".mysqli_errno($koneksi)." - ".mysqli_error($koneksi));
		}
		mysqli_free_result($resultquery);
		mysqli_close($koneksi);
	}
}

?>

<!DOCTYPE html>
<html lang="en" >
<head>
	<meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	
	<title>Tambah Data Penjualan (KWH)</title>

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
      <div class="dashboard-parent text-white" style="height: 100vh; background-color: #4682B4;">
        <div class="dashboard px-4">
				<h2 class="fw-bold mt-4" style="margin-bottom: 25px;">Tambah Data Penjualan</h2>
				<hr>
				<?php
				if ($pesan_error !=="") {
					foreach ($pesan_error as $per) {
						echo "<div class='alert alert-danger alert-dismissible'>
						<a href='#'' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
						<strong>Gagal!</strong> ".$per.
						"</div>";
					}
				}
				?>
				<form action="tambah.php" method="post" name="form1">
					<div class="row">
					<div class="form-group col mb-4">
						<label for="bulan">Bulan</label>
						<select name="bulan" class="form-control">
							<option selected="selected">Bulan</option>
							<?php
							$bulan=array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
							$jlh_bln=count($bulan);
							for($c=0; $c<$jlh_bln; $c+=1){
								echo"<option value=$bulan[$c]> $bulan[$c] </option>";
							}
							?>
						</select>
					</div>
					<div class="form-group col">
						<label for="tahun">Tahun</label>
						<?php
						$now=date("Y");
						echo "<select name='tahun' class='form-control'>
						<option>Tahun</option>";
						for ($a=2012;$a<=$now;$a++)
						{
							echo "<option value='$a'>$a</option>";
						}
						echo "</select>";
						?>
					</div>
				</div>
					<div class="form-group">
						<label for="d_aktual">Data Aktual</label>
						<input class="form-control" type="text" name="d_aktual">
					</div>
					<div style="display: flex;">
						<div style="margin-top:20px;">
							<input class="btn btn-primary" type="submit" name="submit" value="Simpan">
						</div>
						<div style="margin: 20px 0px 0px 20px;">
							<a class="btn btn-danger" href="data.php">Batal</a>
						</div>
					</div>
				</form>
        </div>
      </div>
    </div>

  </div>

  <!-- BOOTSTRAP JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>
</html>