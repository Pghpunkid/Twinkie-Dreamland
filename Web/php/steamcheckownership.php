<?php

	include('steam.php');

	$id = ($_GET['id']?$_GET['id']:"");
	$app = ($_GET['app']?$_GET['app']:"");
	$user = new SteamID($id);

	$url = 'http://api.steampowered.com/IPlayerService/IsPlayingSharedGame/v1/?key=D4C3E0B7C6146CB3E7BF893097D4C6A0&appid_playing='.$app.'&steamid='.$user->convertToUInt64();
	$thisUser = file_get_contents($url);
	echo $thisUser;
?>
