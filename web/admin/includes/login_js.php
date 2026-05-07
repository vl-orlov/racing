<?
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
set_time_limit(0);

include __DIR__ . '/path_helper.php';
include getIncludesFilePath('config/config.php');

$ok = 0;
$err = '';

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!$input) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'ok' => 0,
        'err' => "JSON no válido"
    ]);
    exit;
}

if (!isset($input['pass']) || empty($input['pass'])) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'ok' => 0,
        'err' => "Falta la contraseña"
    ]);
    exit;
}

$pass = $input['pass'];
$adminPassword = $config['admin_password'] ?? 'qwerty';

// Простая проверка пароля (заглушка)
if ($pass === $adminPassword) {
    $_SESSION['admin_logged_in'] = true;
    $ok = 1;
} else {
    $err = "Contraseña incorrecta";
}

$return = [
    'ok' => $ok,
    'err' => $err
];

ob_clean();
header('Content-Type: application/json');
echo json_encode($return);
?>

