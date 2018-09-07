<?php # Script config.inc.php
// ****** Settings ****** //

// turn on/off debug mode
$_SESSION['debug']='on';

// location of MySQL connection script constant
define('MYSQL', '/Library/WebServer/Documents/tbd/secure/connect.inc.php');

// error log file location constanr
define('ERROR_LOG_FILE', '/Library/WebServer/Documents/tbd/error_log');

// set time zone
date_default_timezone_set('America/New_York');

// ****** Error Handling ****** //

// create custom error handler function
function report_errors($num, $msg, $file, $line) {
    $date = new DateTime("now", new DateTimeZone('America/New_York'));
    $now = $date->format("Y-m-d H:i:s");
    $m  = "\n Logged: $now";
    $m .= "\nError #: $num";
    $m .= "\nMessage: $msg";
    $m .= "\n   File: $file";
    $m .= "\n   Line: $line\n";
    error_log($m, 3, ERROR_LOG_FILE);

    // send generic message to browser, if possible
    //echo "<p>Error has occurred!<br>Check error log may contain more information. [Log Timestamp: ".$now."]</p>";
}

// set error handler ot custon error handler
set_error_handler("report_errors", E_ALL);

// report all errors possible
error_reporting(E_ALL);

//ini_set('error_log', "/Library/WebServer/Documents/tbd/error_log");

?>