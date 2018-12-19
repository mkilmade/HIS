<?php
	abstract class Resource extends \HisEntity {
		
		public function checkExistence(string $resourceName) {
			$tableName = $this->bindings['table'];
			// query table for resource already exists in table
			$conn = new Connection();
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
		
		public static function getResourceNames(string $name) {
			$tablename = lcfirst(get_called_class()); 
			$conn = new Connection();
			$id = $tablename . "_id";
			$searchname=$name."%";
			// first list those matching 'shortcut' field
			$query = "SELECT $id, name, shortcut
              FROM $tablename
              WHERE shortcut LIKE ?
              ORDER BY shortcut";
			
			$stmt = $conn->db->prepare($query);
			$stmt->bind_param('s', $searchname);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id, $name, $shortcut);
			
			$names = array();
			if ($stmt->num_rows > 0) {
				while ($stmt->fetch()) {
					$names[] = array(
							'label' => htmlentities($shortcut . ' - ' . $name, ENT_NOQUOTES),
							'value' => htmlentities($name, ENT_NOQUOTES) // change to $id when normalized
					);
				}
			}
			$stmt->free_result();
			$stmt->close();
			
			// add those matching 'name' field
			$query = "SELECT $id, name
              FROM $tablename
              WHERE name LIKE ?
              ORDER BY name";
			
			$stmt = $conn->db->prepare($query);
			$stmt->bind_param('s', $searchname);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id, $name);
			
			if ($stmt->num_rows > 0) {
				while ($stmt->fetch()) {
					$names[] = array(
							'label' => htmlentities($name, ENT_NOQUOTES),
							'value' => htmlentities($name, ENT_NOQUOTES) // change to $id when normalized
					);
				}
			}
			$stmt->free_result();
			$stmt->close();
			$conn->close();
			
			return $names;
		}
	}
	