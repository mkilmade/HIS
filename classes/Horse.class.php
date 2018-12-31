<?php
/**
 *
 * @author Mike Kilmade
 *
 */
class Horse extends \Resource {
	const TABLE = "horse";
	const ID_FLD = "name";
	public function getLastWinData() {
		// get the horse parameter from URL
		$query = "SELECT tb17_id
	              FROM tb17
	              WHERE horse = :horse
	              ORDER BY race_date DESC
	              LIMIT 1";

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->bindValue ( ':horse', $this->name, PDO::PARAM_STR );
		$stmt->execute ();
		$stmt->bindColumn ( 'tb17_id', $tb17_id );

		// return tb17 object
		if ($stmt->fetch ( PDO::FETCH_BOUND )) {
			return TB17::IdFactory ( $tb17_id );
		} else {
			return NULL;
		}
	}
}
