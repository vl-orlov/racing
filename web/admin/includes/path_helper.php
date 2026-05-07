<?
/**
 * Вспомогательные функции для определения путей
 */

/**
 * Получает базовый путь админки (например, /admin/ или /public/racing/admin/)
 * Это веб-путь для использования в HTML/CSS/JS
 */
function getAdminBasePath() {
    $scriptPath = $_SERVER['SCRIPT_NAME'];
    $scriptDir = dirname($scriptPath);
    
    if (strpos($scriptDir, '/admin') !== false) {
        $adminPos = strrpos($scriptDir, '/admin');
        $basePath = substr($scriptDir, 0, $adminPos + 6); // +6 для '/admin'
    } else {
        $basePath = $scriptDir;
    }
    
    if (substr($basePath, -1) !== '/') {
        $basePath .= '/';
    }
    
    return $basePath;
}

/**
 * Получает путь к общим includes файлам (файловый путь на диске)
 * Используется для include/require в PHP
 */
function getIncludesPath() {
    // path_helper.php находится в web/admin/includes/
    // Нужно получить web/includes/
    $helperDir = __DIR__; // web/admin/includes/
    $adminDir = dirname($helperDir); // web/admin/
    $webDir = dirname($adminDir); // web/
    
    return $webDir . '/includes/';
}

/**
 * Получает полный путь к файлу в includes
 */
function getIncludesFilePath($filename) {
    return getIncludesPath() . $filename;
}
?>

