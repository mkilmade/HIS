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

		if ($id == NULL) {
			return NULL;
		}

		$conn = new PDOConnection ();
		if ($conn == NULL) {
			return NULL;
		}

		$md = self::getMetaData ();
		$stmt = $conn->pdo->prepare ( "SELECT * FROM {$md ['table']} WHERE {$md ['id_fld']}  = ? LIMIT 1" );
		$stmt->execute ( [ 
				$id
		] );
		
		// entry not found
		if ($stmt->columnCount() == 0) {
			return FALSE;
		}
		return $stmt->fetchObject ( $md ['callingClass'] );

	}

	private function setProperties(array $data) {

		foreach ( $data as $property => $value ) {
			$this->$property = $value;
		}

	}

	function __destruct() {


	}

	public function insert_entry(array $data) {

		$md = self::getMetaData ();
		$conn = new PDOConnection ();
		$status = $conn->insert_row ( $data, $md ['table'] );

		if ($status) {
			// todo: remove when resources use id field as key
			if ($this instanceof Resource) {
				$md ['id_fld'] = $md ['table'] . "_id";
			}
			$data [$md ['id_fld']] = $status;
			$this->setProperties ( $data );
		}
		return $status;

	}

	public function update_entry(array &$data) {

		$md = self::getMetaData ();
		$conn = new PDOConnection ();
		$status = $conn->update_row ( $data, $md ['table'], $this->{$md ['id_fld']} );
		if ($status) {
			$this->setProperties ( $data );
		}
		return $status;

	}

	private static function getMetaData() {

		$cc = get_called_class ();
		return [ 
				'callingClass' => $cc,
				'id_fld' => $cc::$id_fld,
				'table' => $cc::$table
		];

	}
}
	
	