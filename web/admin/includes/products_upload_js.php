<?
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
    echo json_encode(['ok' => 0, 'err' => 'Error de conexión: ' . $e->getMessage(), 'file_path' => '']);
    exit;
}

$return = ['ok' => 0, 'err' => '', 'file_path' => ''];

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    if (ob_get_level() > 0) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => 0, 'err' => 'Error al subir el archivo']);
    exit;
}

if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    if (ob_get_level() > 0) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => 0, 'err' => 'product_id requerido']);
    exit;
}

$productId = intval($_POST['product_id']);

try {
    global $link;
    
    // Проверяем, существует ли товар
    $query = "SELECT id FROM products WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'i', $productId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$product) {
        throw new Exception('Producto no encontrado');
    }
    
    $file = $_FILES['file'];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    // Проверка типа файла
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Tipo de archivo no permitido. Solo se permiten imágenes (JPEG, PNG, GIF)');
    }
    
    // Проверка размера
    if ($file['size'] > $maxSize) {
        throw new Exception('El archivo es demasiado grande. Máximo 5MB');
    }
    
    // Создаем директорию uploads если не существует
    $uploadsDir = __DIR__ . '/../../uploads/';
    if (!is_dir($uploadsDir)) {
        if (!mkdir($uploadsDir, 0755, true)) {
            throw new Exception('No se pudo crear el directorio de uploads');
        }
    }
    
    // Генерируем имя файла: products_1.jpg, products_2.jpg и т.д.
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'products_' . $productId . '.' . $extension;
    $filePath = $uploadsDir . $fileName;
    
    // Удаляем старое изображение если существует
    $oldFile = glob($uploadsDir . 'products_' . $productId . '.*');
    foreach ($oldFile as $old) {
        if (is_file($old)) {
            @unlink($old);
        }
    }
    
    // Загружаем файл
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Error al mover el archivo');
    }
    
    // Обновляем путь к изображению в БД
    $relativePath = 'products_' . $productId . '.' . $extension;
    $query = "UPDATE products SET image_path = ?, updated_at = UNIX_TIMESTAMP() WHERE id = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'si', $relativePath, $productId);
    
    if (!mysqli_stmt_execute($stmt)) {
        @unlink($filePath); // Удаляем файл если не удалось обновить БД
        throw new Exception('Error al actualizar la base de datos: ' . mysqli_error($link));
    }
    
    $return['ok'] = 1;
    $return['file_path'] = $relativePath;
    $adminBasePath = getAdminBasePath();
    $return['url'] = $adminBasePath . '../uploads/' . $relativePath;
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    $return['err'] = $e->getMessage();
} catch (Error $e) {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    $return['err'] = 'Fatal error: ' . $e->getMessage();
}

if (ob_get_level() > 0) {
    ob_clean();
}
header('Content-Type: application/json');
echo json_encode($return);
exit;
?>

