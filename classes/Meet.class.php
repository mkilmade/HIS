<?php
/**
 *
 * @author mkilmade
 *        
 */
class Meet extends \HisEntity {
	public static $table = "race_meet";
	public static $id_fld = "race_meet_id";

	public static function getTrackId(string $race_date) {

		$query = "SELECT
                    race_meet_id
                  FROM race_meet
                  WHERE start_date <= :start_date AND
                        end_date   >= :end_date
                  LIMIT 1";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ( [ 
				':start_date' => $race_date,
				':end_date' => $race_date
		] );
		$stmt->bindColumn ( 'race_meet_id', $race_meet_id );

		if ($stmt->fetch ( PDO::FETCH_BOUND )) {
			$rmObj = Meet::IdFactory ( $race_meet_id );
			$track_id = $rmObj->track_id;
		} else {
			$track_id = "";
		}
		return array (
				'track_id' => $track_id
		);

	}

	public function meet_filter(string $comparefield) {

		$filter = "$comparefield >= '{$this->start_date}' AND ";
		$filter .= "$comparefield <= '{$this->end_date}' AND ";
		$filter .= "track_id = '{$this->track_id}'";

		return $filter;

	}

	public function getClassTally() {

		$query = "SELECT
	                 COUNT(DISTINCT race_date,race) AS races,
					 AVG(odds) AS avg_odds,
					 STDDEV(odds) AS std_dev,
	                 race_class
	              FROM tb17
	              WHERE " . $this->meet_filter ( 'race_date' ) . "
	              GROUP By race_class
	              ORDER BY races DESC, race_class";
		return TB17::getResultArray ( $query );

	}

	public function getPreviousTrackWins() {

		$query = "SELECT previous_track_id,
		                     COUNT(*) as wins
	              FROM tb17
	              WHERE previous_track_id IS NOT NULL AND " . $this->meet_filter ( 'race_date' ) . "
	              GROUP By previous_track_id
	              ORDER BY wins DESC, previous_track_id";
		return TB17::getResultArray ( $query );

	}

	public function getFtsWins() {

		$query = "SELECT COUNT(*) as wins
	              FROM tb17
	              WHERE comment LIKE '%FTS%' AND " . $this->meet_filter ( 'race_date' ) . "
	              LIMIT 1";
		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ();
		$stmt->bindColumn ( 'wins', $wins );

		if (! $stmt->fetch ( PDO::FETCH_BOUND )) {
			$wins = 0;
		}
		return $wins;

	}

	public function getPreviouslyRanAtMeet() {

		$query = "SELECT
                   horse,
                   race_date
	              FROM tb17
	              WHERE " . $this->meet_filter ( 'race_date' ) . " AND
	                    previous_track_id = '{$this->track_id}' AND
	                    previous_date >= '{$this->start_date}'  AND
	                    previous_date <= '{$this->end_date}'
	              ORDER BY horse, race_date DESC";
		return TB17::getResultArray ( $query );

	}

	// function
	public function getPreviousRaceAtMeetPerCard() {

		$query = "SELECT
                 COUNT(*) AS races,
                 SUM(IF(previous_track_id  = '{$this->track_id}'  AND
                            previous_date >= '{$this->start_date}' AND
                            previous_date <= '{$this->end_date}'
                    ,1,0)) As wins,
                 race_date
              FROM tb17
              WHERE " . $this->meet_filter ( 'race_date' ) . "
              GROUP BY race_date
              ORDER BY race_date DESC";
		return TB17::getResultArray ( $query );

	}

	public function getMultipleWins() {

		$query = "SELECT
                wins, horse
              FROM (
                    SELECT count(*) AS wins, horse
                    FROM tb17
                    WHERE " . $this->meet_filter ( 'race_date' ) . "
                    GROUP BY horse
                   ) AS multi_winners
              WHERE wins > '1'
              ORDER BY wins, horse";
		return TB17::getResultArray ( $query );

	}

	public function getPreviousFinishTally() {

		$query = "SELECT
	                 COUNT(DISTINCT race_date,race) AS count,
	                 previous_finish_position
	              FROM tb17
	              WHERE " . $this->meet_filter ( 'race_date' ) . " AND
	                    previous_track_id = '{$this->track_id}' AND
	                    previous_date >= '{$this->start_date}' AND
	                    previous_date <= '{$this->end_date}'
	              GROUP BY previous_finish_position";
		return TB17::getResultArray ( $query );

	}

	public static function getMeets() {

		$query = "SELECT race_meet_id
		              FROM race_meet
		              ORDER BY start_date DESC
		             ";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ();
		$stmt->bindColumn ( 'race_meet_id', $race_meet_id );

		$meets = [ ];
		while ( $stmt->fetch ( PDO::FETCH_BOUND ) ) {
			$meets [] = Meet::IdFactory ( $race_meet_id );
		}
		return $meets;

	}

	public function getSummaryStats(array $qryParams) {

		// -- build basic stats query
		$query = "SELECT
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
		if ($qryParams ['turf'] != '') {
			$query .= " AND turf='{$qryParams['turf']}'";
			if ($qryParams ['distance'] != 'total') {
				$query .= "AND distance " . ($qryParams ['distance'] == 'sprints' ? "<'8'" : ">='8'");
			}
		}
		// will only get 1 entry and 'LIMIT' 1 is more efficient
		$query .= " LIMIT 1";

		// -- run query
		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );

	}

	public function getMultipleWinnerCount(array $qryParams) {

		// get multiple winners count
		$query = "SELECT
                    COUNT(*) as count
                  FROM (SELECT COUNT(*) AS Wins,
                         horse
                        FROM tb17
                        WHERE {$this->meet_filter('race_date')} AND horse <> ''"; // don't use if no horse enter yet
		                                                                                                               // -- add to derived WHERE clause
		if ($qryParams ['turf'] != '') {
			$query .= " AND turf='{$qryParams['turf']}'";
		}
		$query .= " GROUP BY horse) AS multi_winners_count
         WHERE Wins > '1' LIMIT 1";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ();
		$stmt->bindColumn ( 'count', $multi_winners_count );

		if (! $stmt->fetch ( PDO::FETCH_BOUND )) {
			$multi_winners_count = 0;
		}
		return $multi_winners_count;

	}

	function getTopTen(string $type, int $days, string $as_of_date = NULL) {
		
		// find race_date that is '$days' racing days ago 
		if ($days > 0 && $as_of_date == NULL) {
			$race_dates = $this->getRaceDates ();
			$count = count($race_dates);
			$as_of_date = array_keys($race_dates)[($days > $count) ? 0 : $count-$days];
		}
		
		$date = new DateTime ( $as_of_date );
		$date->sub ( new DateInterval ( 'P1D' ) );
		$date_diff = $date->format ( 'Y-m-d' );

		$query = "SELECT
	                  $type as name,
	                  COUNT(*) as wins,
	                  SUM(IF(favorite='TRUE',1,0)) as favs,
	                  SUM(IF(turf='TRUE',1,0)) as turfs,
	                  AVG(IF(odds<>0.0,odds,NULL)) as avg_odds
	                FROM tb17
	                WHERE race_date > :date_diff AND trainer <> '' AND jockey <> '' AND {$this->meet_filter('race_date')}
	                GROUP BY $type
	                ORDER BY wins DESC, $type
	                LIMIT 10
	              ";

		// -- run query
		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ( [ 
				':date_diff' => $date_diff
		] );
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );

	}

	public function getRacesForDate(string $race_date) {

		// -- get results for last date run
		$query = "SELECT
                    tb17_id
                  FROM tb17
                  WHERE race_date = :race_date AND {$this->meet_filter('race_date')}
                  ORDER BY race
                 ";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ( [ 
				':race_date' => $race_date
		] );
		$stmt->bindColumn ( 'tb17_id', $tb17_id );
		$races = [ ];
		while ( $stmt->fetch ( PDO::FETCH_BOUND ) ) {
			$races [] = TB17::IdFactory ( $tb17_id );
		}
		return $races;

	}

	public function getRaceDates() {

		$query = "SELECT DISTINCT race_date
              FROM tb17
              WHERE {$this->meet_filter('race_date')}
              ORDER BY race_date";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ();
		$stmt->bindColumn ( 'race_date', $race_date );
		$day_of_meet = 0;
		$race_dates = [ ];
		while ( $stmt->fetch ( PDO::FETCH_BOUND ) ) {
			$day_of_meet += 1;
			$race_dates [$race_date] = $day_of_meet;
		}
		return $race_dates;

	}

	public function getWinCounts(string $type, string $name) {

		$query = "SELECT DISTINCT race_date,
                     COUNT(*) as win_count
              FROM tb17
              WHERE $type = :name AND {$this->meet_filter('race_date')}
              GROUP BY race_date
              ORDER BY race_date";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ( [ 
				':name' => $name
		] );
		$stmt->bindColumn ( 'race_date', $race_date );
		$stmt->bindColumn ( 'win_count', $win_count );

		$win_counts = [ ];
		while ( $stmt->fetch ( PDO::FETCH_BOUND ) ) {
			$win_counts [$race_date] = $win_count;
		}
		return $win_counts;

	}
}

