<!DOCTYPE html>

<html lang="es">
<head>
    <title>Racing Game</title>
  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<?
$page = isset($_REQUEST['page']) ? htmlspecialchars($_REQUEST['page']) : '';
SWITCH ( $page ) {
    case 'admin':                       include "includes/admin.php";;
    case 'main':                        include "includes/main.php";;						break;
    default:                            include "includes/main.php";;						break;
}
?>