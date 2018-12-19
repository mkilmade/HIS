<?php
/*
 * @package Horse Information System (HIS)
 * @author Mike Kilmade <mkilmade.nycap.rr.com>
 * @version 0,001
 *
 * @name getTrend
 * @parmam string $_GET['trend'] requested trend name
 * /
 *
 * /* set up common environment needed to process all trend code
 */
require_once('session.php');
$trendName = $_GET['trend'];

/* call trend function requested */
require_once ("includes/trends/$trendName.inc.php");
$trendName($_SESSION['defaults']);
?>