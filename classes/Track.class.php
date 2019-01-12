<?php
/** 
 * @author mkilmade
 * 
 */
class Track extends \HisEntity {
	public static $table = "track";
	public static $id_fld = "track_id";

	public static function getTracks(string $id) {

		$searchid = $id . "%";

		$query = "SELECT track_id
                  FROM track
                  WHERE track_id LIKE :searchid
                  ORDER BY track_id";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ( [ 
				':searchid' => $searchid
		] );
		$stmt->bindColumn ( 'track_id', $track_id );

		$trackObjs = [ ];
		while ( $stmt->fetch ( PDO::FETCH_BOUND ) ) {
			$trackObjs [] = Track::IdFactory ( $track_id );
		}
		return $trackObjs;

	}
}