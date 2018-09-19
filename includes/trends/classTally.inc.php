<?php

// called by getTrend.php
function classTally($conn)
{
    $query = "SELECT 
                 count(DISTINCT race_date,race) AS races,
                 race_class
              FROM tb17
              WHERE {$conn->defaults['meet_filter']}
              GROUP By race_class
              ORDER BY races DESC, race_class";

    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($races, $class);

    echo "
      <table id='classTable' class='tablesorter' style='width:200px; margin: auto; font-size:14px'>
        <caption>Class Breakdown for Meet ($stmt->num_rows)</caption>
        <thead>
          <th>Class</th>
          <th>Races</th>
        </thead>
    <tbody>
    ";

    $total = 0;
    while ($stmt->fetch()) {
        $total += $races;
        echo "<tr>";
        echo "<td style='text-align:left;'>$class</td>";
        echo "<td>$races</td>";
        echo "</tr>";
    }

    echo "
        <tr><td>Total</td><td>$total</td></tr>
        </tbody></table>

        <script>
            $('#classTable').tablesorter({
                widgets: ['zebra']
            });
        </script>
        ";

    $stmt->free_result();
    $stmt->close();
} // function

?> 