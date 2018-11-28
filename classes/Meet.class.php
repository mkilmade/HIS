<?php

require_once('Connection.class.php');
spl_autoload_register(function ($class) {
	require_once $class . '.class.php';
});
	
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
			$rmObj = new Meet($race_meet_id);
			$track_id = $rmObj->track_id;
		} else {
			$track_id = "";
		}
		$stmt->free_result();
		$stmt->close();
		$conn->close();
		
		return array('track_id' => $track_id);
	}
	
	public function meet_filter($comparefield) {
		$filter  = "$comparefield >= '{$this->start_date}' AND ";
        $filter .= "$comparefield <= '{$this->end_date}' AND ";
        $filter .= "track_id = '{$this->track_id}'";
        
		return $filter;
	}

	public function getClassTally() {
		$query = "SELECT
	                 count(DISTINCT race_date,race) AS races,
	                 race_class
	              FROM tb17
	              WHERE " . $this->meet_filter('race_date') . "
	              GROUP By race_class
	              ORDER BY races DESC, race_class";		
		return TB17::getResultArray($query);		
	}

	public function getPreviousTrackWins() {
		$query = "SELECT previous_track_id,
		                     COUNT(*) as wins
		              FROM tb17
		              WHERE previous_track_id IS NOT NULL AND " . $this->meet_filter('race_date') . "
		              GROUP By previous_track_id
		              ORDER BY wins DESC, previous_track_id";
		return TB17::getResultArray($query);
	}

	public function getFtsWins() {
		$conn = new HIS\Connection();
		$query = "SELECT COUNT(*)
	              FROM tb17
	              WHERE comment LIKE '%FTS%' AND " . $this->meet_filter('race_date') . "
	              LIMIT 1";
		$stmt = $conn->db->prepare($query);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($wins);
		if ($stmt->num_rows == 1) {
			$stmt->fetch();
		} else {
			$wins = 0;
		}
		$stmt->free_result();
		$stmt->close();
		$conn->close();
		return $wins;
	}
	
	public function getPreviouslyRanAtMeet() {
		$query = "SELECT
                   horse,
                   race_date
	              FROM tb17
	              WHERE " . $this->meet_filter('race_date') . " AND
	                    previous_track_id = '{$this->track_id}' AND
	                    previous_date >= '{$this->start_date}'  AND
	                    previous_date <= '{$this->end_date}'
	              ORDER BY horse, race_date DESC";
		return TB17::getResultArray($query);
	} // function
	
	
	public function getPreviousRaceAtMeetPerCard() {
		$query = "SELECT
                 COUNT(*) AS races,
                 SUM(IF(previous_track_id  = '{$this->track_id}'  AND
                            previous_date >= '{$this->start_date}' AND
                            previous_date <= '{$this->end_date}'
                    ,1,0)) As wins,
                 race_date
              FROM tb17
              WHERE " . $this->meet_filter('race_date') . "
              GROUP BY race_date
              ORDER BY race_date DESC";
		return TB17::getResultArray($query);
	}
	
	public function getMultipleWins() {
		$query = "SELECT
                wins, horse
              FROM (
                    SELECT count(*) AS wins, horse
                    FROM tb17
                    WHERE " . $this->meet_filter('race_date') . "
                    GROUP BY horse
                   ) AS multi_winners
              WHERE wins > '1'
              ORDER BY wins, horse";
		return TB17::getResultArray($query);		
	}
	
	public function getPreviousFinishTally() {
		$query = "SELECT
	                 COUNT(DISTINCT race_date,race) AS count,
	                 previous_finish_position
	              FROM tb17
	              WHERE " . $this->meet_filter('race_date') . " AND
	                    previous_track_id = '{$this->track_id}' AND
	                    previous_date >= '{$this->start_date}' AND
	                    previous_date <= '{$this->end_date}'
	              GROUP BY previous_finish_position";
		return TB17::getResultArray($query);
	}

	public static function getMeets() {
		$conn = new HIS\Connection();
		$query = "SELECT race_meet_id
		              FROM race_meet
		              ORDER BY start_date DESC
		             ";
		$stmt = $conn->db->prepare($query);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($race_meet_id);
		$meets = array();
		while ($stmt->fetch()) {
			$meets[] = new Meet($race_meet_id);
		}
		$stmt->close();
		$conn->close();	
		
		return $meets;
	}
}

