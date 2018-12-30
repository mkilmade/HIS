<?php
abstract class Resource extends \HisEntity {
	public $name;
	public $shortcut;
	public function checkExistence(string $resourceName) {
		$tableName = lcfirst ( get_called_class () );
		// query table for resource already exists in table
		$query = "SELECT Count(*) AS hit 
                  FROM $tableName
                  WHERE name = :resourceName";
		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->bindValue(':resourceName', $resourceName, PDO::PARAM_STR);
		$stmt->execute ( );
		$stmt->bindColumn('hit', $hit);
		
		$status = 0;
		if ( $stmt->fetch( PDO::FETCH_BOUND ) ) {
			$status = ($hit > 0 ? 1 : 0);
		} 
		return $status;
	}
	public function addResource(string $resourceName) {
		if (! $this->checkExistence ( $resourceName )) {
			return $this->insert_entry ( [ 
					"name" => $resourceName
			] );
		} else {
			return "";
		}
	}
	
	public static function getResourceNames(string $name) {
		$tableName = lcfirst ( get_called_class () );
		$id = $tableName . "_id";
		$searchName = $name . "%";
		// first list those matching 'shortcut' field
		$query = "SELECT $id, name, shortcut
              FROM $tableName
              WHERE shortcut LIKE :searchName
              ORDER BY shortcut";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->bindValue(':searchName', $searchName, PDO::PARAM_STR);
		$stmt->execute ( );
		$stmt->bindColumn($id, $id);
		$stmt->bindColumn('name', $name);
		$stmt->bindColumn('shortcut', $shortcut);
		
		$names = [ ];
		while ( $stmt->fetch( PDO::FETCH_BOUND ) ) {
			$names [] = array (
					'label' => htmlentities ( $shortcut . ' - ' . $name, ENT_NOQUOTES ),
					'value' => htmlentities ( $name, ENT_NOQUOTES ) // change to $id when normalized
			);
		}
		
		$stmt = NULL;

		// add those matching 'name' field
		$query = "SELECT $id, name
              FROM $tableName
              WHERE name LIKE :searchName
              ORDER BY name";

		$stmt = $conn->pdo->prepare ( $query );
		$stmt->bindValue(':searchName', $searchName, PDO::PARAM_STR);
		$stmt->execute ();
		$stmt->bindColumn($id, $id);
		$stmt->bindColumn('name', $name);

		while ( $stmt->fetch( PDO::FETCH_BOUND ) ) {
			$names [] = array (
					'label' => htmlentities ( $name, ENT_NOQUOTES ),
					'value' => htmlentities ( $name, ENT_NOQUOTES ) // change to $id when normalized
			);
		}
		return $names;
	}
}