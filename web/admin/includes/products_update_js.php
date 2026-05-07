<?
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
set_time_limit (0);

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
    echo json_encode(['ok' => 0, 'err' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

$return = ['ok' => 0, 'err' => ''];

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!$input || !isset($input['product_id'])) {
    if (ob_get_level() > 0) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => 0, 'err' => 'product_id requerido']);
    exit;
}

$productId = intval($input['product_id']);

try {
    global $link;
    
    // Не используем mysqli_real_escape_string для prepared statements - они уже безопасны
    $name = isset($input['name']) ? trim($input['name']) : '';
    $description = isset($input['description']) ? trim($input['description']) : '';
    $price = isset($input['price']) ? trim($input['price']) : '0.00';
    $priceValue = floatval($price);
    if ($priceValue < 0) {
        $priceValue = 0.00;
        $price = '0.00';
    }
    $allowed_categories = ['remeras','polo','musculosa','mochila','botella','bucket','accesorios'];
    $category = isset($input['category']) && in_array($input['category'], $allowed_categories)
        ? $input['category'] : '';
    $image_path = isset($input['image_path']) ? trim($input['image_path']) : '';
    
    // Проверка обязательных полей
    if (empty($name)) {
        throw new Exception("El nombre es obligatorio");
    }
    
    // Если image_path не передан, сохраняем текущий путь
    if (empty($image_path)) {
        $query = "SELECT image_path FROM products WHERE id = ? LIMIT 1";
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 'i', $productId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $current = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if ($current) {
            $image_path = $current['image_path'] ?? '';
        }
    }
    
    $query = "UPDATE products SET name = ?, description = ?, price = ?, category = ?, image_path = ?, updated_at = UNIX_TIMESTAMP() WHERE id = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'sssssi', $name, $description, $price, $category, $image_path, $productId);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error al actualizar datos del producto: " . mysqli_error($link));
    }
    mysqli_stmt_close($stmt);
    
    $return['ok'] = 1;
    $return['res'] = 'Datos guardados correctamente';
    
} catch (Exception $e) {
    $return['err'] = $e->getMessage();
} catch (Error $e) {
    $return['err'] = 'Fatal error: ' . $e->getMessage();
}

if (ob_get_level() > 0) {
    ob_clean();
}
header('Content-Type: application/json');
echo json_encode($return);
exit;
?>

