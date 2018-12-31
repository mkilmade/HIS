<?php
require_once ('includes/config.inc.php');
class PDOConnection {
	public $pdo = NULL;
	public function __construct() {
		try {
			$this->pdo = $this->getConnection ();
		} catch ( PDOException $e ) {
			$this->pdo = NULL;
			throw ($e);
		}
	}
	public function __destruct() {
		$this->pdo = NULL;
	}
	private function getConnection() {
		// set DB_* constants
		$db_conf = parse_ini_file ( './secure/config.ini', true ) ['database'];
		if (! defined ( 'DB_HOST' )) {
			define ( 'DB_HOST', $db_conf ['host'] );
			define ( 'DB_USER', $db_conf ['user'] );
			define ( 'DB_NAME', $db_conf ['name'] );
			define ( 'DB_PRODUCTION', $db_conf ['production'] );
		}
		// mysql PDO args used (dsn, user, password, params)
		return new PDO ( "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, $db_conf ['password'], [ 
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		] );
	}

	// insert a new row into a table
	public function insert_row(array &$data, string $table) {
		$fields = "";
		$params = "";
		$paramValues = [ ];

		foreach ( $data as $field => $value ) {
			$fields = $fields . ($fields == "" ? "" : ", ") . $field;
			$params = $params . ($params == "" ? "" : ", ") . "?";
			$paramValues [] = $value;
		}

		$status = $this->execute_query ( "INSERT INTO $table ($fields) VALUES ($params)", $paramValues );
		if ($status) {
			$status = $this->pdo->lastInsertId ();
		}
		return $status;
	}

	// update an entry in a table
	public function update_row(array &$data, string $table, $id) {
		$fldvals = "";
		$paramValues = [ ];

		foreach ( $data as $field => $value ) {
			$fldvals = $fldvals . ($fldvals == "" ? "" : ", ") . $field . "= ?";
			$paramValues [] = $value;
		}
		return $this->execute_query ( "UPDATE $table SET $fldvals WHERE " . $table . "_id = '$id'", $paramValues );
	}
	public function execute_query(string $query, array $parmValues) {
		try {
			$status = false;
			$stmt = $this->pdo->prepare ( $query );
			$status = $stmt->execute ( $parmValues );

			if (! $status) {
				$status = "{$stmt->errorInfo[2]} ({$stmt->errorInfo[0]})";
			}
		} catch ( PDOException $e ) {
			$status = "{$e->getMessage()} ({$e->getCode()})";
		} finally {
			return $status;
		}
	}
}

