<?php
    session_start();

    include("class.MCDB.php");

    $db_user = "username";
	$db_pass = "password";
	$db_host = "hostname";
	$db_db   = "database";

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

        $db_user = "username";
        $db_pass = "password";
        $db_host = "hostname";
        $db_db   = "database";
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

    /*$query = "SELECT * FROM ".
             "Structures S, ClanMembers M ".
             "WHERE ".
             "M.ClanID=(".
             "  SELECT ClanID ".
             "  FROM ClanMembers ".
             "  WHERE ".
             "      AccountID=$accountId AND ".
             "      DBBackupGUID='$backupGUID'".
             ") AND ".
             "S.AccountID=M.AccountID AND ".
             "S.ClassName='PlotSign' AND ".
             "S.DBBackupGUID='$backupGUID'";

    $result = $db->query($query);
    if (!$result || $result->num_rows == 0) {
        doError("Could not perform that.\n".$query);
        header("Content-Type: image/jpeg");
        imagejpeg($img);
        imagedestroy($img);
        return;
    }

    $rowCount = 0;
    while ($data = $result->fetch_array()) {
        $poiX = ($data['PosX'] * $coordToMapScale) + $poiXAdj;
        $poiY = 2028 - ($data['PosY'] * $coordToMapScale) + $poiYAdj;

        //imagefilledrectangle($img, $poiX-25, $poiY-25, $poiX+25, $poiY+25, $black);
        //imagefilledrectangle($img, $poiX-20, $poiY-20, $poiX+20, $poiY+20, $white);
        $rowCount++;
    }*/

    $query = "SELECT * FROM ".
             "Structures S, ClanMembers M ".
             "WHERE ".
             "M.ClanID=(".
             "  SELECT ClanID ".
             "  FROM ClanMembers ".
             "  WHERE ".
             "      AccountID=$accountId AND ".
             "      DBBackupGUID='$backupGUID'".
             ") AND ".
             "S.AccountID=M.AccountID AND ".
             "S.ClassName='PlotSign' AND ".
             "S.DBBackupGUID='$backupGUID'";

    $result = $db->query($query);
    if (!$result || $result->num_rows == 0) {
        doError("Could not perform that.\n".$query);
        header("Content-Type: image/jpeg");
        imagejpeg($img);
        imagedestroy($img);
        return;
    }

    $rowCount = 0;

    $minX = 2028;
    $maxX = 0;
    $minY = 2028;
    $maxY = 0;

    $minXPerson = "";
    $maxXPerson = "";
    $minYPerson = "";
    $maxYPerson = "";

    while ($data = $result->fetch_array()) {
        $text = "+ PlotSign (".$data['MemberName'].")";
        $poiX = ($data['PosX'] * $coordToMapScale) + $poiXAdj;
        $poiY = 2028 - ($data['PosY'] * $coordToMapScale) + $poiYAdj;

        if ($poiX < $minX) {
            $minX = $poiX;
            $minXPerson = $data['MemberName'];
        }
        if ($poiX > $maxX) {
            $maxX = $poiX;
            $maxXPerson = $data['MemberName'];
        }
        if ($poiY < $minY) {
            $minY = $poiY;
            $minYPerson = $data['MemberName'];
        }
        if ($poiY > $maxY) {
            $maxY = $poiY;
            $maxYPerson = $data['MemberName'];
        }

        createShadowTextTTF($img,'../fonts/Lato-Black.ttf', 16, $text, $poiX, $poiY, $black, $white);
        $rowCount++;
    }

    $xDistance = $maxX - $minX;
    $yDistance = $maxY - $minY;

    $size = ($xDistance > $yDistance?$xDistance:$yDistance)+300;

    if ($maxX > 2028) {
        $minX = 2028 - $size;
        $maxX = 2028;
    }

    if ($minX < 0) {
        $minX = 0;
        $maxX = 0+$size;
    }

    if ($maxY > 2028) {
        $minY = 2028 - $size;
        $maxY = 2028;
    }

    if ($minY < 0) {
        $minY = 0;
        $maxY = 0+$size;
    }

    $img2 = imagecrop($img, ['x' => $minX-100, 'y' => $minY-100, 'width' => $size, 'height' => $size]);

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
