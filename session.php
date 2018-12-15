<?php
ini_set('session.gc_maxlifetime',3600);
spl_autoload_register(function ($class) {
	require_once 'classes/' . $class . '.class.php';
});
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
	