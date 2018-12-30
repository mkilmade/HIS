<?php
require_once ('includes/config.inc.php');
class PDOConnection {
	public $pdo = NULL;
	public function __construct() {
		try {
			$this->pdo = $this->getConnection();
		} catch( PDOException $e ) {
			$this->pdo = NULL;
			throw($e);
		}
	}
	
	public function __destruct() {
		$this->pdo = NULL;
	}
	
	private function getConnection() {
		//set DB_* constants
		$db_conf = parse_ini_file ( './secure/config.ini', true ) ['database'];
		if (! defined ( 'DB_HOST' )) {
			define ( 'DB_HOST', $db_conf ['host'] );
			define ( 'DB_USER', $db_conf ['user'] );
			define ( 'DB_NAME', $db_conf ['name'] );
			define ( 'DB_PORT', $db_conf ['port'] );
			define ( 'DB_PRODUCTION', $db_conf ['production'] );
		}
		// mysql PDO args used (dsn, user, password, params)
		return new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
		               DB_USER,
				       $db_conf ['password'], 
		               [ PDO::ATTR_PERSISTENT => true ]);
	}
}

