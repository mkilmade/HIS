<?php
  session_start();
  require_once('includes/config.inc.php');
  include_once('includes/connection.inc.php');
  $conn = new Connection();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Tampa Bay Downs 2017/18 Search</title>
  <link href="jquery/jquery-ui.min.css" rel="stylesheet">
  <script src="jquery/jquery.js"></script>
  <script src="jquery/jquery.tablesorter.js"></script>
  <script src="jquery/jquery-ui.min.js"></script>
</head>
<body>
  <h1 id="body_title">Tampa Bay Downs 2017/18 Results Search</h1>
  <table>
    <tr>
      <td><a href='index.php'>Home</a></td>
    </tr>
  </table>
  <br />
  <form action="results.php" method="post">
  <p><strong>Choose Result Order:</strong><br />
  <select name="searchorder">
    <option value="horse">Horse</option>
    <option value="trainer">Trainer</option>
    <option value="jockey">Jockey</option>
  </select>
  <br><br>
  <strong>Race Information:</strong><br />
  <table>
    <tr>
        <th>Date</th>
        <th>Race</th>
        <th>Distance</th>
        <th>Turf?</th>
        <th>Sex</th>
        <th>Age</th>
        <th>Class</th>
        <th>Track Coniditon</th>
        <th>Field Size</th>
    </tr>
    <tr>
        <td><input type="date" id="race_date" name="race_date" value="2018-01-23"></td>
        <td>
          <select name="race">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
          </select>
        </td>
        <td>
          <select name="distance">
            <option value="4.5">4.5f</option>
            <option value="5">5f</option>
            <option value="5.5">5.5f</option>
            <option value="6" selected>6f</option>
            <option value="6.5">6.5f</option>
            <option value="7">7f/option>
            <option value="7.5">7.5f</option>
            <option value="8">Mile</option>
            <option value="8.06">1m 40yds</option>
            <option value="8.5">1m 1/16</option>
            <option value="9">1m 1/8</option>
            <option value="9.5">1m 3/16</option>
            <option value="10">1m 1/4</option>
            <option value="11">1m 3/8</option>
            <option value="13">1m 5/8</option>
            <option value="12">1m 1/2</option>
            <option value="16">2m</option>
          </select>
        </td>
       <td>
         <input type="radio" value="TRUE" id="turf" name="turf">Turf<br>
         <input type="radio" value="FALSE" id="turf" checked name="turf">Dirt
        </td>
        <td>
          <select name="sex">
            <option value="male" selected>Male</option>
            <option value="female">Female</option>
          </select>
        </td>
        <td>
          <select name="age">
            <option value="2">3</option>
            <option value="3">3</option>
            <option value="3+">3+</option>
            <option value="4">4</option>
            <option value="4+" selected>4+</option>
          </select>
        <td><input type="text" id="race_class" name="race_class"></td>
        <td>
          <select name="track_condition">
            <option value="Fast" selected>Fast</option>
            <option value="Good">Good</option>
            <option value="Sloppy">Sloppy</option>
            <option value="Muddy">Muddy</option>
            <option value="Firm">Firm</option>
            <option value="Soft">Soft</option>
            <option value="Yielding">Yielding</option>
            <option value="Hard">Hard</option>
            <option value="Frozen">Frozen</option>
            <option value="Wet Fast">Wet Fast</option>
          </select>
        </td>
        <td>
          <select name="field_size">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
            <option value="16">16</option>
            <option value="17">17</option>
            <option value="18">18</option>
            <option value="19">19</option>
            <option value="20">20</option>
          </select>
        </td>

      </tr>
    </table>

  <strong>Winner's Information:</strong><br />
  <table>
    <tr>
      <th>Horse</th>
      <th>Jockey</th>
      <th>Trainer</th>
      <th>Flow</th>
      <th>Post</th>
      <th>Odds</th>
      <th>Favorite</th>
      <th>Time</th>
      <th>Comment</th>
    </tr>
    <tr>
      <td><input type="text" id="horse" name="searchhorse"></td>
      <td><input type="text" id="jockey" name="searchjockey"></td>
      <td><input type="text" id="trainer" name="searchtrainer"></td>
      <td><input type="text" id="race_flow" name="race_flow"></td>
      <td>
          <select name="post_position">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
            <option value="16">16</option>
            <option value="17">17</option>
            <option value="18">18</option>
            <option value="19">19</option>
            <option value="20">20</option>
          </select>
        </td>
      <td><input type="text" id="odds" name="odds"></td>
      <td><input type="radio" value="TRUE" id="favorite" name="favorite">Favorite<br>
          <input type="radio" value="FALSE" id="favorite" checked name="favorite">Not Favorite</td>
      <td><input type="text" id="time_of_race" name="time_of_race"></td>
      <td><input type="text" id="comment" name="comment"></td>
    </tr>
  </table>
  <p><input type="submit" name="submit" value="Search"></p>
  </form>
  <?php
    echo "
    <script>
      $(document).ready(function() {
        $('#horse').autocomplete({source: JSON.parse(".json_encode($conn->class_extent('horse')).")});
        $('#trainer').autocomplete({source: JSON.parse(".json_encode($conn->class_extent('trainer')).")});
        $('#jockey').autocomplete({source: JSON.parse(".json_encode($conn->class_extent('jockey')).")});
        $('#race_class').autocomplete({source: JSON.parse(".json_encode($conn->distinct_category('race_class')).")});
        $('#race_flow').autocomplete({source: JSON.parse(".json_encode($conn->distinct_category('race_flow')).")});
        $('#race_date').datepicker({
          currentText: 'Today',
          defaultDate: 0,
          dateFormat: 'yy-mm-dd',
          showButtonPanel: true
        });
        $('#race_date').datepicker('setDate', new Date());
        $('#title').text('{$conn->defaults['meet_name']} (Search)');
        $('#body_title').text('{$conn->defaults['meet_name']}');
      });
    </script>
  ";
    $conn->close();
  ?>
</body>
</html>

