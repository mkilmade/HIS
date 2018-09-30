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
    $query = "SELECT DISTINCT $category
          FROM tb17
          WHERE $category LIKE \"$name%\"
          ORDER BY $category";
    
    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($cat);
    
    $cats = array();
    while ($stmt->fetch()) {
        $cats[] = array(
            'label' => htmlentities($cat),
            'value' => htmlentities($cat)
        );
    }
    
    $stmt->free_result();
    $stmt->close();
    
    return $cats;
}

function getDomainEntryNames($searchname, $tablename, $conn) {
    $id = $tablename . "_id";
    
    // first list those matching 'shortcut' field
    $query = "SELECT $id, name, shortcut
      FROM $tablename
      WHERE shortcut LIKE \"$searchname%\"
      ORDER BY shortcut";
    
    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $shortcut);
    
    $names = array();
    if ($stmt->num_rows > 0) {
        while ($stmt->fetch()) {
            $names[] = array(
                'label' => htmlentities($shortcut . ' - ' . $name),
                'value' => htmlentities($name) // change to $id when normalized
            );
        }
    }
    $stmt->free_result();
    $stmt->close();
    
    // add those matching 'name' field
    $query = "SELECT $id, name
          FROM $tablename
          WHERE name LIKE \"$searchname%\"
          ORDER BY name";
    
    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name);
    
    if ($stmt->num_rows > 0) {
        while ($stmt->fetch()) {
            $names[] = array(
                'label' => htmlentities($name),
                'value' => htmlentities($name) // change to $id when normalized
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
    
}function previous_next_out_winners($previous_date,
                                    $previous_track_id,
                                    $previous_race,
                                    $conn) {
    $query = "SELECT
                COUNT(CONCAT(previous_date, previous_race, previous_track_id)) as wins
              FROM tb17
              WHERE previous_date = '$previous_date' AND
                    previous_track_id = '$previous_track_id' AND
                    previous_race = '$previous_race'";
    
    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($wins);
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();
                                        
    return array('wins' => $wins);
}
?>