<?php
/**
 *
 * @author Mike Kilmade
 *
 */
class Horse extends \Resource {
	public $horse_id;
	public function __construct($id = NULL, Connection $conn = NULL) {
		$this->bindings ['table'] = "horse";
		$this->bindings ['key_fld'] = "name";
		$this->bindings ['type'] = "s";
		parent::__construct ( $id, $conn );
	}
	public function getLastWinDatax() {
		// get the horse parameter from URL
		$conn = new Connection ();
		$query = "SELECT trainer, jockey
	              FROM tb17
	              WHERE horse = ?
	              ORDER BY race_date DESC
	              LIMIT 1";

		$stmt = $conn->db->prepare ( $query );
		$stmt->bind_param ( 's', $this->name );
		$stmt->execute ();
		$lastWinData = $stmt->get_result ()->fetch_assoc ();
		if (count ( $lastWinData ) == 0) {
			$lastWinData ["trainer"] = "";
			$lastWinData ["jockey"] = "";
		}
		$stmt->close ();
		$conn->close ();

		return $lastWinData;
	}
	public function getLastWinData() {
		// get the horse parameter from URL
		$pdo = new PDO("mysql:host=localhost;dbname=histest", 'mkilmade', 'albhaw29&');
		$query = "SELECT track_id, race_date, race, finish_position, trainer, jockey
	              FROM tb17
	              WHERE horse = :horse
	              ORDER BY race_date DESC
	              LIMIT 1";
		
		$stmt = $pdo->prepare ( $query );
		$stmt->execute ([':horse' => $this->name]);
		$lastWinData = $stmt->fetch(PDO::FETCH_ASSOC);
		if (count ( $lastWinData ) == 0) {
			$lastWinData ["track_id"] = "";
			$lastWinData ["race_date"] = "";
			$lastWinData ["race"] = "";
			$lastWinData ["finish_position"] = "";
			$lastWinData ["trainer"] = "";
			$lastWinData ["jockey"] = "";
		}
		$stmt= NULL;
		$pdo = NULL;
		
		return $lastWinData;
	}
}
