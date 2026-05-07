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
    
    // Проверяем, что $link доступен
    global $link;
    if (!isset($link) || !$link) {
        throw new Exception("Variable \$link no está disponible después de DBconnect()");
    }
} catch (Exception $e) {
    if (ob_get_level() > 0) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode([
        'res' => '',
        'ok' => 0,
        'cant' => 0,
        'err' => "Error de conexión a la base de datos: " . $e->getMessage(),
    ]);
    exit;
}

$basePath = getAdminBasePath();

$res = '';
$cant = 0;

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!$input) {
    if (ob_get_level() > 0) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode([
        'res' => '',
        'ok' => 0,
        'cant' => 0,
        'err' => "JSON no válido"
    ]);
    exit;
}

$busc = isset($input['busc']) ? trim($input['busc']) : '';

try {
    global $link;
    
    $res .= '
    	<div class="adm_zag">ID</div>
    	<div class="adm_zag">Nombre</div>
    	<div class="adm_zag"></div>
    	<div class="adm_zag"></div>
    ';
    
    $query="SELECT id, name, description, price, image_path FROM products";
    
    if ( strlen($busc) > 0 ) {
    	$buscEscaped = mysqli_real_escape_string($link, $busc);
    	$query .= " WHERE 
    			id				LIKE '%".$buscEscaped."%' OR 
    			name			LIKE '%".$buscEscaped."%' OR
    			description		LIKE '%".$buscEscaped."%'";
    }
    
    $query .= " ORDER BY id DESC";
    
    $result = mysqli_query($link, $query);
    
    if (!$result) {
        throw new Exception("Error SQL: " . mysqli_error($link) . " (Query: " . htmlspecialchars($query) . ")");
    }
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    	$cant++;
    	$displayName = $row['name'] ? htmlspecialchars($row['name']) : '(Sin nombre)';
    	
    	$res .= '
    		<div class="adm_list_txt product-row" id="product_row_'.$row['id'].'" onclick="selectProduct('.$row['id'].')" style="cursor: pointer;">
    			'.$row['id'].'
    		</div>
    		<div class="adm_list_txt product-row" id="product_row_name_'.$row['id'].'" onclick="selectProduct('.$row['id'].')" style="cursor: pointer;">
    			'.$displayName.'
    		</div>
    		<div class="adm_list_txt pad product-row" id="product_row_edit_'.$row['id'].'" onclick="event.stopPropagation(); selectProduct('.$row['id'].')">
    			<img src="'.$basePath.'img/edit.png" class="edit-icon-size">
    		</div>
    		<div class="adm_list_txt pad product-row" id="product_row_del_'.$row['id'].'" onclick="event.stopPropagation(); product_del('.$row['id'].')">
    			<img src="'.$basePath.'img/trash.png" class="edit-icon-size">
    		</div>
    	';
    }
    
    // Очищаем буфер вывода перед отправкой JSON
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        "res" => $res,
        "ok" => 1,
        "cant" => $cant,
    ]);
    exit;
    
} catch (Exception $e) {
    if (ob_get_level() > 0) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode([
        'res' => '',
        'ok' => 0,
        'cant' => 0,
        'err' => $e->getMessage(),
    ]);
    exit;
} catch (Error $e) {
    if (ob_get_level() > 0) {
        ob_clean();
    }
    header('Content-Type: application/json');
    echo json_encode([
        'res' => '',
        'ok' => 0,
        'cant' => 0,
        'err' => "Fatal error: " . $e->getMessage(),
    ]);
    exit;
}
?>

