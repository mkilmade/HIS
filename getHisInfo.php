<?php
    session_start();
    require_once('includes/config.inc.php');
    require_once('includes/connection.inc.php');;
    $conn = new Connection();
    
    switch($_GET['type']) {
        case('next_race'):
            $response = getNextRaceNumber($_GET['race_date'], $conn);
            break;
        case('race_class'):
        case('race_flow'):
            $response =  getCategoryNames($_GET['name'], $_GET['entity_name'], $conn);
            break;
        case('horse'):
        case('jockey');
        case('trainer'):
            $response = getEntityNames($_GET['name'], $_GET['entity_name'], $conn);
            break;
        default:
            $response =  array('error' => 'Invalid request');
    }
    echo json_encode($response);
    
    $conn->close();
    
function getNextRaceNumber($race_date, &$conn) {
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

function getEntityNames($searchname, $tablename, $conn) {
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
?>