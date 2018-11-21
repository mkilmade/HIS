<?php

require_once('Connection.class.php');
spl_autoload_register(function ($class) {
	require_once $class . '.class.php';
});
	/** 
 * @author mkilmade
 * 
 */
class Track extends \HisEntity {

	/**
	 */
	public function __construct($id, $conn = NULL) {
		$this->bindings['table']   = "track";
		$this->bindings['key_fld'] = "track_id";
		$this->bindings['type']    = "s";
		parent::__construct($id, $conn);
	}
	
	public static function getTracks($id) {
		$conn = new HIS\Connection();
		$searchid=$id."%";
		
		$query = "SELECT track_id
                  FROM track
                  WHERE track_id LIKE ?
                  ORDER BY track_id";
		
		$stmt = $conn->db->prepare($query);
		$stmt->bind_param('s', $searchid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($track_id);
		
		$trackObjs = array();
		if ($stmt->num_rows > 0) {
			while ($stmt->fetch()) {
				$trackObjs[] = new Track($track_id, $conn);
			}
		}
		$stmt->free_result();
		$stmt->close();
		$conn->close();
		
		return $trackObjs;
	}
	
}

