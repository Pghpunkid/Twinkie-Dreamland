<?php

	include('steam.php');

	$id = ($_GET['id']?$_GET['id']:"");
	$user = new SteamID($id);

	$url = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key=D4C3E0B7C6146CB3E7BF893097D4C6A0&steamids='.$user->convertToUInt64();
	$thisUser = file_get_contents($url);
	echo $thisUser;
?>
