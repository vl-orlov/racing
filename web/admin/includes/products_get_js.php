<?
session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
set_time_limit (0);
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

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
    echo json_encode(['ok' => 0, 'err' => 'Error de conexión: ' . $e->getMessage(), 'data' => null]);
    exit;
}

$return = ['ok' => 0, 'err' => '', 'data' => null];

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
    
    // Читаем цену как строку, чтобы сохранить точное значение (включая незначащие нули)
    $query = "SELECT id, name, description, CAST(price AS CHAR) as price, image_path, created_at, updated_at 
              FROM products WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, 'i', $productId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $productData = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$productData) {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        header('Content-Type: application/json');
        echo json_encode(['ok' => 0, 'err' => 'Producto no encontrado']);
        exit;
    }
    
    // Конвертация дат
    $productData['created_at'] = $productData['created_at'] ? date('Y-m-d H:i', $productData['created_at']) : '';
    $productData['updated_at'] = $productData['updated_at'] ? date('Y-m-d H:i', $productData['updated_at']) : '';
    
    // Цена читается как строка через CAST, сохраняем точное значение
    // Убираем только лишние пробелы, но не форматируем
    $productData['price'] = trim($productData['price']);
    if (empty($productData['price']) || !is_numeric($productData['price'])) {
        $productData['price'] = '0.00';
    }
    
    $return['ok'] = 1;
    $return['data'] = $productData;
    
} catch (Exception $e) {
    $return['err'] = 'Error: ' . $e->getMessage();
    error_log("Error en products_get_js.php: " . $e->getMessage() . " en línea " . $e->getLine());
} catch (Error $e) {
    $return['err'] = 'Error fatal: ' . $e->getMessage();
    error_log("Error fatal en products_get_js.php: " . $e->getMessage() . " en línea " . $e->getLine());
}

// Очищаем буфер вывода перед отправкой JSON
if (ob_get_level() > 0) {
    ob_clean();
}

header('Content-Type: application/json');
echo json_encode($return);
exit;
?>

