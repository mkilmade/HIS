<?php

/** 
 * @author mkilmade
 * 
 */
spl_autoload_register(function ($class) {
	require_once $class . '.class.php';
});
class Defaults extends \HisEntity {

	/**
	 */
	public function __construct($id = 1, HIS\Connection $conn = NULL) {
		$this->bindings['table']   = "current_defaults";
		$this->bindings['key_fld'] = "current_defaults_id";
		$this->bindings['type']    = "i";
		parent::__construct ( $id, $conn = NULL );
	}

	// -- get current meet default values
	public static function get_his_defaults()
	{
		$query = "SELECT cd.race_meet_id,
                     rm.track_id,
                     rm.start_date,
                     rm.end_date,
                     rm.name AS meet_name,
                     cd.past_days,
                     cd.previous_track_id,
                     trk.site_url,
                     trk.scratches_url,
                     trk.name AS track_name
              FROM current_defaults AS cd
              INNER JOIN race_meet AS rm
                 USING (race_meet_id)
              INNER JOIN track as trk
                 USING (track_id)
              LIMIT 1
             ";
		
		$conn = new HIS\Connection();
		$stmt = $conn->db->prepare($query);
		$stmt->execute();
		$defaults = $stmt->get_result()->fetch_assoc();
		$defaults['meet_name'] = addslashes($defaults['meet_name']);
		$defaults['track_name'] = addslashes($defaults['track_name']);
		$defaults['meet_filter'] = "race_date >= '{$defaults['start_date']}' AND
                                race_date <= '{$defaults['end_date']}' AND
                                track_id = '{$defaults['track_id']}'";
		$stmt->free_result();
		$stmt->close();
		return $defaults;
	}
	
}

