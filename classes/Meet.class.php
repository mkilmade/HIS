<?php

require_once('HisEntity.class.php');
require_once('Connection.class.php');

/**
 *
 * @author mkilmade
 *        
 */
class Meet extends \HisEntity {

	/**
	 */
	public function __construct($id, $conn = NULL) {
		$this->bindings['table']   = "race_meet";
		$this->bindings['key_fld'] = "race_meet_id";
		$this->bindings['type']    = "i";
		parent::__construct ( $id, $conn);
	}
	
	public static function getTrackId($race_date) {
		$conn = new HIS\Connection();
		$query = "SELECT
                    race_meet_id
                  FROM race_meet
                  WHERE start_date <= ? AND
                        end_date   >= ?
                  LIMIT 1";
		
		$stmt = $conn->db->prepare($query);
		$stmt->bind_param('ss', $race_date, $race_date);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($race_meet_id);
		if ($stmt->num_rows > 0) {
			$stmt->fetch();
			$meetObj = new Meet($race_meet_id);
			$track_id = $meetObj->track_id;
		} else {
			$track_id = "";
		}
		$stmt->free_result();
		$stmt->close();
		$conn->close();
		
		return array('track_id' => $track_id);
	}
	
}

