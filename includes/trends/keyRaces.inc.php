<?php
require_once ('includes/envInit.inc.php'); // called by getTrend.php
function keyRaces($defaults) {
	$key_races = TB17::findKeyRaces ( 90, $defaults ['track_id'] );
	// -- build html key race table
	echo "
      <table id='keyTable' class='tablesorter' style='width:900px; font-size:14px'>
        <caption>Key Race Information for last 90 days (" . count ( $key_races ) . ")</caption>
        <thead>
          <th>Date</th>
          <th>Key Race #</th>
          <th>Track</th>
          <th>Wins</th>
          <th>Race Information (next out winners at NYRA/Tampa only)</th>
        </thead>
    <tbody>
    ";

	$n = 0;
	foreach ( $key_races as $key_race ) {
		$previous_date = $key_race ['previous_date'];
		$previous_race = $key_race ['previous_race'];
		$previous_track_id = $key_race ['previous_track_id'];
		$wins = $key_race ['wins'];

		// get key race data
		$key_raceObj = TB17::getRaceInfo ( $previous_date, $previous_race, $previous_track_id );
		if ($key_raceObj == NULL) {
			$key_race_data = "Key Race Winner: Sorry, only NYRA/Tampa races on file. Use race link for chart.";
		} else {
			$key_race_data = "Key Race Winner: ";
			$key_race_data .= "<b>";
			$key_race_data .= $key_raceObj->horse;
			$key_race_data .= "</b> : ";
			$key_race_data .= $key_raceObj->race_class;
			$key_race_data .= " : ";
			$key_race_data .= $key_raceObj->distance . ($key_raceObj->turf == "TRUE" ? ' t' : '');
			$key_race_data .= " : ";
			$key_race_data .= $key_raceObj->time_of_race;
		}

		// get last winner data from key race
		$nows = TB17::getNextOutWinners ( $previous_date, $previous_race, $previous_track_id );
		$key_race_data .= "<br>Next Out Winners:";

		foreach ( $nows as $winnerObj ) {
			$key_race_data .= "<br> - ";
			$key_race_data .= "<b>{$winnerObj->horse}</b> <sup>{$winnerObj->previous_finish_position}</sup> : ";
			$key_race_data .= "<a target='_blank' href='{$winnerObj->getChartUrl()}'>";
			$key_race_data .= "{$winnerObj->race_date} ";
			$key_race_data .= "{$winnerObj->track_id} <sup> R{$winnerObj->race} </sup> : ";
			$key_race_data .= "</a>";
			$key_race_data .= "{$winnerObj->race_class} : ";
			$key_race_data .= "{$winnerObj->distance}" . ($winnerObj->turf == "TRUE" ? ' t' : '') . " : ";
			$key_race_data .= "{$winnerObj->time_of_race}";
		}

		$chart_url = TB17::getEquibaseUrl($previous_date, $previous_track_id, $previous_race);
		echo "<tr>";
		echo "<td>$previous_date</td>";
		echo "<td><a target='_blank' href='$chart_url'>$previous_race</a></td>";
		echo "<td>$previous_track_id</td>";
		echo "<td>$wins</td>";
		echo "<td style='text-align: left'>$key_race_data</td>";
		echo "</tr>";
		$n = $n + 1;
		if ($n == 1000)
			break;
	}

	echo "
      </tbody></table>
    
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
} // function

?>
