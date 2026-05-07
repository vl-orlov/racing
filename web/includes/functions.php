<?
function DBconnect() {
    global $link;

    // Load config
    $configPath = __DIR__ . '/config/config.php';
    if (!file_exists($configPath)) {
        throw new Exception("Error: Configuration file not found at: " . $configPath);
    }
    $config = require $configPath;

    // Check if the parameters are loaded
    if (!isset($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_database'], $config['db_port'])) {
        throw new Exception("Error: The configuration file is corrupted or incomplete.");
    }

    // Extract database connection details
    $db_host = $config['db_host'];
    $db_user = $config['db_user'];
    $db_pass = $config['db_pass'];
    $db_database = $config['db_database'];
    $db_port = $config['db_port']; // Ensure we use the port

    // Connect to MySQL with port
    $link = mysqli_connect($db_host, $db_user, $db_pass, $db_database, $db_port);

    // Check connection
    if (!$link) {
        throw new Exception("Unable to establish a DB connection: " . mysqli_connect_error());
    }

    mysqli_set_charset($link, "utf8");
}
