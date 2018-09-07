<?php

    $query = "SELECT 
                 COUNT(*) AS races, 
                 SUM(IF(previous_track_id  = '{$conn->defaults['track_id']}'   AND 
                            previous_date >= '{$conn->defaults['start_date']}' AND
                            previous_date <= '{$conn->defaults['end_date']}'
                    ,1,0)) As wins,
                 race_date
              FROM tb17
              WHERE {$conn->defaults['meet_filter']}
              GROUP BY race_date
              ORDER BY race_date DESC";

    $stmt = $conn->db->prepare($query);  
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($races,
                       $wins,
                       $race_date);

    echo "
      <div style='overflow-x:auto;float: left;'>
      <table id='previousMeetDateCountTable' class='tablesorter' style='width:200px; font-size:14px'>
        <caption>Winner's Previous Race At Meet</caption>
        <thead>
          <th>Date</th>
          <th>Wins per card</th>
        </thead>
    <tbody>
    ";

    while($stmt->fetch()) {
      if ($wins == 0 ) {
        continue;
      }
      $dt = substr($race_date,5,5);
      echo "<tr>";
      echo "<td>$dt</td>";
      echo "<td>$wins of $races</td>";
      echo "</tr>";
    }

    $stmt->free_result();
    $stmt->close();

    echo "</tbody></table></div>";

?>