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
    echo json_encode(['ok' => 0, 'err' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!$input) {
  if (ob_get_level() > 0) {
    ob_clean();
  }
  header('Content-Type: application/json');
  echo json_encode(['ok' => 0, 'err' => 'JSON no válido']);
  exit;
}

try {
    global $link;
    
    // Не используем mysqli_real_escape_string для prepared statements - они уже безопасны
    $name = isset($input['name']) ? trim($input['name']) : '';
    $description = isset($input['description']) ? trim($input['description']) : '';
    // Сохраняем цену точно как указано, без преобразования через floatval
    $price = isset($input['price']) ? trim($input['price']) : '0.00';
    // Преобразуем в число для проверки, но сохраняем исходное значение
    $priceValue = floatval($price);
    if ($priceValue < 0) {
        $priceValue = 0.00;
        $price = '0.00';
    }
    
    // Проверка обязательных полей
    if (empty($name)) {
        throw new Exception('El nombre es obligatorio');
    }
    
    $query = "INSERT INTO products (name, description, price) VALUES (?, ?, ?)";
    
    $stmt = mysqli_prepare($link, $query);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . mysqli_error($link));
    }
    
    // Используем 's' для всех параметров, включая цену (DECIMAL в MySQL принимает строку)
    mysqli_stmt_bind_param($stmt, "sss", $name, $description, $price);
    
    $success = mysqli_stmt_execute($stmt);
    
    if (!$success) {
        throw new Exception("Error al ejecutar la consulta: " . mysqli_stmt_error($stmt));
    }
    
    mysqli_stmt_close($stmt);
    
    if (ob_get_level() > 0) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => 1]);
    exit;
    
} catch (Exception $e) {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    if (ob_get_level() > 0) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => 0, 'err' => $e->getMessage()]);
    exit;
} catch (Error $e) {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    if (ob_get_level() > 0) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => 0, 'err' => 'Fatal error: ' . $e->getMessage()]);
    exit;
}
?>

