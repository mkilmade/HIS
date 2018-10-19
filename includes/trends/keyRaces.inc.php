<?php

// called by getTrend.php
function keyRaces($conn)
{
    $date = new DateTime();
    $date->sub(new DateInterval('P90D'));
    $limit = $date->format('Y-m-d');
    $query = "SELECT *
              FROM
              (
                SELECT  previous_date,
                        previous_race,
                        previous_track_id,
                        COUNT(CONCAT(previous_date, previous_race, previous_track_id)) as wins
                 FROM tb17
                 WHERE previous_track_id IS NOT NULL AND previous_date > '$limit'
                 GROUP BY previous_date,
                          previous_race,
                          previous_track_id
              ) AS key_races
              
              WHERE (wins > 2 AND previous_track_id <> '{$conn->defaults['track_id']}')
                    ||
                    (wins > 1 AND previous_track_id = '{$conn->defaults['track_id']}')
              ORDER BY wins DESC,
                       previous_date DESC,
                       previous_track_id,
                       previous_race";
    //echo "<br>$query";
    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($previous_date, $previous_race, $previous_track_id, $wins);

    // -- build html key race table
    echo "
      <table id='keyTable' class='tablesorter' style='width:900px; margin: auto; font-size:16px'>
        <caption>Key Race Information for last 90 days ($stmt->num_rows)</caption>
        <thead>
          <th>Date</th>
          <th>Key Race #</th>
          <th>Track</th>
          <th>Wins</th>
          <th>Race Information (next out winners at NYRA only)</th>
        </thead>
    <tbody>
    ";

    $n = 0;
    while ($stmt->fetch()) {
        // build key race data
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
        $stmt2 = $conn->db->prepare($qry);
        $stmt2->execute();
        $assoc_data = $stmt2->get_result()->fetch_assoc();
        if (count($assoc_data) == 0) {
            $key_race_data = "Key Race Winner: Sorry, only NYRA races on file. Use race link for chart.";
        } else {
            $key_race_data = "Key Race Winner: ";
            $key_race_data .= "<b>";
            $key_race_data .= $assoc_data['horse'];
            $key_race_data .= "</b> : ";
            $key_race_data .= $assoc_data['race_class'];
            $key_race_data .= " : ";
            $key_race_data .= $assoc_data['distance'] . ($assoc_data['turf'] == "TRUE" ? ' t' : '');
            $key_race_data .= " : ";
            $key_race_data .= $assoc_data['time_of_race'];
        }
        $stmt2->close();

        // get last winner data from key race
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

        $stmt2 = $conn->db->prepare($qry);
        $stmt2->execute();
        $stmt2->store_result();
        $stmt2->bind_result($horse, $race_date, $race, $track_id, $race_class, $distance, $turf, $time_of_race, $previous_finish_position);
        $key_race_data .= "<br>Next Out Winners:";

        while ($stmt2->fetch()) {
            $key_race_data .= "<br> - ";
            $key_race_data .= "<b>$horse</b> <sup>$previous_finish_position</sup> : ";
            $key_race_data .= "$race_date ";
            $key_race_data .= "$track_id <sup> R$race </sup> : ";
            $key_race_data .= "$race_class : ";
            $key_race_data .= "$distance" . ($turf == "TRUE" ? ' t' : '') . " : ";
            $key_race_data .= "$time_of_race";
        }
        $stmt2->close();

        // $date = new DateTime($previous_date, new DateTimeZone('America/New_York'));
        $date = new DateTime($previous_date, new DateTimeZone(HIS_TIMEZONE));
        $chart_file = "http://www.equibase.com/premium/chartEmb.cfm?track=$previous_track_id&racedate=" . $date->format("m/d/y") . "&cy=USA&rn=$previous_race";
        echo "<tr>";
        echo "<td>$previous_date</td>";
        echo "<td><a target='_blank' href='$chart_file'>$previous_race</a></td>";
        echo "<td>$previous_track_id</td>";
        echo "<td>$wins</td>";
        echo "<td style='text-align: left'>$key_race_data</td>";
        echo "</tr>";
        $n = $n + 1;
        if ($n == 1000)
            break;
    }

    echo "
      </tbody></table></div>
    
      <script>
        $('#keyTable').tablesorter({
           widgets: ['zebra'],
           headers: {
             1: {
              sorter: false
             },
             4: {
              sorter: false
             }
           }
        });
      </script>
    ";

    $stmt->free_result();
    $stmt->close();
} // function

?>
