<?php
/* 
 * @package Horse Information System (HIS)
 * @author Mike Kilmade <mkilmade.nycap.rr.com>
 * @version 0,001
 * 
 * @name getTrend
 * @parmam string $_GET['trend'] requested trend name
 * /

 /* set up common environment needed to process all trend code */
session_start();
require_once ('includes/config.inc.php');
require_once ('includes/connection.inc.php');
$conn = new Connection();
$trendName = $_GET['trend'];

/* call trend function requested */
require_once ("includes/trends/$trendName.inc.php");
$trendName($conn);

/* clean up environment */
$conn->close();
$trendName=null;

?>