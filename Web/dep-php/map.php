<?php
    $img = imagecreatefromjpeg('../images/orca_island.jpg');
    $black  = imagecolorallocate($img, 0, 0, 0);
    $blue   = imagecolorallocate($img, 0, 0, 255);
    $white  = imagecolorallocate($img, 255, 255, 255);
    $red    = imagecolorallocate($img, 255, 0, 0);
    $green  = imagecolorallocate($img, 0, 255, 0);

    $filter = ($_GET['f']?$_GET['f']:"");
    $subfilter = ($_GET['s']?$_GET['s']:"");

    if ($filter == 'bases') {
        showObjects('Structures', 'PlotSign');
    }
    else if ($filter == 'structures') {
        if ($subfilter == "") {
            doError("Specify a subfilter.");
        }
        else {
            showObjects('Structures', $subfilter);
        }
    }
    else if ($filter == 'vehicles') {
        if ($subfilter == "") {
            doError("Specify a subfilter.");
        }
        else {
            showObjects('Vehicles', $subfilter);
        }
    }
    else if ($filter == 'players') {
        if ($subfilter == "") {
            doError("Specify a subfilter.");
        }
        else {
            showObjects('Characters', $subfilter);
        }
    }
    else if ($filter == "") {
        doError("Specify a filter.");
    }
    else {
        doError("Specify a valid filter.");
    }

    header("Content-Type: image/jpeg");
    imagejpeg($img);
    imagedestroy($img);

    function showObjects($category, $param) {
        global $img, $white, $black, $green;

        $coordToMapScale = 0.238;
        $poiXAdj = 50;
        $poiZAdj = -41;

        $db = new SQLite3('../assets/miscreated.db');

        $query = "";
        if ($category == 'Vehicles') {
            $query = 'SELECT * FROM Vehicles';
            if ($param != "all") {
                $query .= ' WHERE ClassName="'.$param.'"';
            }
        }
        else if ($category == 'Structures') {
            $query = 'SELECT * FROM Structures';
            if ($param != "all") {
                $query .= ' WHERE ClassName="'.$param.'"';
            }
        }
        else if ($category == 'Characters') {
            $query = 'SELECT * FROM Characters';
            if ($param != "all") {
                $query .= ' WHERE AccountID='.$param;
            }
        }
        else {
            doError("Unsupported Category");
            return;
        }

        $result = $db->query($query);
        if (!$result) {
            doError("Could not perform that.");
        }

        $rowCount = 0;
        while ($data = $result->fetchArray(SQLITE3_ASSOC)) {
            $poiX = ($data['PosX'] * $coordToMapScale) + $poiXAdj;
            $poiY = 2028 - ($data['PosY'] * $coordToMapScale) + $poiZAdj;

            $user = false;
            if (isset($data['AccountID'])) {
                if ($data['AccountID'] != 0) {
                    $url = 'https://www.twinkiedreamland.com/php/steamconvert.php?id=[U:1:'.$data['AccountID'].']';
                    $user = json_decode(file_get_contents($url),true);
                }
            }

            $fontType = 5;
            //imagefilledrectangle($img, $poiX-15, $poiZ-15, $poiX+15, $poiZ+15, $blue);

            if ($param != 'all') {
                $text = $param;
            }
            else {
                if ($category == 'Vehicles')
                    $text = $data['ClassName'];
                if ($category == 'Characters')
                    $text = "";
            }
            if ($user !== false) {
                if (isset($user['response']['players'][0])) {
                    $text.=" (".$user['response']['players'][0]['personaname'].")";
                }
            }

            if ($category == 'Vehicles') {
                $hours = round(($data['AbandonTimer'] / 60) / 60);
                $mins = round(($data['AbandonTimer'] / 60) % 60);
                $text .= "(".$hours."h ".$mins."m)";
            }

            imagestring($img, $fontType, $poiX, $poiY+2, "+ ".$text, $black);
            imagestring($img, $fontType, $poiX, $poiY-2, "+ ".$text, $black);
            imagestring($img, $fontType, $poiX-2, $poiY, "+ ".$text, $black);
            imagestring($img, $fontType, $poiX+2, $poiY, "+ ".$text, $black);
            imagestring($img, $fontType, $poiX-2, $poiY+2, "+ ".$text, $black);
            imagestring($img, $fontType, $poiX-2, $poiY+2, "+ ".$text, $black);
            imagestring($img, $fontType, $poiX+2, $poiY-2, "+ ".$text, $black);
            imagestring($img, $fontType, $poiX+2, $poiY-2, "+ ".$text, $black);
            imagestring($img, $fontType, $poiX, $poiY, "+ ".$text, $green);

            imagestring($img, 5, $poiX, $poiY+16, "X:".$data['PosX']." Y:".$data['PosY'],$white);
            $rowCount++;
        }

        if ($rowCount == 0) {
            doError("0 Results");
        }
    }

    function doError($msg) {
        global $img, $white;
        imagefilledrectangle($img, 512, 924, 1536, 1124, $black);
        imagettftext($img, 36, 0, 522, 970, $white, '../fonts/Lato-Black.ttf', "Error");
        imagettftext($img, 36, 0, 522, 1026, $white, '../fonts/Lato-Black.ttf', $msg);
    }
?>
