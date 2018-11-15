<?php
/**
 *
 * @author mkilmade
 *        
 */
require_once('HisEntity.class.php');	
	
	class TB17 extends HisEntity {
		function __construct($id) {
			$this->table = "tb17";
			parent::__construct ( $id );
		}
	}
