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
	public function __construct($id = 1, $conn = NULL) {
		$this->bindings['table']   = "current_defaults";
		$this->bindings['key_fld'] = "current_defaults_id";
		$this->bindings['type']    = "i";
		parent::__construct ( $id, $conn = NULL );
	}
}

