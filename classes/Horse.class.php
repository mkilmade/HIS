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
	
	class Horse extends \Resource {
		public function __construct($id = NULL, HIS\Connection $conn = NULL) {
			$this->bindings['table']   = "horse";
			$this->bindings['key_fld'] = "name";
			$this->bindings['type']    = "s";
			parent::__construct($id, $conn);
		}
		
		public function getLastWinData() {
			// get the horse parameter from URL
			$conn = new HIS\Connection();
			$query = "SELECT trainer, jockey
              FROM tb17
              WHERE horse = ?
              ORDER BY race_date DESC
              LIMIT 1";
			
			$stmt = $conn->db->prepare($query);
			$stmt->bind_param('s', $this->name);
			$stmt->execute();
			$lastWinData = $stmt->get_result()->fetch_assoc();
			if (count($lastWinData) == 0) {
				$lastWinData["trainer"] = "";
				$lastWinData["jockey"] = "";
			}
			$stmt->close();
			$conn->close();
			
			return $lastWinData;
			
		}
		
	}
