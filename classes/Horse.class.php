<?php
/**
 *
 * @author Mike Kilmade
 *
 */
require_once('HisEntity.class.php');

	class Horse extends HisEntity{
		function __construct($id) {
			$this->table = "tb17";
			parent::__construct($id);
		}
	}
	
