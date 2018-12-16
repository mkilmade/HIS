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
	public function __construct($id = NULL, HIS\Connection $conn = NULL) {
		$this->bindings['table']   = "race_meet";
		$this->bindings['key_fld'] = "race_meet_id";
		$this->bindings['type']    = "i";
		parent::__construct ( $id, $conn);
	}
	
	public static function getTrackId(string $race_date) {
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
	
	public function meet_filter(string $comparefield) {
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
	
	public function getSummaryStats(array $qryParams) {
		$conn = new HIS\Connection();
		// -- build basic stats query
		$query="SELECT
                 COUNT(DISTINCT race_date) AS dates,
                 MAX(race_date) AS last_date,
                 COUNT(DISTINCT race_date, race) AS races,
                 SUM(IF(comment LIKE 'dead%',1,0)) AS deadheat,
                 TRUNCATE(AVG(post_position),1) AS avg_post,
                 SUM(IF(comment LIKE 'dead%',field_size/2,field_size)) AS sum_field_size,
                 TRUNCATE(AVG(IF(odds>0,odds,NULL)),2) AS avg_odds,
                 COUNT(DISTINCT trainer) AS trainers,
                 COUNT(DISTINCT jockey) AS jockeys,
                 COUNT(DISTINCT horse) AS horses
                FROM tb17
                WHERE {$this->meet_filter('race_date')} and horse <> ''"; // don't use if no horse enter yet
		
		// -- add WHERE clause
		if ($qryParams['turf'] <> '') {
			$query .= " AND turf='{$qryParams['turf']}'";
			if ($qryParams['distance'] <> 'total') {
				$query .= "AND distance ".($qryParams['distance']=='sprints' ? "<'8'" : ">='8'");
			}
		}
		// will only get 1 entry and 'LIMIT' 1 is more efficient
		$query .= " LIMIT 1";
		
		// -- run query
		$stmt = $conn->db->prepare($query);
		$stmt->execute();
		$result=$stmt->get_result();
		$stat_line=$result->fetch_assoc();
		$stmt->free_result();
		$stmt->close();
		$conn->close();
		
		return $stat_line;
	}
	
	public function getMultipleWinnerCount(array $qryParams) {
		$conn = new HIS\Connection();
		// get multiple winners count
		$qry="SELECT
               COUNT(*) as count
              FROM (SELECT COUNT(*) AS Wins,
                      horse
                    FROM tb17
                    WHERE {$this->meet_filter('race_date')} AND horse <> ''"; // don't use if no horse enter yet
		// -- add to derived WHERE clause
		if ($qryParams['turf'] <> '') {
			$qry .= " AND turf='{$qryParams['turf']}'";
		}
		$qry .= " GROUP BY horse) AS multi_winners_count
         WHERE Wins > '1' LIMIT 1";
		
		//echo "<br>$sum_field_size:$races";
		// -- run query
		$stmt = $conn->db->prepare($qry);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($multi_winners_count);
		$stmt->fetch();
		$stmt->free_result();
		$stmt->close();
		$conn->close();
		return $multi_winners_count;
	}
	
	function getTopTen(string $type, string $as_of_date, int $days) {
		$conn = new HIS\Connection();
		// -- build basic stats query
		if ($days>0) {
			$date= new DateTime($as_of_date);
			$date->sub(new DateInterval('P'.$days.'D'));
		} else {
			$date= new DateTime($as_of_date);
			$date->sub(new DateInterval('P1D'));
		}
		$date_diff=$date->format('Y-m-d');
		
		$query="SELECT
	                  $type as name,
	                  COUNT(*) as wins,
	                  SUM(IF(favorite='TRUE',1,0)) as favs,
	                  SUM(IF(turf='TRUE',1,0)) as turfs,
	                  AVG(IF(odds<>0.0,odds,NULL)) as avg_odds
	                FROM tb17
	                WHERE race_date > ? AND trainer <> '' AND jockey <> '' AND {$this->meet_filter('race_date')}
	                GROUP BY $type
	                ORDER BY wins DESC, $type
	                LIMIT 10
	              ";
	                  
       // -- run query
       $stmt = $conn->db->prepare($query);
       $stmt->bind_param('s', $date_diff);
       $stmt->execute();
       $result=$stmt->get_result();
       $rows=$result->fetch_all(MYSQLI_ASSOC);
       $stmt->free_result();
       $stmt->close();
       $conn->close();
       //print_r($rows);
       return $rows;
	}
	
	public function getRacesForDate(string $race_date) {
		$conn = new HIS\Connection();
		// -- get results for last date run
		$query = "SELECT
                    tb17_id
                  FROM tb17
                  WHERE race_date = ? AND {$this->meet_filter('race_date')}
                  ORDER BY race
                 ";
		
		$stmt = $conn->db->prepare($query);
		$stmt->bind_param('s', $race_date);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($tb17_id);
		$races = [];
		while($stmt->fetch()) {
			$races[] = new TB17($tb17_id);
		}		
		$stmt->free_result();
		$stmt->close();
		$conn->close();
		return $races;
	}

	public function getRaceDates()
	{
		$conn = new HIS\Connection();
		$race_dates = array();
		$query = "SELECT DISTINCT race_date
              FROM tb17
              WHERE {$this->meet_filter('race_date')}
              ORDER BY race_date";
		
		$stmt = $conn->db->prepare($query);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows == 0) {
			return $race_dates;
		}
		$stmt->bind_result($race_date);
		$day_of_meet = 0;
		while ($stmt->fetch()) {
			$day_of_meet = $day_of_meet + 1;
			$race_dates[$race_date] = $day_of_meet;
		}
		$stmt->free_result();
		$stmt->close();
		$conn->close();
		return $race_dates;
	}
	
	public function getWinCounts(string $type, string $name)
	{
		$conn = new HIS\Connection();
		$win_counts = array();
		$query = "SELECT DISTINCT race_date,
                     COUNT(*) as win_count
              FROM tb17
              WHERE $type = ? AND {$this->meet_filter('race_date')}
              GROUP BY race_date
              ORDER BY race_date";
		
		$stmt = $conn->db->prepare($query);
		$stmt->bind_param('s', $name);
		$stmt->execute();
		$stmt->store_result();
		if ($stmt->num_rows == 0) {
			return $win_counts;
		}
		$stmt->bind_result($race_date, $win_count);
		while ($stmt->fetch()) {
			$win_counts[$race_date] = $win_count;
		}
		$stmt->free_result();
		$stmt->close();
		$conn->close();
		return $win_counts;
	}
	
	
	
}

