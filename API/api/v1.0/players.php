<?php

    include("api-core.php");

    if ($token == "") {
        $return['Status'] = false;
        $return['Message'] = "Malformed or Missing Parameter - token";
        echo json_encode($return);
        exit(0);
    }

    if (!check_api_token($token)) {
        $return['Status'] = false;
        $return['Message'] = "Invalid Token";
        echo json_encode($return);
        exit(0);
    }

    $return['MaintenanceMode'] = false;
    if ($mcdb->checkMaintenanceMode()) {
        $return['Status'] = false;
        $return['Message'] = "Database Under Maintenance";
        $return['MaintenanceMode'] = true;
        echo json_encode($return);
        exit(0);
    }

    if ($cmd == "get") {
        if ($accountID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - accountID";
            echo json_encode($return);
            exit(0);
        }

        $player = $mcdb->getPlayer($accountID);
        unset($player['DBBackupGUID']);
        unset($player['DBCharacterID']);
        unset($player['CharacterID']);
        unset($player['GameServerID']);
        unset($player['PosZ']);
        unset($player['RotZ']);
        unset($player['MapName']);
        unset($player['Data']);
        unset($player['SelectedSlot']);
        unset($player['CreationDate']);
        $player['Gender'] = ($player['Gender'] == 0?"Male":"Female");

        $name = $mcdb->getPlayerSteamName($accountID);
        $player['Name'] = $name;

        $lastSeen = $mcdb->getPlayerLastSeen($accountID);
        if ($lastSeen != "--") {
            $lastSeenDate = new DateTime($lastSeen['LastSeen']);
            $player['LastSeen'] = $lastSeenDate->format($api_datetime_format);
        }
        else {
            $player['LastSeen'] = $lastSeen;
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Player'] = $player;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getAll") {
        $players = $mcdb->getAllPlayers();
        for ($p=0; $p<sizeof($players); $p++) {
            unset($players[$p]['DBBackupGUID']);
            unset($players[$p]['DBCharacterID']);
            unset($players[$p]['CharacterID']);
            unset($players[$p]['GameServerID']);
            unset($players[$p]['PosZ']);
            unset($players[$p]['RotZ']);
            unset($players[$p]['MapName']);
            unset($players[$p]['Data']);
            unset($players[$p]['SelectedSlot']);
            unset($players[$p]['CreationDate']);
            $players[$p]['Gender'] = ($players[$p]['Gender'] == 0?"Male":"Female");

            $name = $mcdb->getPlayerSteamName($players[$p]['AccountID']);
            $players[$p]['Name'] = $name;

            $lastSeen = $mcdb->getPlayerLastSeen($players[$p]['AccountID']);
            if ($lastSeen != "--") {
                $lastSeenDate = new DateTime($lastSeen);
                $players[$p]['LastSeen'] = $lastSeenDate->format($api_datetime_format);
            }
            else {
                $players[$p]['LastSeen'] = $lastSeen;
            }
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Players'] = $players;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getRecent") {
        $players = $mcdb->getRecentPlayers();
        for ($p=0; $p<sizeof($players); $p++) {
            unset($players[$p]['DBBackupGUID']);
            unset($players[$p]['DBCharacterID']);
            unset($players[$p]['CharacterID']);
            unset($players[$p]['GameServerID']);
            unset($players[$p]['PosZ']);
            unset($players[$p]['RotZ']);
            unset($players[$p]['MapName']);
            unset($players[$p]['Data']);
            unset($players[$p]['SelectedSlot']);
            unset($players[$p]['CreationDate']);
            $players[$p]['Gender'] = ($players[$p]['Gender'] == 0?"Male":"Female");

            $name = $mcdb->getPlayerSteamName($players[$p]['AccountID']);
            $players[$p]['Name'] = $name;

            $lastSeen = $mcdb->getPlayerLastSeen($players[$p]['AccountID']);
            if ($lastSeen != "--") {
                $lastSeenDate = new DateTime($lastSeen);
                $players[$p]['LastSeen'] = $lastSeenDate->format($api_datetime_format);
            }
            else {
                $players[$p]['LastSeen'] = $lastSeen;
            }
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Players'] = $players;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getSteamName") {
        if ($accountID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - accountID";
            echo json_encode($return);
            exit(0);
        }

        $steamName = $mcdb->getPlayerSteamName($accountID);
        if (!$steamName) {
            $return['Status'] = false;
            $return['Message'] = "Unable to get players steam name";
            echo json_encode($return);
            exit(0);
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Name'] = $steamName;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getPlayerServerAdminLevel") {
        if ($accountID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - accountID";
            echo json_encode($return);
            exit(0);
        }

        $serverAdminLevel = $mcdb->getPlayerServerAdminLevel($accountID);
        if (!$serverAdminLevel) {
            $return['Status'] = false;
            $return['Message'] = "Unable to get players server admin level";
            echo json_encode($return);
            exit(0);
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['ServerAdminLevel'] = $serverAdminLevel;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getPlayerClan") {
        if ($accountID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - accountID";
            echo json_encode($return);
            exit(0);
        }

        $clanID = $mcdb->getPlayerClan($accountID);

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Clan'] = $clanID;
        echo json_encode($return);
        exit(0);
    }
    if ($cmd == "getPlayerItems") {
        if ($accountID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - $accountID";
            echo json_encode($return);
            exit(0);
        }

        $playerItems = $mcdb->getPlayerItems($accountID);

        for ($p=0; $p<sizeof($playerItems); $p++) {
            unset($playerItems[$p]['ItemID']);
            unset($playerItems[$p]['DBBackupGUID']);
            unset($playerItems[$p]['DBItemID']);
            unset($playerItems[$p]['ParentGUID']);
            unset($playerItems[$p]['Data']['skin']);
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['PlayerItems'] = $playerItems;
        echo json_encode($return);
        exit(0);
    }
    else {
        $return['Status'] = false;
        $return['Message'] = "Invalid Option";
        echo json_encode($return);
        exit(0);
    }

?>
