<?php

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
		$ini = parse_ini_file('../secure/config.ini', true);
		date_default_timezone_set($ini['system']['timezone']);
		include (".".$ini['system']['mysql']);
		$this->db = $db;
	}
	
	// close database connection.inc object
	public function close()
	{
		$this->db->close();
		// clog('Connection to database '.DB_NAME.' has been closed!');
	}
}

