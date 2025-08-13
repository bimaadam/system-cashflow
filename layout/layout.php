<?php
require_once __DIR__ . '/../function.php';
require_once __DIR__ . '/../cek.php';
require_login();

$title = $title ?? 'Dashboard - Admin Dekorasi';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<title><?= htmlspecialchars($title) ?></title>
	<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
	<link href="css/styles.css" rel="stylesheet" />
	<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
	<?php if (!empty($head_extra)) echo $head_extra; ?>
</head>
<body class="sb-nav-fixed">
	<?php require __DIR__ . '/navbar.php'; ?>
	<div id="layoutSidenav">
		<?php require __DIR__ . '/sidebar.php'; ?>
		<div id="layoutSidenav_content">
			<main class="container-fluid px-4 mt-4">
				<?php if (!empty($content)) echo $content; ?>
			</main>
			<footer class="py-4 bg-light mt-auto">
				<div class="container-fluid text-center">
					<small>&copy; <?= date('Y') ?> Admin Dekorasi</small>
				</div>
			</footer>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="js/scripts.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
	<script src="assets/demo/chart-area-demo.js"></script>
	<script src="assets/demo/chart-bar-demo.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
	<?php if (!empty($scripts_extra)) echo $scripts_extra; ?>
</body>
</html>

