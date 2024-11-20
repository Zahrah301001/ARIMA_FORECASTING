<?php 
	// Untuk menghubungkan dengan file perhitungan_preprocessing.php agar hasil perhitungan bisa digunakan di file ini.
	include "perhitungan_preprocessing.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="js/chart.min.js"></script>

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
			<div class="dashboard-parent text-white" style="position: fixed; overflow: auto; height: 100vh; background-color: #4682B4;">
        		<div class="dashboard px-4">
					<div>
						<h3 class="fw-bold" style="margin-bottom: 25px;">HASIL PLOT ACF PACF</h3>
						<hr>
					</div>
					<div style="display: flex; flex-direction: column; gap: 1rem;">
						<canvas id="acf" width="400" height="104"></canvas>
						<canvas id="pacf" width="400" height="104"></canvas>
					</div>
				</div>
			</div>
						

	<!-- BOOTSTRAP JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
	<script>
	// CHART ACF
	var ctxAcf = document.getElementById('acf').getContext('2d');
	var acfChart = new Chart(ctxAcf, {
		type: 'bar', // Base type
		data: {
		labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
		datasets: [
			{
			label: 'ACF',
			data: <?php echo json_encode($array_acf_less_decimal); ?>,
			backgroundColor: 'rgba(255, 159, 64, 0.2)',
			borderColor: 'rgba(255, 159, 64, 1)',
			borderWidth: 2
			},
			{
			label: 'G+',
			data: <?php echo json_encode($array_gplus_acf); ?>,
			type: 'line', // Specify this dataset as a line chart
			borderColor: 'rgba(75, 192, 192, 1)',
			borderWidth: 2,
			fill: false
			},
			{
			label: 'G-',
			data: <?php echo json_encode($array_gminus_acf); ?>,
			type: 'line', // Specify this dataset as a line chart
			borderColor: 'rgba(153, 102, 255, 1)',
			borderWidth: 2,
			fill: false
			},
		]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				}
			},
			plugins: {
				legend: {
					labels: {
						color: 'black'
					}
				}
			},
			title: {
                display: true,
                text: 'Grafik ACF',
                fontColor: 'black',
                fontSize: 16
            }
		}
	});

	ctxAcf.canvas.style.backgroundColor = 'white';

	// CHART PACF
	var ctxPacf = document.getElementById('pacf').getContext('2d');
	var pacfChart = new Chart(ctxPacf, {
		type: 'bar', // Base type
		data: {
		labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
		datasets: [
			{
			label: 'PACF',
			data: <?php echo json_encode($array_pacf_less_decimal); ?>,
			backgroundColor: 'rgba(255, 159, 64, 0.2)',
			borderColor: 'rgba(255, 159, 64, 1)',
			borderWidth: 2
			},
			{
			label: 'G+',
			data: <?php echo json_encode($array_gplus_pacf); ?>,
			type: 'line', // Specify this dataset as a line chart
			borderColor: 'rgba(75, 192, 192, 1)',
			borderWidth: 2,
			fill: false
			},
			{
			label: 'G-',
			data: <?php echo json_encode($array_gminus_pacf); ?>,
			type: 'line', // Specify this dataset as a line chart
			borderColor: 'rgba(153, 102, 255, 1)',
			borderWidth: 2,
			fill: false
			},
		]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true
				}
			},
			plugins: {
				legend: {
					labels: {
						color: 'black'
					}
				}
			},
			title: {
                display: true,
                text: 'Grafik PACF',
                fontColor: 'black',
                fontSize: 16
            }
		}
	});

	ctxPacf.canvas.style.backgroundColor = 'white';
	</script>

</body>
</html>