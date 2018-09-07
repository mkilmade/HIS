<?php
    session_start();
    require_once('includes/config.inc.php');
    include_once('includes/connection.php');
    $conn = new Connection();

    $type=$_GET['type'];
    $name=urldecode($_GET['name']);

    // -- get last racing date and defaults
    $race_dates=$conn->getRaceDates();
    $days_in_meet=end($race_dates);
    $win_tally=0;
    $graphValues=array(0);
    $win_counts=$conn->getWinCounts($type, $name);
    foreach($race_dates as $race_date => $win_count) {
        if (isset($win_counts[$race_date])) {
            $win_tally=$win_tally+$win_counts[$race_date];
        }
        $graphValues[]=$win_tally;
    }
    unset($$win_count);
    // Add values to the graph
    $graphValuesx=array(0,80,23,11,190,245,50,80,111,240,55);
    $string = $_GET['type'].": ".$name."    wins: ".$win_tally;
    // Define .PNG image
    header("Content-type: image/png");
    $imgWidth=330;
    $imgHeight=315;
    // Create image and define colors
    $image=imagecreatetruecolor($imgWidth, $imgHeight);
    $colorWhite=imagecolorallocate($image, 255, 255, 255);
    $colorGrey=imagecolorallocate($image, 192, 192, 192);
    $colorBlue=imagecolorallocate($image, 0, 0, 255);
    $colorBlack=imagecolorallocate($image, 0, 0, 0);
    $colorBeige=imagecolorallocate($image, 245, 245, 220);
    imagefill($image, 0, 0, $colorWhite);
    imagefilledrectangle($image, 30, 0, 330, 300, $colorBeige);

    // Create dashed grid
    $style = array($colorGrey, IMG_COLOR_TRANSPARENT);
    imagesetstyle($image, $style);
    imagesetthickness($image, 2);
    for ($i=0; $i<22; $i++){
        if  ($i<21) imageline($image, 30, $i*15, 330, $i*15, IMG_COLOR_STYLED); // horizontial
        if($i<22 and $i>1) imageline($image, $i*15, 0, $i*15, 300, IMG_COLOR_STYLED); // vertical
    }
    imageline($image, 30, 300, 330, 0, IMG_COLOR_STYLED);

    // create generic comparison light lines and labels
    imagestring($image, 3, 205, 75, "1 win a day", $colorBlack);
    imageline($image, 30, 300, 330, 150, IMG_COLOR_STYLED);
    // angle text not supported in php v5.5
    //imagettftext($image, 10, 45, 150, 150, $colorBlack, './Arial.ttf', "1 win a day");
    imagestring($image, 3, 200, 175, "1 win every 2 days", $colorBlack);
    imageline($image, 30, 300, 180, 0, IMG_COLOR_STYLED);
    imagestring($image, 3, 100, 50, "2 wins a day", $colorBlack);

    // Create border around image
    imagesetthickness($image, 1);
    imageline($image, 30, 0, 30, 300, $colorBlack); // left edge
    imageline($image, 30, 0, 330, 0, $colorBlack); // top edge
    imageline($image, 329, 0, 329, 300, $colorBlack); // right edge
    imageline($image, 30, 300, 330, 300, $colorBlack); // bottom edge

   // Add in graph values
    imagesetthickness($image, 2);
    for ($i=0; $i<$days_in_meet; $i++){
        imageline($image, ($i*1.5)+30, (300-($graphValues[$i]*1.5)), (($i+1)*1.5)+30, (300-($graphValues[$i+1]*1.5)), $colorBlue);
    }

    // show total wins for individual at end of graph values
    imagestring($image, 3, ($days_in_meet*1.5)+25, 285-($graphValues[$days_in_meet]*1.5), "{$graphValues[$days_in_meet]}", $colorBlue);

    //$px = (imagesx($image) - 7.5 * strlen($string)) / 2;
    // label indices
    imagestring($image, 3, 225, 280, "as of Race Day", $colorBlack);
    imagestring($image, 3, 38, 301, "10", $colorBlack);
    imagestring($image, 3, 68, 301, "30", $colorBlack);
    imagestring($image, 3, 98, 301, "50", $colorBlack);
    imagestring($image, 3, 128, 301, "70", $colorBlack);
    imagestring($image, 3, 158, 301, "90", $colorBlack);
    imagestring($image, 3, 185, 301, "110", $colorBlack);
    imagestring($image, 3, 215, 301, "130", $colorBlack);
    imagestring($image, 3, 245, 301, "150", $colorBlack);
    imagestring($image, 3, 275, 301, "170", $colorBlack);
    imagestring($image, 3, 305, 301, "190", $colorBlack);

    imagestringup($image, 3, 35, 160, "Wins", $colorBlue);
    imagestring($image, 3, 7, 8, "190", $colorBlue);
    imagestring($image, 3, 7, 38, "170", $colorBlue);
    imagestring($image, 3, 7, 68, "150", $colorBlue);
    imagestring($image, 3, 7, 98, "130", $colorBlue);
    imagestring($image, 3, 7, 128, "110", $colorBlue);
    imagestring($image, 3, 13, 160, "90", $colorBlue);
    imagestring($image, 3, 13, 188, "70", $colorBlue);
    imagestring($image, 3, 13, 218, "50", $colorBlue);
    imagestring($image, 3, 13, 248, "30", $colorBlue);
    imagestring($image, 3, 13, 278, "10", $colorBlue);
    imagestring($image, 3, 115, 15, ucfirst($type).": $name", $colorBlue);

    // Output graph and clear image from memory
    imagepng($image);
    imagedestroy($image);
    $conn->close();
?>