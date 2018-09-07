<?php

    $query = "SELECT 
                 horse,
                 race_date
              FROM tb17
              WHERE {$conn->defaults['meet_filter']} AND 
                    previous_track_id = '{$conn->defaults['track_id']}' AND 
                    previous_date >= '{$conn->defaults['start_date']}' AND 
                    previous_date <= '{$conn->defaults['end_date']}'
              ORDER BY horse, race_date DESC";

    $stmt = $conn->db->prepare($query);  
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($horse,
                       $race_date);

    echo "
      <div style='overflow-x:auto;float: left;'>
      <table id='previouslyRanAtMeetWinTable' class='tablesorter' style='width:300px; font-size:14px'>
        <caption>Previous Race At Meet ($stmt->num_rows)</caption>
        <thead>
          <th>Horse</th>
          <th>Date</th>
        </thead>
    <tbody>
    ";

    while($stmt->fetch()) {
      $dt = substr($race_date,5,5);
      echo "<tr>";
      echo "<td>$horse</td>";
      echo "<td>$dt</td>";
      echo "</tr>";
    }

    $stmt->free_result();
    $stmt->close();

    echo "</tbody></table></div>";

?>