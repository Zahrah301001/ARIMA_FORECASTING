<?php

session_start();

if (!isset($_SESSION["username"])) {
  header("Location: login.php");
}

include_once("koneksi.php");

if (isset($_POST["submit"])) {
	if ($_POST["submit"]=="Edit") {

		$id = htmlentities(strip_tags(trim($_POST["id"])));
		$id = mysqli_real_escape_string($koneksi,$id);

		$querytampilid = "select * from tb_data where id ='$id'";
		$resultquery = mysqli_query($koneksi,$querytampilid);

		if (!$resultquery) {
			die("Query Error: ".mysqli_errno($koneksi)." - ".mysqli_error($koneksi));
		}

		$data = mysqli_fetch_assoc($resultquery);

		$id=$data["id"];

		$bln_thn = $data["bln_thn"];
		$pecah = explode(" ", $bln_thn);
		$bulan = $pecah[0];
		$tahun = $pecah[1];

		$d_aktual = $data["d_aktual"];
		mysqli_free_result($resultquery);

	}

	else if ($_POST["submit"]=="Update Data") {

		$id = htmlentities(strip_tags(trim($_POST["id"])));
		$bulan = htmlentities(strip_tags(trim($_POST["bulan"])));
		$tahun = htmlentities(strip_tags(trim($_POST["tahun"])));
		$d_aktual = htmlentities(strip_tags(trim($_POST["d_aktual"])));

	}

	$pesan_error = array();

	if ($bulan === "Bulan") {
		$pesan_error[]= "Bulan belum dipilih!";
	}

	if ($tahun === "Tahun") {
		$pesan_error[]= "Tahun belum dipilih!"; 
	}

	if (empty($d_aktual)) {
		$pesan_error[]= "Data aktual belum di isi!";
	}


	if ((!$pesan_error) AND ($_POST["submit"]=="Update Data")) {

		$id = mysqli_real_escape_string($koneksi,$id);
		$bulan = mysqli_real_escape_string ($koneksi,$bulan);
		$tahun = mysqli_real_escape_string ($koneksi,$tahun);
		$bln_thn = $bulan." ".$tahun;
		$d_aktual = mysqli_real_escape_string($koneksi,$d_aktual);

		$queryupdate = "update tb_data set ";
		$queryupdate .= "bln_thn = '$bln_thn', d_aktual = '$d_aktual' ";
		$queryupdate .= "where id = '$id'";

		$resultquery = mysqli_query($koneksi,$queryupdate);

		if ($resultquery) {
			$pesan_sukses = "Data penjualan bulan \"<b>$bln_thn</b>\" berhasil diupdate!";
			$pesan_sukses = urlencode($pesan_sukses);
			header("Location: data.php?pesan_sukses={$pesan_sukses}");
		}
		else {
			die("Query gagal dijalankan: ".mysqli_errno($koneksi)." - ".mysqli_error($koneksi));
		}
		mysqli_free_result($resultquery);
	}
	mysqli_close($koneksi);
}
else {
	header("Location: data.php");
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
	
	<title>Edit data</title>

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
				<h2 class="fw-bold mt-4" style="margin-bottom: 25px;">Edit data</h2>
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
				<form action="edit.php" method="post" name="update_data">
					<div>
						<input type="hidden" name="id" value="<?php echo $id;?>">
					</div>
					<div class="row">
					<div class="form-group col mb-4">
						<label for="bulan">Bulan</label>
						<select name="bulan" class="form-control">
							<option selected="selected"><?php echo $bulan;?></option>
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
						echo "<select name='tahun' class='form-control' >
						<option>$tahun</option>";
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
						<input class="form-control" type="text" name="d_aktual" value="<?php echo $d_aktual;?>">
					</div>
					<div style="display: flex;">
						<div style="margin-top:20px;">
							<input class="btn btn-primary" type="submit" name="submit" value="Update Data">
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