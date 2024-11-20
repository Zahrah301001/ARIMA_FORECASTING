<?php

session_start();

if (!isset($_SESSION["username"])) {
	header("Location: login.php");
}

include_once("koneksi.php");

$querytampil = "Select * from tb_data";

if (isset($_GET['pesan_sukses'])) {
	$pesan_sukses = $_GET['pesan_sukses'];
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
	
	<title>Input Data</title>

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
      <div class="dashboard-parent text-white" style="position: fixed; overflow: auto; height: 100vh; background-color: #4682B4;">
        <div class="dashboard px-4">
			<h2 class="fw-bold mt-4" style="margin-bottom: 25px; color: white;">Upload Data</h2>
			<hr>
			<?php
			if (isset($pesan_sukses)) {
				echo "<div class='alert alert-success d-flex justify-content-between align-items-center'>
				<div><span class='fw-bold'>Sukses! </span>". $pesan_sukses ."</div>
				<button class='alert-dismissible border border-0 p-0 bg-transparent' data-bs-dismiss='alert' aria-label='close'>
				<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='currentColor' class='bi bi-x' viewBox='0 0 16 16'>
				<path d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708'/>
				</svg>
				</button>
				</div>";
			}	
			?>
			<div style="margin-bottom: 20px;">
				<a class="btn btn-primary" href="tambah.php"><i class="bi bi-plus"></i> Tambah Data</a>
			</div>
			<table class="table table-bordered bg-dark">
				<tr style="background-color: #23AAE1; color: #FFFFFF;">
					<th>Bulan - Tahun</th>
					<th>Data Aktual</th>
					<th style="width: 10%;">Aksi</th>
				</tr>
				<?php
				$resultquery = mysqli_query($koneksi,$querytampil);

				If(!$resultquery){
					die("Query Error : ".mysqli_errno($koneksi)." - ".mysqli_error($koneksi));
				}

				while ($data = mysqli_fetch_assoc($resultquery)) {
					?>
					<tr style="color: #FFF200;">
						<?php
						echo "<td>$data[bln_thn]</td>";
						echo "<td>$data[d_aktual]</td>";
						?>
						<td class="d-flex">
							<form action="edit.php" method="post">
								<input type="hidden" name="id" value="<?php echo "$data[id]"; ?>">
								<button class="btn btn-warning me-2" name="submit" value="Edit"><i class="bi bi-pencil-fill"></i></button>
							</form>
					
							<form action="hapus.php" method="post">
								<input type="hidden" name="id" value="<?php echo "$data[id]"; ?>">
								<input type="hidden" name="bln_thn" value="<?php echo "$data[bln_thn]"; ?>">
								<button class="btn btn-danger" name="submit" value="Hapus"><i class="bi bi-trash-fill"></i></button>
							</form>
						</td>
					</tr>
					<?php

				}

				mysqli_free_result($resultquery);
				mysqli_close($koneksi);
				?>
			</table>
        </div>
      </div>
    </div>

  </div>

  <!-- BOOTSTRAP JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

</body>
</html>