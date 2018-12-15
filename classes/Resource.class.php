<?php
spl_autoload_register(function ($class) {
	require_once $class . '.class.php';
});
	abstract class Resource extends \HisEntity {
		
		public function checkExistence(string $resourceName) {
			$tableName = $this->bindings['table'];
			// query table for resource already exists in table
			$conn = new HIS\Connection();
			$stmt = $conn->db->prepare("SELECT * FROM $tableName WHERE name = ?");
			$stmt->bind_param('s', $resourceName);
			$stmt->execute();
			$stmt->store_result();
			$status = $stmt->num_rows;
			$stmt->close();
			$conn->close();
			return $status;
		}
		
		public function addResource(string $resourceName) {
			if (!$this->checkExistence($resourceName)) {
				return $this->insert_entry(["name" => $resourceName]);
			} else {
				return "";
			}
		}
		
	}

