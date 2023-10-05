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
        $clans = $mcdb->getClans();

        for ($c=0; $c<sizeof($clans); $c++) {
            unset($clans[$c]['DBClanID']);
            unset($clans[$c]['DBBackupGUID']);
            unset($clans[$c]['GameServerID']);
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Clans'] = $clans;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getClan") {
        if ($clanID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - clanID";
            echo json_encode($return);
            exit(0);
        }

        $clan = $mcdb->getClan($clanID);

        unset($clan['DBClanMemberID']);
        unset($clan['DBBackupGUID']);
        unset($clan['GameServerID']);

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Clan'] = $clan;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getClanMembers") {
        if ($clanID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - clanID";
            echo json_encode($return);
            exit(0);
        }

        $clanMembers = $mcdb->getClanMembers($clanID);

        for ($c=0; $c<sizeof($clanMembers); $c++) {
            unset($clanMembers[$c]['DBClanMemberID']);
            unset($clanMembers[$c]['ClanMemberID']);
            unset($clanMembers[$c]['DBBackupGUID']);
            unset($clanMembers[$c]['GameServerID']);
            unset($clanMembers[$c]['CanAlterMembers']);
            unset($clanMembers[$c]['CanAlterParts']);
            unset($clanMembers[$c]['CanAlterLocks']);
            unset($clanMembers[$c]['CanAlterPower']);
            $clanMembers[$c]['MemberName'] = $mcdb->getPlayerSteamName($clanMembers[$c]['AccountID']);
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['ClanMembers'] = $clanMembers;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getAllClanMembers") {
        $clanMembers = $mcdb->getAllClanMembers();

        for ($c=0; $c<sizeof($clanMembers); $c++) {
            unset($clanMembers[$c]['DBClanMemberID']);
            unset($clanMembers[$c]['ClanMemberID']);
            unset($clanMembers[$c]['DBBackupGUID']);
            unset($clanMembers[$c]['GameServerID']);
            unset($clanMembers[$c]['CanAlterMembers']);
            unset($clanMembers[$c]['CanAlterParts']);
            unset($clanMembers[$c]['CanAlterLocks']);
            unset($clanMembers[$c]['CanAlterPower']);
            $clanMembers[$c]['MemberName'] = $mcdb->getPlayerSteamName($clanMembers[$c]['AccountID']);
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['ClanMembers'] = $clanMembers;
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
