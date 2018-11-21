<?php
/**
 *
 * @author Mike Kilmade
 *
 */
require_once('Connection.class.php');
spl_autoload_register(function ($class) {
	require_once $class . '.class.php';
});
	abstract class HisEntity {
		// database table bindings[] (table, key_fld and type)
		protected $bindings = array();
		/**
		 */
		function __construct($id, $conn = NULL) {
			if ($conn == NULL) {
				$conn = new HIS\Connection();
				$this->propertyInit($id, $conn);
				$conn->close();
			} else {
				$this->propertyInit($id, $conn);
			}
		}
		
		private function propertyInit($id, $conn) {
			// todo: change to always use id field when tables are normalized
			$query = "SELECT *
                      FROM {$this->bindings['table']}
                      WHERE {$this->bindings['key_fld']} = ?";
			
			$stmt = $conn->db->prepare($query);
			$stmt->bind_param($this->bindings['type'], $id);
			if ($stmt->execute()) {
				$result = $stmt->get_result();
				if ($result->num_rows > 0) {
					// dynamically create properties corresponding to each field in table
					foreach($result->fetch_assoc() as $field => $value) {
						$this->$field = $value;
					}
				}
			}
			$stmt->free_result();
			$stmt->close();
		}
		
		function __destruct() {
		}
	}
	
	