<?php
// securely set up db environment, make db connection and protect code

// $ini is set in Connection.class.php constructor
if (!defined('DB_HOST')) {
	define('DB_HOST', $ini['database']['host']);
	define('DB_USER', $ini['database']['user']);
	define('DB_NAME', $ini['database']['name']);
	define('DB_PORT', $ini['database']['port']);
	define('DB_PRODUCTION', $ini['database']['production']);
}

$db = @new mysqli("p:" . DB_HOST, DB_USER, $ini['database']['password'], DB_NAME, DB_PORT);

if ($db->connect_errno) {
    echo "Database connect error:<br>
             Number: $db->connect_errno<br>
               Text: $db->connect_error";
    die();
} else {
    $db->set_charset("utf8");
}
?>