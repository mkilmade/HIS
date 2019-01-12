<?php

/** 
 * @author mkilmade
 * 
 */
class Defaults extends \HisEntity {
	public static $table = "current_defaults";
	public static $id_fld = "current_defaults_id";
	
	// -- get current meet default values
	public static function get_his_defaults() {
		$query = "SELECT cd.race_meet_id,
                     rm.track_id,
                     rm.start_date,
                     rm.end_date,
                     rm.name AS meet_name,
                     cd.past_days,
                     cd.previous_track_id,
					 cd.age,
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

		$conn = new PDOConnection ();
		$stmt = $conn->pdo->prepare ( $query );
		$stmt->execute ( );
		
		$defaults = $stmt->fetch( PDO::FETCH_ASSOC );
		$defaults ['meet_name'] = addslashes ( $defaults ['meet_name'] );
		$defaults ['track_name'] = addslashes ( $defaults ['track_name'] );
		
		$defaults ['meet_filter'] = "race_date >= '{$defaults['start_date']}' AND
                                     race_date <= '{$defaults['end_date']}' AND
                                     track_id = '{$defaults['track_id']}'";
		return $defaults;
	}
}

