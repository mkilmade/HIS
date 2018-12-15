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
		function __construct($id, HIS\Connection $conn = NULL) {
			if ($id == NULL) {
				return;
			}
			if ($conn == NULL) {
				$conn = new HIS\Connection();
				$this->propertyInit($id, $conn);
				$conn->close();
			} else {
				$this->propertyInit($id, $conn);
			}
		}
		
		private function propertyInit($id, HIS\Connection $conn) {
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
					$this->setProperties($result->fetch_assoc()); 
				}
			}
			$stmt->free_result();
			$stmt->close();
		}
		
		private function setProperties(array $data) {
			foreach($data as $property => $value) {
				$this->$property = $value;
			}
		}
		
		function __destruct() {
		}
		
		public function insert_entry(array $data) {
			$conn = new HIS\Connection();
			$status = $conn->insert_row($data, $this->bindings['table']);
			if ($status) {
				$id_field = $this->bindings['key_fld'];
				// todo: remove when resources use id field as key
				if ($this instanceof Resource) {
					$id_field = $this->bindings['table'] . "_id";
				}
				$data[$id_field] = $status;
				$this->setProperties($data);
			}
			$conn->close();
			return $status;
		}
		
		public function update_entry(array &$data, $id) {
			$conn = new HIS\Connection();
			$status =  $conn->update_row($data, $this->bindings['table'], $id);
			if ($status) {
				$this->setProperties($data);
			}
			$conn->close();
			return $status;
		}
		
	}
	
	