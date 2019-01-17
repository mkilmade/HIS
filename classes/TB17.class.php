<?php
/**
 *
 * @author mkilmade
 *        
 */
class TB17 extends \HisEntity {
	public static $table = "tb17";
	public static $id_fld = "tb17_id";

	public static function last_race_date(string $meet_filter = NULL) {

		$query = "SELECT MAX(race_date) AS last_race_date
		              FROM tb17" . ($meet_filter == NULL ? "" : " WHERE $meet_filter") . "
		              LIMIT 1
	                 ";
		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ();
		$stmt->bindColumn ( 'last_race_date', $last_race_date );

		if (! $stmt->fetch ( PDO::FETCH_BOUND )) {
			$last_race_date = '';
		}
		return $last_race_date;

	}

	// -- get last race # for a date for current meet (null) or for a specific date during meet
	public static function last_race(string $race_date = null, string $meet_filter) {

		// -- get default value if $race_date is null
		if ($race_date === null) {
			$race_date = TB17::last_race_date ( $meet_filter );
		}

		$query = "SELECT MAX(race) AS last_race
                  FROM tb17
                  WHERE race_date = :race_date AND $meet_filter
                  LIMIT 1
                 ";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ( [ 
				':race_date' => $race_date
		] );
		$stmt->bindColumn ( 'last_race', $last_race );

		if (! $stmt->fetch ( PDO::FETCH_BOUND )) {
			$last_race = 0;
		}
		return $last_race;

	}

	public static function getRaceInfo(string $previous_date, string $previous_race, string $previous_track_id) {

		$query = "SELECT tb17_id
                    FROM tb17
                    WHERE race_date = :race_date AND
                       race         = :race AND
                       track_id     = :track_id
                    LIMIT 1";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->bindValue ( ':race_date', $previous_date, PDO::PARAM_STR );
		$stmt->bindValue ( ':race', $previous_race, PDO::PARAM_INT );
		$stmt->bindValue ( ':track_id', $previous_track_id, PDO::PARAM_STR );
		$stmt->execute ();
		$stmt->bindColumn ( 'tb17_id', $tb17_id );

		$winnerObj = NULL;
		if ($stmt->fetch ( PDO::FETCH_BOUND )) {
			$winnerObj = TB17::IdFactory ( $tb17_id );
		}
		return $winnerObj;

	}

	public static function findKeyRaces(int $search_limit, string $track_id) {

		$date = new DateTime ();
		$date->sub ( new DateInterval ( 'P' . $search_limit . 'D' ) );
		$limit = $date->format ( 'Y-m-d' );
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
              
              WHERE (wins > 1 AND previous_track_id <> '$track_id')
                    ||
                    (wins > 1 AND previous_track_id = '$track_id')
              ORDER BY wins DESC,
                       previous_date DESC,
                       previous_track_id,
                       previous_race";
		return TB17::getResultArray ( $query );

	}

	// cuurently not used but could be usefull in future ; needs testing
	public static function getPreviousNextOutWinnersCount(string $previous_date, string $previous_track_id, int $previous_race) {

		$query = "SELECT
                   		COUNT(CONCAT(previous_date, previous_race, previous_track_id)) as wins
                  	FROM tb17
                  	WHERE previous_date = :previous_date AND
                    	  previous_track_id = :previous_track_id AND
                       	  previous_race = :previous_race
                    LIMIT 1";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->bindValue ( ':previous_date', $previous_date, PDO::PARAM_STR );
		$stmt->bindValue ( ':previous_track_id', $previous_track_id, PDO::PARAM_STR );
		$stmt->bindValue ( ':previous_race', $previous_race, PDO::PARAM_INT );
		$stmt->execute ();
		$stmt->bindColumn ( 'wins', $wins );

		if (! $stmt->fetch ( PDO::FETCH_BOUND )) {
			$wins = 0;
		}
		return array (
				'wins' => $wins
		);

	}

	public static function getNextOutWinners(string $previous_date, string $previous_race, string $previous_track_id) {

		$query = "SELECT tb17_id
                FROM tb17
                WHERE previous_date      = :previous_date AND
                      previous_race      = :previous_race AND
                      previous_track_id  = :previous_track_id
                ORDER BY race_date DESC, race
               ";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->bindValue ( ':previous_date', $previous_date, PDO::PARAM_STR );
		$stmt->bindValue ( ':previous_race', $previous_race, PDO::PARAM_INT );
		$stmt->bindValue ( ':previous_track_id', $previous_track_id, PDO::PARAM_STR );
		$stmt->execute ();
		$stmt->bindColumn ( 'tb17_id', $tb17_id );

		$nows = array ();
		while ( $stmt->fetch ( PDO::FETCH_BOUND ) ) {
			$nows [] = TB17::IdFactory ( $tb17_id );
		}
		return $nows;

	}

	public static function getIndividualMeetStats(string $table, string $name, string $meet_filter) {

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
			          WHERE $table = :name AND $meet_filter
			          GROUP BY $table
                      LIMIT 1";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ( [ 
				':name' => $name
		] );
		return $stmt->fetch ( PDO::FETCH_ASSOC );

	}

	public static function getRaceSummaryInfo(int $tb17_id) {

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
			          WHERE tb17_id = :tb17_id
                      LIMIT 1";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->bindValue ( ':tb17_id', $tb17_id, PDO::PARAM_INT );
		$stmt->execute ();
		return $stmt->fetch ( PDO::FETCH_ASSOC );

	}

	public static function getResultArray(string $query) {

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ();
		return $stmt->fetchAll ( PDO::FETCH_ASSOC );

	}

	public static function getCategoryNames(string $name, string $category) {

		$searchName = $name . "%";
		$query = "SELECT DISTINCT $category
              FROM tb17
              WHERE $category LIKE :searchName
              ORDER BY $category";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ( [ 
				':searchName' => $searchName
		] );
		$stmt->bindColumn ( $category, $cat );

		$cats = array ();
		while ( $stmt->fetch ( PDO::FETCH_BOUND ) ) {
			$cats [] = array (
					'label' => htmlentities ( $cat, ENT_NOQUOTES ),
					'value' => htmlentities ( $cat, ENT_NOQUOTES )
			);
		}
		return $cats;

	}

	public static function getBrowseRequestResults(array $filters, string $meet_filter) {

		$query = "SELECT tb17_id
                  FROM tb17
                  WHERE race_date LIKE :race_date AND
                        trainer   LIKE :trainer AND
                        jockey    LIKE :jockey AND
                        horse     LIKE :horse AND
                        $meet_filter
                  ORDER BY race_date DESC,
                           race DESC";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ( [ 
				':race_date' => $filters ['race_date'],
				':trainer' => $filters ['trainer'],
				':jockey' => $filters ['jockey'],
				':horse' => $filters ['horse']
		] );
		$stmt->bindColumn ( 'tb17_id', $tb17_id );

		$races = [ ];
		while ( $stmt->fetch ( PDO::FETCH_BOUND ) ) {
			$races [] = TB17::IdFactory ( $tb17_id );
		}
		return $races;

	}

	// get Equibase chart url for race
	public function getChartUrl() {

		return $this->getEquibaseUrl ( $this->race_date, $this->track_id, $this->race );

	}

	// get Equibase chart url for race
	public static function getEquibaseUrl(string $race_date, string $track_id, int $race = 1) {

		$date = new DateTime ( $race_date, new DateTimeZone ( 'America/New_York' ) );
		$url = "http://www.equibase.com/premium/chartEmb.cfm?";
		$url .= "track=$track_id";
		$url .= "&raceDate=" . $date->format ( "m/d/y" );
		$url .= "&cy=USA";
		$url .= "&rn=$race";
		return $url;

	}
}