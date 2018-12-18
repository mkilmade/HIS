<?php
/**
 * AJAX request handlers for HIS databace information
 * 
 * @name getHorseInfo
 *
 * @author Mike Kilmade
 * @version v0.0.1
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
    session_start();
    spl_autoload_register(function ($class) {
    	require_once "classes/". $class . '.class.php';
    });
    require_once('includes/config.inc.php');
    
    switch($_GET['type']) {
        case('next_race'):
        	$response = array('next_race' => TB17::last_race($_GET['race_date'], 
        	   												 $_SESSION['defaults']['meet_filter']) + 1);
            break;
        case('autocomplete'):
            $domain = $_GET['domain'];
            switch($domain) {
                case('race_class'):
                case('race_flow'):
                    $response =  TB17::getCategoryNames($_GET['name'],
                                                        $domain);
                    break;
                case('horse'):
                case('jockey');
                case('trainer'):
                	$response = ucfirst($domain)::getResourceNames($_GET['name']);
                	break;
                case('track_id'):
                case('previous_track_id'):
                	$response = getTracks($_GET['name']);
                	break;
                default:
                    $response =  array('error'   => 'Invalid autocorrect request',
                                       'request' => $domain);
            }
            break;
        case('last_win_data'):
            $response = getLastWinData($_GET['horse']);
            break;
        case('get_track_id'):
        	$response = Meet::getTrackId($_GET['race_date']);
        	break;
        case('individual_stats'):
        	$domain = $_GET['domain'];
        	switch($domain) {
        		case('trainer'):
        	    case('jockey'):
        	    	$response = getIndividualMeetStats($domain, $_GET['name'], 
        	    	                                            $_SESSION['defaults']['meet_filter']);
        	    	break;
        	    default:
        	    	$response =  array('html'   => '<b>Invalid domain: ' . $domain. '</b>');
        	}
        	break;
        case('race_summary'):
        	$response = getRaceSummary($_GET['race_id']);
        	break;
        case('next_out_winners'):
            $response = nextOutWinners($_GET['race_date'], 
                                       $_GET['race'],
                                       $_GET['track_id']);
            break;
        case('previous_next_out_winners_count'): // cuurently not used but could be usefull in future
        	$response = TB17::getPreviousNextOutWinnersCount($previous_date,
        	                                                 $previous_track_id,
        	                                                 $previous_race);
            break;
        default:
            $response =  array('error'  => 'Invalid request',
                               'request'=> $_GET['type']);
    }
    echo json_encode($response);
    
function getNextRaceNumber($race_date, $meet_filter) {
	return array('next_race' => TB17::last_race($race_date, $meet_filter) + 1);
}

function getTracks($id) {
	$trackObjs = Track::getTracks($id);
    $tracks = array();
    if (count($trackObjs) > 0) {
    	foreach ($trackObjs as $trackObj) {
            $tracks[] = array(
            		'label' => $trackObj->track_id,
            		'value' => $trackObj->track_id
            );
        }
    }
    return $tracks;
}
/**
 * Queries HIS database to find the last race horse won and returns the train and jockey of that win
 *
 * @param string $horse horse id
 * @return array keys of 'trainer' and 'jockey'
 * 
 */
function getLastWinData($horse) {
	$horseObj = new Horse($horse);
	return $horseObj->getLastWinData();
}

function nextOutWinners($previous_date,
                        $previous_race,
                        $previous_track_id) {
        $winnerObj = TB17::getRaceInfo($previous_date,
                        			   $previous_race,
                        			   $previous_track_id);
        if ($winnerObj == NULL) {
            $caption = "<b>Previous Race Specifics: Sorry, only NYRA/Tanpa races on file. Use race link for chart</b>";
        } else {
            $caption = "<b>Previous Race Specifics: ";
            $caption .= $winnerObj->horse;
            $caption .= " : ";
            $caption .= $winnerObj->race_class;
            $caption .= " : ";
            $caption .= $winnerObj->distance;
            $caption .= " : ";
            $caption .=($winnerObj->turf == "TRUE" ? 'Turf' : 'Dirt');
            $caption .= " : ";
            $caption .= $winnerObj->time_of_race;
            $caption .= "</b>";
        }
            
        $html="";
        $html .= "<table id='nowTable' class='tablesorter' style='margin: auto; width:800px; font-size:14px'>
                    <caption>$caption</caption>
                    <thead>
                        <th>Horse</th>
                        <th>Date</th>
                        <th>Race</th>
                        <th>Track</th>
                        <th>Prev Finish</th>
                        <th>Class</th>
                        <th>Distance</th>
                        <th>Surface</th>
                        <th>Time</td>
                    </thead>
                    <tbody>
        ";
        $nows = TB17::getNextOutWinners($previous_date,
        		                        $previous_race,
        		                        $previous_track_id);
        if (count($nows) == 0) {
            $html .= "<tr><td colspan=9>No next out winners found for this race</tr>";
        } else {
        	foreach ($nows as $winnerObj) {
                $html .= "<tr>";
                $html .= "<td>$winnerObj->horse</td>";
                $html .= "<td>$winnerObj->race_date</td>";
                $html .= "<td>$winnerObj->race</td>";
                $html .= "<td>$winnerObj->track_id</td>";
                $html .= "<td>$winnerObj->previous_finish_position</td>";
                $html .= "<td>$winnerObj->race_class</td>";
                $html .= "<td>$winnerObj->distance</td>";
                $html .= "<td>". ($winnerObj->turf == "TRUE" ? 'Turf' : 'Dirt') ."</td>";
                $html .= "<td>$winnerObj->time_of_race</td>";
                $html .= "</tr>";
            }
        }
        $html .= "</tbody></table>";
        return array( "html" => $html);
}

function getIndividualMeetStats($table, $name, $meet_filter) {
	$html = "";
	$html .= "<table style='border: 3px solid black; color: black;background-color: #F5F5DC;'>";
	$html .= "<caption style='text-align: center; font-weight: bold;'>'$name'</caption>";
	
	$stats = TB17::getIndividualMeetStats($table, $name, $meet_filter);
	
	foreach ($stats as $field => $value) {
		$html .= "<tr>";
		$html .= "  <td style='text-align:right; border-bottom: 1px dotted;'>$field:</td>";
		$html .= "  <td style='text-align:left; font-weight: bold; border-bottom: 1px dotted;'>$value</td>";
		$html .= "</tr>";
	}
	
	$html .= "</table>";
	return array("html" => $html);
}

function getRaceSummary($race_id) {
	$html = "";
	$html .= "<table style='border: 3px solid black; color: black; background-color: #F5F5DC;'>";
	$html .= "<caption style='text-align: center; font-weight: bold;'>Race Information</caption>";
	
	$race_summary = TB17::getRaceSummaryInfo($race_id);
	foreach ($race_summary as $field => $value) {
		$html .= "<tr>";
		$html .= "   <td style='text-align:right; border-bottom: 1px dotted;'>$field: </td>";
		$html .= "   <td style='text-align:left; font-weight: bold; border-bottom: 1px dotted;'>$value</td>";
		$html .= "</tr>";
	}
	$html .= "</table>";
	return array("html" => $html);
}
?>