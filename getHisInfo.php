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
    	include "classes/". $class . '.class.php';
    });
    require_once('includes/config.inc.php');
    require_once('includes/connection.inc.php');;
    $conn = new Connection();
    
    switch($_GET['type']) {
        case('next_race'):
        	$response = getNextRaceNumber($_GET['race_date'], $conn->defaults['meet_filter']);
            break;
        case('autocomplete'):
            $domain = $_GET['domain'];
            switch($domain) {
                case('race_class'):
                case('race_flow'):
                    $response =  getCategoryNames($_GET['name'],
                                                  $domain,
                                                  $conn);
                    break;
                case('horse'):
                case('jockey');
                case('trainer'):
                	$response = getDomainEntryNames($_GET['name'],
                	                                $domain,
                	                                $conn);
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
        	$response = getTrackId($_GET['race_date']);
        	break;
        case('individual_stats'):
        	$response = getIndividualMeetStats($_GET['domain'], $_GET['name'], $conn->defaults['meet_filter']);
        	break;
        case('next_out_winners'):
            $response = nextOutWinners($_GET['race_date'], 
                                       $_GET['race'],
                                       $_GET['track_id']);
            break;
        case('previous_next_out_winners'): // cuurently not used but could be usefull in future
            $response = previousNextOutWinners($_GET['previous_date'],
                                               $_GET['previous_track_id'],
                                               $_GET['previous_race'],
                                               $conn);
            break;
        default:
            $response =  array('error'  => 'Invalid request',
                               'request'=> $_GET['type']);
    }
    echo json_encode($response);
    
    $conn->close();
    
function getNextRaceNumber($race_date, $meet_filter) {
	return array('next_race' => TB17::last_race($race_date, $meet_filter) + 1);
}

function getCategoryNames($name, $category, $conn) {
    $searchname=$name."%";
    $query = "SELECT DISTINCT $category
              FROM tb17
              WHERE $category LIKE ?
              ORDER BY $category";
    
    $stmt = $conn->db->prepare($query);
    $stmt->bind_param('s', $searchname);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($cat);
    
    $cats = array();
    while ($stmt->fetch()) {
        $cats[] = array(
            'label' => htmlentities($cat, ENT_NOQUOTES),
            'value' => htmlentities($cat, ENT_NOQUOTES)
        );
    }
    
    $stmt->free_result();
    $stmt->close();
    
    return $cats;
}

function getDomainEntryNames($name, $tablename, $conn) {
    $id = $tablename . "_id";
    $searchname=$name."%";
    // first list those matching 'shortcut' field
    $query = "SELECT $id, name, shortcut
              FROM $tablename
              WHERE shortcut LIKE ?
              ORDER BY shortcut";
    
    $stmt = $conn->db->prepare($query);
    $stmt->bind_param('s', $searchname);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $shortcut);
    
    $names = array();
    if ($stmt->num_rows > 0) {
        while ($stmt->fetch()) {
            $names[] = array(
                'label' => htmlentities($shortcut . ' - ' . $name, ENT_NOQUOTES),
                'value' => htmlentities($name, ENT_NOQUOTES) // change to $id when normalized
            );
        }
    }
    $stmt->free_result();
    $stmt->close();
    
    // add those matching 'name' field
    $query = "SELECT $id, name
              FROM $tablename
              WHERE name LIKE ?
              ORDER BY name";
    
    $stmt = $conn->db->prepare($query);
    $stmt->bind_param('s', $searchname);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name);
    
    if ($stmt->num_rows > 0) {
        while ($stmt->fetch()) {
            $names[] = array(
                'label' => htmlentities($name, ENT_NOQUOTES),
                'value' => htmlentities($name, ENT_NOQUOTES) // change to $id when normalized
            );
        }
    }
    $stmt->free_result();
    $stmt->close();
    
    return $names;
    
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
 * @param Connection $conn HIS database connection object instance
 * @return array keys of 'trainer' and 'jockey'
 * 
 */
function getLastWinData($horse) {
	$horseObj = new Horse($horse);
	return $horseObj->getLastWinData();
}
function getTrackId($race_date) {
	return Meet::getTrackId($race_date);
}

// cuurently not used but could be usefull in future
function previousNextOutWinners($previous_date,
                                $previous_track_id,
                                $previous_race,
                                $conn) {
        $query = "SELECT
                   COUNT(CONCAT(previous_date, previous_race, previous_track_id)) as wins
                  FROM tb17
                  WHERE previous_date = ? AND
                        previous_track_id = ? AND
                        previous_race = ?";
        
        $stmt = $conn->db->prepare($query);
        $stmt->bind_param('ssi', $previous_date,
                                 $previous_track_id,
                                 $previous_race);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($wins);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();
        
        return array('wins' => $wins);
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
?>