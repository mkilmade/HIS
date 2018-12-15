<?php
/**
 *
 * @author mkilmade
 *        
 */
require_once('Connection.class.php');
spl_autoload_register(function ($class) {
	require_once $class . '.class.php';
});
	
	class TB17 extends \HisEntity {
		public function __construct($id = NULL, HIS\Connection $conn = NULL) {
			$this->bindings['table']   = "tb17";
			$this->bindings['key_fld'] = "tb17_id";
			$this->bindings['type']    = "i";
		    parent::__construct ($id, $conn);
		}

		public static function last_race_date(string $meet_filter = NULL) {
			$conn = new HIS\Connection();
			$query = "SELECT MAX(race_date)
		              FROM tb17" . ($meet_filter == NULL ? "" : " WHERE $meet_filter") . "
		              LIMIT 1
	                 ";
			$stmt = $conn->db->prepare($query);
			$stmt->execute();
			$stmt->store_result();
			if ($stmt->num_rows == 0) {
				return '';
			}
			$stmt->bind_result($last_race_date);
			$stmt->fetch();
			$stmt->free_result();
			$stmt->close();
			$conn->close();
			return $last_race_date;
		}
		
		// -- get last race # for a date for current meet (null) or for a specific date during meet
		public static function last_race(string $race_date = null, string $meet_filter)
		{
			$conn = new HIS\Connection();
			
			// -- get default value if $race_date is null
			if ($race_date === null) {
				$race_date = TB17::last_race_date($meet_filter);
			}
			
			$query = "SELECT MAX(race)
                  FROM tb17
                  WHERE race_date = ? AND $meet_filter
                  LIMIT 1
                 ";
			
			$stmt = $conn->db->prepare($query);
			$stmt->bind_param('s', $race_date);
			$stmt->execute();
			$stmt->store_result();
			if ($stmt->num_rows == 0) {
				return 0;
			}
			$stmt->bind_result($last_race);
			$stmt->fetch();
			$stmt->free_result();
			$stmt->close();
			$conn->close();
			return $last_race;
		}
		
		public static function getRaceInfo(string $previous_date,
				                           string $previous_race,
				                           string $previous_track_id) {
		    $conn = new HIS\Connection();
			$qry = "SELECT tb17_id
                    FROM tb17
                    WHERE race_date = ? AND
                       race         = ? AND
                       track_id     = ?
                    LIMIT 1";
			$stmt = $conn->db->prepare($qry);
			$stmt->bind_param('sis', $previous_date,
					                 $previous_race,
					                 $previous_track_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($tb17_id);

			if ($stmt->num_rows == 0) {
				$winnerObj = NULL;
			} else { 
				$stmt->fetch();
				$winnerObj = new TB17($tb17_id, $conn);
			}
			$stmt->close();
			$conn->close();
			return $winnerObj;

		}
		
		public static function findKeyRaces(int $search_limit, string $track_id) {
			$date = new DateTime();
			$date->sub(new DateInterval('P' . $search_limit . 'D'));
			$limit = $date->format('Y-m-d');
			$query = "SELECT *
              FROM
              (
                SELECT  previous_date,
                        previous_race,
                        previous_track_id,
                        COUNT(CONCAT(previous_date, previous_race, previous_track_id)) as wins
                 FROM tb17
                 WHERE previous_track_id IS NOT NULL AND previous_date > '$limit'
                 GROUP BY previous_date,
                          previous_race,
                          previous_track_id
              ) AS key_races
              
              WHERE (wins > 2 AND previous_track_id <> '$track_id')
                    ||
                    (wins > 1 AND previous_track_id = '$track_id')
              ORDER BY wins DESC,
                       previous_date DESC,
                       previous_track_id,
                       previous_race";
			return TB17::getResultArray($query);			
		}
		
		public static function getNextOutWinners(string $previous_date,
				                                 string $previous_race,
				                                 string $previous_track_id) {
		    $conn = new HIS\Connection();
		    $qry = "SELECT tb17_id
                FROM tb17
                WHERE previous_date      = ? AND
                      previous_race      = ? AND
                      previous_track_id  = ?
                ORDER BY race_date DESC, race
               ";
		    
		    $stmt = $conn->db->prepare($qry);
		    $stmt->bind_param('sis', $previous_date,
		    		                 $previous_race,
		    		                 $previous_track_id);
		    $stmt->execute();
		    $stmt->store_result();
		    $stmt->bind_result($tb17_id);
		    $nows = array();
		    while ($stmt->fetch()) {
		    	$nows[] = new TB17($tb17_id, $conn);
		    }
		    $stmt->close();
		    $conn->close();
		    return $nows;	    
		}
		
		public static function getIndividualMeetStats(string $table, string $name, string $meet_filter) {
			$conn = new HIS\Connection();
			$query = "SELECT
			             COUNT(*) as 'Wins',
			             SUM(IF(turf='FALSE',1,0)) as 'Dirt',
			             SUM(IF(turf='TRUE',1,0)) as 'Turf',
			             TRUNCATE(AVG(odds),1) as 'Odds',
			             TRUNCATE(AVG(IF(favorite='TRUE',odds,NULL)),1) as 'Favs Odds',
			             SUM(IF(distance<'8',1,0)) as 'Sprints',
			             SUM(IF(distance<'8' and turf='FALSE',1,0)) as 'Dirt Sprints',
			             SUM(IF(distance<'8' and turf='TRUE',1,0)) as 'Turf Sprints',
			             SUM(IF(distance>='8',1,0)) as 'Routes',
			             SUM(IF(distance>='8' and turf='FALSE',1,0)) as 'Dirt Routes',
			             SUM(IF(distance>='8' and turf='TRUE',1,0)) as 'Turf Routes'
			          FROM tb17
			          WHERE $table = ? AND $meet_filter
			          GROUP BY $table";
			$stmt = $conn->db->prepare($query);
			$stmt->bind_param('s', $name);
			if ($stmt->execute()) {
				$result = $stmt->get_result();
				if ($result->num_rows > 0) {
					return $result->fetch_assoc();
				} else {
					return array();
				}
			} else {
				return array();
			}
		}
		
		public static function getRaceSummaryInfo(int $tb17_id) {
			$conn = new HIS\Connection();
			$query = "SELECT race as 'Race',
				             track_condition as 'Condition',
				             turf as 'Turf',
				             horse as 'Horse',
				             time_of_race as 'Time',
				             IF(favorite='TRUE',CONCAT(CAST(odds as char),'<sup>*</sup>'),odds) as 'Odds',
				             race_flow as 'Flow',
				             post_position as 'Post',
				             field_size as 'Field',
				             jockey as 'Jockey',
				             trainer as 'Trainer',
				             comment as 'Comment'
			          FROM tb17
			          WHERE tb17_id = ?";
			$stmt = $conn->db->prepare($query);
			$stmt->bind_param('s', $tb17_id);
			if ($stmt->execute()) {
				$result = $stmt->get_result();
				if ($result->num_rows > 0) {
					return $result->fetch_assoc();
				} else {
					return array();
				}
			} else {
				return array();
			}
		}
		
		public static function getResultArray(string $query) {
			$conn = new HIS\Connection();
			$stmt = $conn->db->prepare($query);
			if ($stmt->execute()) {
				$result = $stmt->get_result();
				if ($result->num_rows > 0) {
					return $result->fetch_all(MYSQLI_ASSOC);
				} else {
					return array();
				}
			} else {
				return array();
			}
		}
	}
	
	