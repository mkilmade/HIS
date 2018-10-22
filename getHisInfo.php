<?php
    session_start();
    require_once('includes/config.inc.php');
    require_once('includes/connection.inc.php');;
    $conn = new Connection();
    
    switch($_GET['type']) {
        case('next_race'):
            $response = getNextRaceNumber($_GET['race_date'], $conn);
            break;
        case('autocomplete'):
            $domain = $_GET['domain'];
            switch($domain) {
                case('race_class'):
                case('race_flow'):
                    $response =  getCategoryNames($_GET['name'], $domain, $conn);
                    break;
                case('horse'):
                case('jockey');
                case('trainer'):
                    $response = getDomainEntryNames($_GET['name'], $domain, $conn);
                    break;
                default:
                    $response =  array('error' => 'Invalid autocorrect request');
            }
            break;
        case('last_win_data'):
            $response = getLastWinData($_GET['horse'], $conn);
            break;
        case('next_out_winners'):
            $response = next_out_winners($_GET['race_date'], $_GET['race'], $_GET['track_id'], $conn);
            break;
        case('previous_next_out_winners'):
            $response = previous_next_out_winners($_GET['previous_date'],
                                                  $_GET['previous_track_id'],
                                                  $_GET['previous_race'],
                                                  $conn);
            break;
        default:
            $response =  array('error' => 'Invalid request');
    }
    echo json_encode($response);
    
    $conn->close();
    
function getNextRaceNumber($race_date, $conn) {
    return array('next_race' => $conn->last_race($race_date) + 1);
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

function getLastWinData($horse, $conn) {
    // get the horse parameter from URL
    $query = "SELECT trainer, jockey
           FROM tb17
          WHERE horse = ?
       ORDER BY race_date DESC
          LIMIT 1";
    
    $stmt = $conn->db->prepare($query);
    $stmt->bind_param('s', $horse);
    $stmt->execute();
    $lastWinData = $stmt->get_result()->fetch_assoc();
    if (count($lastWinData) == 0) {
        $lastWinData["trainer"] = "";
        $lastWinData["jockey"] = "";
    }
    $stmt->close();
    
    return $lastWinData;
    
}
function previous_next_out_winners($previous_date,
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
function next_out_winners($previous_date,
                          $previous_race,
                          $previous_track_id,
                          $conn) {
        $qry = "SELECT horse,
                       race_class,
                       distance,
                       time_of_race,
                       turf
                    FROM tb17
                    WHERE race_date = '$previous_date' and
                          race      = '$previous_race' and
                          track_id  = '$previous_track_id'
                    LIMIT 1";
        $stmt = $conn->db->prepare($qry);
        $stmt->execute();
        $assoc_data = $stmt->get_result()->fetch_assoc();
        $html = "<p>";
        if (count($assoc_data) == 0) {
              $html .= "Key Race Winner: Sorry, only NYRA races on file. Use race link for chart.";
        } else {
              $html = "Key Race Winner: ";
              $html .= "<b>";
              $html .= $assoc_data['horse'];
              $html .= "</b> : ";
              $html .= $assoc_data['race_class'];
              $html .= " : ";
              $html .= $assoc_data['distance'] . ($assoc_data['turf'] == "TRUE" ? ' t' : '');
              $html .= " : ";
              $html .= $assoc_data['time_of_race'];
        }
        $html .="</p>";
        $stmt->close();
        $stmt = "";
            
        $qry = "SELECT horse,
                       race_date,
                       race,
                       track_id,
                       race_class,
                       distance,
                       turf,
                       time_of_race,
                       previous_finish_position
                FROM tb17
                WHERE previous_date      = '$previous_date' and
                      previous_race      = '$previous_race' and
                      previous_track_id  = '$previous_track_id'
                ORDER BY race_date DESC, race
               ";
        
        $stmt = $conn->db->prepare($qry);
        $stmt->bind_param('sis', $previous_date,
                                 $previous_race,
                                 $previous_track_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($horse,
                           $race_date,
                           $race,
                           $track_id,
                           $race_class,
                           $distance,
                           $turf,
                           $time_of_race,
                           $previous_finish_position);
        $data = array();
        $html .= "<table border='1'>";
        while ($stmt->fetch()) {
            $html .= "<tr>";
            $html .= "<td>$horse <sup>$previous_finish_position</sup></td>";
            $html .= "<td>$race_date</td>";
            $html .= "<td>$race</td>";
            $html .= "<td>$track_id</td>";
            $html .= "<td>$race_class</td>";
            $html .= "<td>$distance" . ($turf == "TRUE" ? ' t' : '') ."</td>";
            $html .= "<td>$time_of_race</td>";
            $html .= "</tr>";
        }
        $html .= "</table>";
        $data["html"] = $html;
        $stmt->close();
        return $data;
}
?>