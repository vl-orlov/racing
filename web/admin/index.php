<?
session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
set_time_limit(0);
error_reporting(E_ALL);

include __DIR__ . '/includes/path_helper.php';
include getIncludesFilePath('functions.php');
DBconnect();

$basePath = getAdminBasePath();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
	header('Location: ' . $basePath . 'login.php');
    exit();
}

$page = isset($_REQUEST['page']) ? htmlspecialchars($_REQUEST['page']) : '';
if ($page == 'logout') {
    include __DIR__ . '/includes/logout.php';
    exit();
}
?>

<!DOCTYPE html>
<html lang="es-AR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Racing Gaming · Admin</title>
    <link rel="icon" type="image/png" href="<?= $basePath ?>img/logo.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Rajdhani:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $basePath ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="<?= $basePath ?>css/style.css" rel="stylesheet">
    <link href="<?= $basePath ?>css/styleA.css?v=2.0" rel="stylesheet">
</head>
<body id="page-top">

<div id="debug"></div>
<div id="wrapper">

    <?php include 'includes/nav.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include 'includes/topbar.php'; ?>

            <div class="container-fluid px-4 py-3">
                <?php include __DIR__ . '/includes/products.php'; ?>
            </div>

        </div>
    </div>

</div>

<script src="<?= $basePath ?>vendor/jquery/jquery.min.js"></script>
<script src="<?= $basePath ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $basePath ?>vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="<?= $basePath ?>js/sb-admin-2.min.js"></script>

</body>
</html>

