<?php
require_once ('includes/envInit.inc.php');
header ( 'Location: index.php?reset_session=1' );

if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	$post = $_POST;
	unset ( $post ['submit'] );
	// get current values for comparisons via object
	$current = Defaults::IdFactory( 1 );
	foreach ( $post as $field => $value ) {
		if ($field == 'current_defaults_id') {
			unset ( $post [$field] );
			continue;
		}
		if ($value == $current->$field) {
			unset ( $post [$field] );
			continue;
		} else if ($field == 'race_meet_id') {
			// don't use track conditions for newly selected race meet
			unset ( $_SESSION ['dirt_track_condition'] );
			unset ( $_SESSION ['turf_track_condition'] );
		} // race_meet_id check 'if'
	} // value check 'else'

	if ((count ( $post )) > 0) {
		$status = $current->update_entry ( $post );
		if (! $status) {
			// log warning
			trigger_error ( "Warning -> Update " . $status, E_USER_WARNING );
		} // $status if
	} // count if
} // REQUEST_METHOD if
?>