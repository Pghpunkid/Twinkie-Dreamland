<?php

    session_start();

    $db_user    = "root";
    $db_pass    = "Hunter1234";
    $db_host    = "localhost";
    $db_db      = "miscreated_online";

    $page_name  = $_SERVER['REQUEST_URI'];
    $host_name  = $_SERVER['HTTP_HOST'];

    if (!isset($_SESSION['steamid'])) {
        header("Location: https://www.twinkiedreamland.com");
        return;
    }

    if ($host_name == "twinkiedreamland") {
        $_SESSION['steamid'] = "76561197993836697";
        $_SESSION['steamid3'] = "[U:1:33570969]";
        $_SESSION['accountid'] = "33570969";
        $steamprofile['personaname'] = 'Pghpunkid';

        $db_user = "root";
        $db_pass = "Hunter1234";
        $db_host = "ca1.pghnetwork.net";
        $db_db   = "miscreated_online";
    }

    $steamId    = (isset($_SESSION['steamid'])?$_SESSION['steamid']:0);
    $steamId3   = (isset($_SESSION['steamid3'])?$_SESSION['steamid3']:0);
    $accountId  = (isset($_SESSION['accountid'])?$_SESSION['accountid']:0);
    $loggedIn   = (!isset($_SESSION['steamid'])?false:true);

    $_SESSION['steamid'] = $steamId;
    $_SESSION['logged_in'] = $loggedIn;

    $universe   = $steamId >> 56;
    $accountId  = $steamId & 0xffffffff;
    $steamId3   = "[U:" . $universe . ":" . $accountId . "]";
    $_SESSION['accountid'] = $accountId;
    $_SESSION['steamid3'] = $steamId3;
    $coordToMapScale = 0.238;
    $poiXAdj = 50;
    $poiYAdj = -41;

    $img    = imagecreatefromjpeg('../images/orca_island.jpg');
    $black  = imagecolorallocate($img, 0, 0, 0);
    $blue   = imagecolorallocate($img, 0, 0, 255);
    $white  = imagecolorallocate($img, 255, 255, 255);
    $red    = imagecolorallocate($img, 255, 0, 0);
    $green  = imagecolorallocate($img, 0, 255, 0);

    $db = new MySQLi($db_host, $db_user, $db_pass, $db_db);

    $query = "SELECT GUID FROM DB_BackupHistory ORDER BY BackupDateTime DESC LIMIT 1;";
    $result = $db->query($query);
    if (!$result || $result->num_rows == 0) {
        //doError("Could not get data version.");
        header("Content-Type: image/jpeg");
        imagejpeg($img);
        imagedestroy($img);
        return;
    }
    $row = $result->fetch_assoc();
    $backupGUID = $row['GUID'];

    $query = "SELECT * FROM Structures WHERE AccountID=$accountId AND ClassName='PlotSign' AND DBBackupGUID='$backupGUID'";
    $result = $db->query($query);
    if (!$result) {
        doError("Could not perform that.\n".$query);
        header("Content-Type: image/jpeg");
        imagejpeg($img);
        imagedestroy($img);
        return;
    }

    if ($result->num_rows == 0) {
        doError("No Structures Found.\n".$query);
        header("Content-Type: image/jpeg");
        imagejpeg($img);
        imagedestroy($img);
        return;
    }

    $posX = 0;
    $posY = 0;
    $overallHealth = 0;
    $overallMaxHealth = 0;
    $overallObjects = 0;

    $plot = $result->fetch_assoc();
    $poiX = ($plot['PosX'] * $coordToMapScale) + $poiXAdj;
    $poiY = 2028 - ($plot['PosY'] * $coordToMapScale) + $poiYAdj;

    if ($poiX+400 > 2028) {
        $poiX = 2028 - 400;
    }

    if ($poiY+400 > 2028) {
        $poiY = 2028 - 400;
    }

    //imagestring($img, 3, $poiX, $poiY, "+ PlotSign",$white);
    createShadowTextTTF($img,'../fonts/Lato-Black.ttf', 16, "+ PlotSign", $poiX, $poiY, $black, $white);

    $img2 = imagecrop($img, ['x' => $poiX-200, 'y' => $poiY-200, 'width' => 400, 'height' => 400]);

    header("Content-Type: image/jpeg");
    imagejpeg($img2);
    imagedestroy($img);
    imagedestroy($img2);

    function doError($msg) {
        global $img, $white;
        imagefilledrectangle($img, 512, 924, 1536, 1124, $black);
        imagettftext($img, 16, 0, 522, 970, $white, '../fonts/Lato-Black.ttf', "Error");
        imagettftext($img, 16, 0, 522, 1026, $white, '../fonts/Lato-Black.ttf', $msg);
    }

    function createShadowTextTTF($img, $font, $fontSize, $string, $x, $y, $shadowColor, $color) {
        $xOffset = -5;
        $yOffset = 8;

        $xStart = ($xOffset+$x)-2;
        $yStart = ($yOffset+$y)-2;

        for ($posX=$xStart; $posX<=($xStart+5); $posX++) {
            for ($posY=$yStart; $posY<=($yStart+5); $posY++) {
                imagettftext($img, $fontSize, 0, $posX, $posY, $shadowColor, $font, $string);
            }
        }

        imagettftext($img, $fontSize, 0, ($xOffset+$x), ($yOffset+$y), $color, $font, $string);
    }
?>
