<?php
define('DB_HOST','127.0.0.1');
define('DB_USER','mkilmade');
define('DB_PASSWORD','albhaw29&');
define('DB_PORT','3306');
define('DB_NAME', 'tbd');

$db = @new mysqli(DB_HOST,
                 DB_USER,
                 DB_PASSWORD,
                 DB_NAME,
                 DB_PORT);

if ($db->connect_errno) {
    die;
} else {
    $db->set_charset("utf8");
}