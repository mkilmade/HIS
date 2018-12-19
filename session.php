<?php
ini_set('session.gc_maxlifetime',3600);

// include/require settings used to locate files
set_include_path('classes/' . PATH_SEPARATOR . get_include_path());
spl_autoload_extensions('.class.php,.inc,.php');
spl_autoload_register();
// environemnt set up
require_once('includes/config.inc.php');

// $_SESSION and session set up
if (isset($_GET['reset_session']) && $_GET['reset_session'] == 1 ) {
		session_start();
		session_destroy();
		header('Location: index.php');
	}
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
		if (!isset($_SESSION['defaults'])) {
			echo "Setting session defaults...";
			$_SESSION['defaults'] = Defaults::get_his_defaults();
			
			// initialize track conditions to have more accurate input when
			// adding winner entries by race # (lower to higher)
			if(!isset($_SESSION['dirt_track_condition'])) {
				$_SESSION['dirt_track_condition'] = "Fast";
			}
			if(!isset($_SESSION['turf_track_condition'])) {
				$_SESSION['turf_track_condition'] = "Firm";
			}
			//print_r($_SESSION);
		}
	}
	