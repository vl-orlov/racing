<!DOCTYPE html>
<html lang="es-AR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Racing Gaming · Elegí jugar a lo Racing</title>
    <meta name="description" content="Racing Gaming — Indumentaria, mochilas y accesorios oficiales del primer club gaming argentino. Cápsula 2026." />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bebas+Neue&family=Inter:wght@400;500;600;700;800&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="img/logo.svg">
    <link rel="shortcut icon" type="image/png" href="img/logo.svg">
    <link rel="apple-touch-icon" href="img/logo.svg">
</head>
<body>

<?php
include 'includes/functions.php';
try {
    DBconnect();
    global $link;
    $query = "SELECT id, name, description, price, category, image_path FROM products ORDER BY id DESC";
    $result = mysqli_query($link, $query);
    $products = [];
    if ($result) {
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $products[] = $row;
        }
    }
} catch (Exception $e) {
    $products = [];
}

$page = isset($_REQUEST['page']) ? htmlspecialchars($_REQUEST['page']) : '';
switch ($page) {
    case 'main':
    default:
        include 'includes/main.php';
        break;
}
?>

</body>
</html>
