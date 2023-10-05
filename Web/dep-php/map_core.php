<?php

    include("steamauth/steamauth.php");
    include("steamauth/userInfo.php");

    $db_user    = "username";
    $db_pass    = "password";
    $db_host    = "hostname";
    $db_db      = "database";

    $page_name  = $_SERVER['REQUEST_URI'];
    $host_name  = $_SERVER['HTTP_HOST'];

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

    $db_db      = "miscreated_online";
    $steamId    = (isset($_SESSION['steamid'])?$_SESSION['steamid']:0);
    $steamId3   = (isset($_SESSION['steamid3'])?$_SESSION['steamid3']:0);
    $accountId  = (isset($_SESSION['accountid'])?$_SESSION['accountid']:0);
    $loggedIn   = (!isset($_SESSION['steamid']) || $_SESSION['steamid'] == 0?false:true);

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
?>
