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

		$query = "SELECT *
                  FROM track
                  WHERE track_id LIKE :searchid
                  ORDER BY track_id";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ( [ 
				':searchid' => $searchid
		] );

		$trackObjs = [ ];
		while ( $to = $stmt->fetchObject ( __CLASS__ ) ) {
			$trackObjs [] = $to;
		}
		return $trackObjs;

	}
}