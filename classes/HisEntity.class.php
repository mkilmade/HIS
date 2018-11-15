<?php
/**
 *
 * @author Mike Kilmade
 *
 */
spl_autoload_register(function ($class) {
	//echo "...including " . $class;
	include $class . '.class.php';
});
	
	abstract class HisEntity {
		protected $table = NULL;
		protected $conn = NULL;
		/**
		 */
		function __construct($id) {
			$this->conn = new Connection();
			$query = "SELECT *
              FROM $this->table
              WHERE $this->table"."_id = ?";

			$stmt = $this->conn->db->prepare($query);
			$stmt->bind_param('i', $id);
			$stmt->execute();
			
			// dynamically create properties corresponding to each field in table
			foreach($stmt->get_result()->fetch_assoc() as $field => $value) {
				$this->$field = $value;
			}
			
			$stmt->free_result();
			$stmt->close();
		}
		
		function __destruct() {
			$this->conn->close();
		}
	}
	
	