<?php
    require("php/steamauth/steamauth.php");
    require("php/steamauth/userInfo.php");

    include("core_functions.php");

    header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header("Pragma: no-cache"); // HTTP/1.0
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

    $systemTZ = 'America/New_York';

    $isDev = false;

    date_default_timezone_set($systemTZ);

    include('../dbconfig.php');

    $SERVER_ADMIN_LEVEL = 3;
    $SERVER_DEVELOPER_LEVEL = 4;
    $SERVER_LEVELS = array(0 => "Player", 1 => "Donator", 2 => "Moderator", 3 => "Server Admin", 4 => "Developer");

    $page_name    = $_SERVER['REQUEST_URI'];
    $host_name    = $_SERVER['HTTP_HOST'];

    $steamId            = (isset($_SESSION['steamid'])?$_SESSION['steamid']:0);
    $steamId3           = (isset($_SESSION['steamid3'])?$_SESSION['steamid3']:0);
    $accountId          = (isset($_SESSION['accountid'])?$_SESSION['accountid']:0);
    $loggedIn           = (!isset($_SESSION['steamid']) || $_SESSION['steamid'] == 0?false:true);
    $serverAdminLevel   = (isset($_SESSION['serverAdminLevel'])?$_SESSION['serverAdminLevel']:0);

    $universe = $steamId >> 56;
    $accountId = $steamId & 0xffffffff;
    $steamId3 = "[U:" . $universe . ":" . $accountId . "]";
    $_SESSION['accountid'] = $accountId;
    $_SESSION['steamid3'] = $steamId3;
    $_SESSION['steamid'] = $steamId;
    $_SESSION['logged_in'] = $loggedIn;

    $token = false;
    $serverAdminLevel = 0;

    //Get Server Admin Level
    if ($loggedIn) {
        $request = json_decode(file_get_contents("https://api.twinkiedreamland.com/api/v1.0/token.php?param=request"),true);
        if ($request['Status']) {
            $token = $request['Token'];
            $request = json_decode(file_get_contents("https://api.twinkiedreamland.com/api/v1.0/players.php?param=getPlayerServerAdminLevel&accountID=$accountId&token=$token"),true);

            if ($request['Status']) {
                $serverAdminLevel = $request['ServerAdminLevel'];
            }
        }
    }
    $_SESSION['serverAdminLevel'] = $serverAdminLevel;





?>
