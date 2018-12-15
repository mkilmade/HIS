<?php
namespace HIS;
/** 
 * @author mkilmade
 * 
 */

class Connection {

	// datebase connection.inc object used for queries/inserts/etc
	public $db;
	
	/**
	 * @name main constructor
	 */
	function __construct()
	{
		$ini = parse_ini_file('./secure/config.ini', true);
		date_default_timezone_set($ini['system']['timezone']);
		include ($ini['system']['mysql']);
		$this->db = $db;
	}
	
	// close database connection.inc object
	public function close()
	{
		$this->db->close();		// clog('Connection to database '.DB_NAME.' has been closed!');
	}
	
	public function execute_query(string $query)
	{
		$stmt = $this->db->stmt_init();
		if ($stmt->prepare($query)) {
			$status = $stmt->execute();
			if (!$status) {
				$status = "{$stmt->error} ({$stmt->errno})";
			}
		} else {
			$status = "{$stmt->error} ({$stmt->errno})";
		}
		$stmt->close();
		return $status;
	}
	
	// insert a new row into a table
	public function insert_row(array &$data, string $table)
	{
		$fields = "";
		$values = "";
		foreach ($data as $field => $value) {
			$value = $this->db->escape_string(trim($value));
			$fields = $fields . ($fields == "" ? "" : ", ") . $field;
			$values = $values . ($values == "" ? "" : ", ") . "'" . $value . "'";
		}
		//echo "<br>INSERT INTO $table ($fields) VALUES ($values)";
		$status = $this->execute_query("INSERT INTO $table ($fields) VALUES ($values)");
		if ($status) {
			$status = $this->db->insert_id;
		}
		return $status;
	}
	
	// update an entry in a table
	public function update_row(array &$data, string $table, $id)
	{
		$fldvals = "";
		foreach ($data as $field => $value) {
			$value = $this->db->escape_string(trim($value));
			$fldvals = $fldvals . ($fldvals == "" ? "" : ", ") . $field . "='" . $value . "'";
		}
		return $this->execute_query("UPDATE $table SET $fldvals WHERE " . $table . "_id='$id'");
	}
	
	
}

