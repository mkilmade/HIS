<?php
/**
 *
 * @author Mike Kilmade
 *
 */
require_once('HisEntity.class.php');
	
	class Horse extends HisEntity {
		function __construct($id) {
			$this->table = "horse";
			parent::__construct($id);
		}
	}
