<?php
// Script config.inc.php
// ****** Settings ****** //
$ini = parse_ini_file ( 'config.ini', true );

// turn on/off debug mode
$_SESSION ['debug'] = $ini ['system'] ['debug'];

// / error log file location constanr
define ( 'ERROR_LOG_FILE', $ini ['system'] ['error_log'] );

// set time zone
define ( 'HIS_TIMEZONE', $ini ['system'] ['timezone'] );
date_default_timezone_set ( HIS_TIMEZONE );
unset($ini);
// ****** Error Handling ****** //

// create custom error handler function
function report_errors($num, $msg, $file, $line) {
	$date = new DateTime ( "now", new DateTimeZone ( HIS_TIMEZONE ) );
	$now = $date->format ( "Y-m-d H:i:s" );
	$m = "\r\n Logged: $now";
	$m .= "\r\nError Level: $num";
	$m .= "\r\n    Message: $msg";
	$m .= "\r\n       File: $file";
	$m .= "\r\n       Line: $line\r\n";
	error_log ( $m, 3, ERROR_LOG_FILE );

// 	$to      = 'mkilmade@gmail.com';
// 	$subject = 'HIS Error';
// 	$message = $m;
// 	$headers = 'From: mkilmade@Kelso.local' . "\r\n" .
// 			'Reply-To: mkilmade@Kelso.local' . "\r\n" .
// 			'X-Mailer: PHP/' . phpversion();
	
// 	mail($to, $subject, $message, $headers);
	
	// send generic message to browser, if possible
	// echo "<p>Error has occurred!<br>Check error log may contain more information. [Log Timestamp: ".$now."]</p>";
}

// set error handler to custon error handler
set_error_handler ( "report_errors", E_ALL );

// report all errors possible
error_reporting ( E_ALL );

/* activate MySQL error reporting */
$driver = new mysqli_driver();
$driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;
unset($driver);

function exception_handler($e) {
	$date = new DateTime ( "now", new DateTimeZone ( HIS_TIMEZONE ) );
	$now = $date->format ( "Y-m-d H:i:s" );
	$m = "\r\n Logged: $now";
	$m .= "\r\nException Class: " . get_class($e);
	$m .= "\r\n     Error Code: {$e->getCode()}";
	$m .= "\r\n        Message: {$e->getMessage()}";
	$m .= "\r\n           File: {$e->getFile()}";
	$m .= "\r\n           Line: {$e->getLine()}\r\n";
	error_log ( $m, 3, ERROR_LOG_FILE );
	
// 	$to      = 'mkilmade@gmail.com';
// 	$subject = 'HIS Exception Logged';
// 	$message = $e->getMessage();
// 	$headers = 'From: mkilmade@Kelso.local' . "\r\n" .
// 			'Reply-To: mkilmade@Kelso.local' . "\r\n" .
// 			'X-Mailer: PHP/' . phpversion();
	
// 	mail($to, $subject, $message, $headers);
}

set_exception_handler('exception_handler');

?>