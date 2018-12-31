<?php
/**
 *
 * @author Mike Kilmade
 *
 */
abstract class HisEntity {
	/**
	 */
	function __construct() {
	}
	
	public static function IdFactory($id) {
		if ($id == NULL) { return NULL; }
		
		$conn = new PDOConnection();
		if ($conn == NULL) {return NULL;}
		$callingClass = get_called_class();
		$table = constant( $callingClass . '::TABLE' );
		$id_fld = constant( $callingClass . '::ID_FLD' );
		
		$stmt = $conn->pdo->prepare("SELECT * FROM $table WHERE $id_fld  = ? LIMIT 1");
		$stmt->execute([$id]);
		return $stmt->fetchObject($callingClass);
	}
	
	private function setProperties(array $data) {
		foreach ( $data as $property => $value ) {
			$this->$property = $value;
		}
	}
	function __destruct() {
	}
	public function insert_entry(array $data) {
		$callingClass = get_called_class();
		$table = constant( $callingClass . '::TABLE' );
		$id_fld = constant( $callingClass . '::ID_FLD' );
		
		$conn = new PDOConnection ();
		$status = $conn->insert_row ( $data, $table );
		
		if ($status) {
			$id_field = $id_fld;
			// todo: remove when resources use id field as key
			if ($this instanceof Resource) {
				$id_field = $table . "_id";
			}
			$data [$id_field] = $status;
			$this->setProperties ( $data );
		}
		return $status;
	}
	public function update_entry(array &$data) {
		$callingClass = get_called_class();
		$table = constant( $callingClass . '::TABLE' );
		$id_fld = constant( $callingClass . '::ID_FLD' );
		
		$conn = new PDOConnection ();
		$status = $conn->update_row ( $data, $table, $this->$id_fld );
		if ($status) {
			$this->setProperties ( $data );
		}
		return $status;
	}
}
	
	