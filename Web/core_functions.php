<?php

    function calculateTimeLeft($seconds) {
        $hours = floor($seconds/3600);
        $minutes = floor($seconds/60) % 60;

        $timeStr = "";
        if ($hours > 0) {
            $timeStr .= $hours."h ";
        }
        $timeStr .= $minutes."m";
        return $timeStr;
    }

    function getSSLPage($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

?>
