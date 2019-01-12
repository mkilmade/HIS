<?php
// common environment setup

// set max session timeout
ini_set ( 'session.gc_maxlifetime', 3600 );
ini_set ( 'session.dispaly_error', 1 );

// include/require settings used to locate files
$root = dirname(__DIR__);
set_include_path ( $root . '/classes' . PATH_SEPARATOR . $root . '/includes' . PATH_SEPARATOR . $root . '/secure' . PATH_SEPARATOR . get_include_path () );
//set_include_path ( 'classes/' . PATH_SEPARATOR . 'includes/' . PATH_SEPARATOR . 'secure/' . PATH_SEPARATOR . get_include_path () );
spl_autoload_extensions ( '.class.php,.inc.php,.php' );
spl_autoload_register ();
unset($root);

// environemnt set up
require_once ('config.inc.php');

// $_SESSION and session set up / start
if (session_status () !== PHP_SESSION_ACTIVE) {
	session_start ();
	if (! isset ( $_SESSION ['defaults'] )) {
		echo "Setting session defaults...";
		$_SESSION ['defaults'] = Defaults::get_his_defaults ();

		// initialize track conditions to have more accurate input when
		// adding winner entries by race # (lower to higher)
		if (! isset ( $_SESSION ['dirt_track_condition'] )) {
			$_SESSION ['dirt_track_condition'] = "Fast";
		}
		if (! isset ( $_SESSION ['turf_track_condition'] )) {
			$_SESSION ['turf_track_condition'] = "Firm";
		}
		// print_r($_SESSION);
	}
}
	