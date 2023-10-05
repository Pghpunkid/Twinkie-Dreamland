<?php

	//This script take the SQLite DB from Miscreated and uploads it to MySQL.
	include('steam.php');
	include("servercfg.php");

	$mcdb = new SQLite3('backups/current/miscreated.db');
	$dbi = new MySQLi($mysqlDBHost, $mysqlDBUser, $mysqlDBPassword, $mysqlDBDatabase);

	$backupDateTime = file_get_contents("backup.log");
	$backupGUID = GUID();

	$dbi->query("UPDATE DB_SystemVars SET MaintenanceMode = 'Y', LastUpdate='$backupDateTime' WHERE ServerID=$serverID");
	$result = $dbi->query("INSERT INTO DB_BackupHistory SET GUID='$backupGUID',BackupDateTime='$backupDateTime', ServerID=$serverID");

	$tables = array(
		0 => "Characters",
		1 => "ClanMembers",
		2 => "Clans",
		3 => "Entities",
		4 => "Items",
		5 => "ServerAccountData",
		6 => "StructureParts",
		7 => "Structures",
		8 => "Tasks",
		9 => "Vehicles",
		10 => "Version",
		11 => "sqlite_sequence"
	);

	for ($t=0; $t<sizeof($tables); $t++) {
		$query = "SELECT * FROM ".$tables[$t];
		$result = $mcdb->query($query);

		while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
			$query = "INSERT INTO ".$tables[$t]." SET ";

			foreach($row as $key=>$value) {
				$query.=$key."='".$value."',";
			}
			$query.="DBBackupGUID='$backupGUID'";
			$result2 = $dbi->query($query);

			//Verify their name is in DB_SteamNames
			if ($tables[$t] == "Characters") {
				$accountID = $row['AccountID'];

				$query2 = "SELECT * FROM DB_SteamNames WHERE AccountID=".$accountID." AND TO_DAYS(SYSDATE()) - TO_DAYS(LastUpdate) <= 30";
				$result2 = $dbi->query($query2);

				if (!$result2) {
					echo $dbi->error."\n";
				}

				if ($result2->num_rows == 0) {
					$steamID3 = "[U:1:$accountID]";
					$user = new SteamID($steamID3);
					$steamID64 = $user->convertToUInt64();

					$url = 'https://api.twinkiedreamland.com/php/steamconvert.php?id='.$steamID64;
					$thisUser = json_decode(file_get_contents($url),true);
					if (isset($thisUser['response']['players'][0])) {
						$userName = $dbi->real_escape_string($thisUser['response']['players'][0]['personaname']);
						$query3 = "INSERT INTO DB_SteamNames SET SteamID = '$steamID64', SteamID3='$steamID3', AccountID=$accountID, Name='$userName', LastUpdate=SYSDATE()";
						$result3 = $dbi->query($query3);
					}
				}
			}
			else if ($tables[$t] == 'ClanMembers') {
				$accountID = $row['AccountID'];
				$query2 = "SELECT * FROM DB_SteamNames WHERE AccountID=".$accountID;
				$result2 = $dbi->query($query2);

				if (!$result2) {
					echo $dbi->error."\n";
				}

				if ($result2->num_rows == 0) {
					$steamID3 = "[U:1:$accountID]";
					$user = new SteamID($steamID3);
					$steamID64 = $user->convertToUInt64();

					$url = 'https://api.twinkiedreamland.com/php/steamconvert.php?id='.$steamID64;
					$thisUser = json_decode(file_get_contents($url),true);
					if (isset($thisUser['response']['players'][0])) {
						$userName = $dbi->real_escape_string($thisUser['response']['players'][0]['personaname']);
						$query3 = "INSERT INTO DB_SteamNames SET SteamID = '$steamID64', SteamID3='$steamID3', AccountID=$accountID, Name='$userName', LastUpdate=SYSDATE()";
						$result3 = $dbi->query($query3);
					}
				}
			}
		}
	}

	$dbi->query("UPDATE DB_SystemVars SET MaintenanceMode = 'N' WHERE ServerID=$serverID");

	function GUID()
	{
		if (function_exists('com_create_guid') === true)
		{
			return trim(com_create_guid(), '{}');
		}
		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}

?>
