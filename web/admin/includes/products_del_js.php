<?php
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
set_time_limit(0);

include __DIR__ . '/path_helper.php';
include getIncludesFilePath('functions.php');

try {
    DBconnect();
    global $link;
    if (!isset($link) || !$link) {
        throw new Exception("Variable \$link no está disponible después de DBconnect()");
    }
} catch (Exception $e) {
    if (ob_get_level() > 0) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(["ok" => 0, "err" => "Error de conexión: " . $e->getMessage()]);
    exit;
}

$ok = 0;
$res = '';

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!isset($input['id']) || !is_numeric($input['id'])) {
    if (ob_get_level() > 0) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(["ok" => 0, "err" => "ID no válido"]);
    exit;
}

$id = intval($input['id']);

try {
    global $link;
    
    // Получаем путь к изображению перед удалением
    $query = "SELECT image_path FROM products WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    // Удаляем товар
    $query = "DELETE FROM products WHERE id = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error al eliminar: " . mysqli_stmt_error($stmt));
    }
    
    $ok = 1;
    
    // Удаляем файл изображения если он существует
    if ($product && !empty($product['image_path'])) {
        $imagePath = __DIR__ . '/../../uploads/' . $product['image_path'];
        if (file_exists($imagePath)) {
            @unlink($imagePath);
        }
    }
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    $res = $e->getMessage();
} catch (Error $e) {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    $res = "Fatal error: " . $e->getMessage();
}

if (ob_get_level() > 0) {
    ob_clean();
}
header('Content-Type: application/json');
echo json_encode([
    "ok" => $ok,
    "res" => $res
]);
exit;
?>

