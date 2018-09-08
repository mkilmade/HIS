<?php
$ini = parse_ini_file('his.ini');

define('DB_HOST', $ini['host']);
define('DB_USER', $ini['user']);
define('DB_NAME', $ini['name']);
define('DB_PORT', $ini['port']);

$db = @new mysqli(DB_HOST,
                  DB_USER,
                  $ini['password'],
                  DB_NAME,
                  DB_PORT);

if ($db->connect_errno) {
    echo "Database connect error:<br>
         Number: $db->connect_errno<br>
           Text: $db->connect_error";
    die;
} else {
    $db->set_charset("utf8");
}
$ini='';