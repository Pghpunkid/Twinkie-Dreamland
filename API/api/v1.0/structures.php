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

    if ($cmd == "getPlot") {
        if ($accountID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - accountID";
            echo json_encode($return);
            exit(0);
        }

        $plot = $mcdb->getPlayerPlotSign($accountID);
        unset($plot['DBBackupGUID']);
        unset($plot['DBStructuresID']);
        unset($plot['StructureID']);
        unset($plot['GameServerID']);
        unset($plot['MapName']);
        unset($plot['PosZ']);
        unset($plot['RotX']);
        unset($plot['RotY']);
        unset($plot['RotZ']);
        unset($plot['Data']);

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Plot'] = $plot;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getAllPlots") {
        $plots = $mcdb->getAllPlayerPlotSigns();
        for ($p=0; $p<sizeof($plots); $p++) {
            unset($plots[$p]['DBBackupGUID']);
            unset($plots[$p]['DBStructuresID']);
            unset($plots[$p]['StructureID']);
            unset($plots[$p]['GameServerID']);
            unset($plots[$p]['MapName']);
            unset($plots[$p]['PosZ']);
            unset($plots[$p]['RotX']);
            unset($plots[$p]['RotY']);
            unset($plots[$p]['RotZ']);
            unset($plots[$p]['Data']);
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Plots'] = $plots;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getStructures") {
        if ($accountID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - accountID";
            echo json_encode($return);
            exit(0);
        }

        $structures = $mcdb->getStructures($accountID);
        for ($s=0; $s<sizeof($structures); $s++) {
            unset($structures[$s]['DBBackupGUID']);
            unset($structures[$s]['DBStructuresID']);
            unset($structures[$s]['StructureID']);
            unset($structures[$s]['GameServerID']);
            unset($structures[$s]['MapName']);
            unset($structures[$s]['PosZ']);
            unset($structures[$s]['RotX']);
            unset($structures[$s]['RotY']);
            unset($structures[$s]['RotZ']);
            unset($structures[$s]['Data']);
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Structures'] = $structures;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getAllStructures") {
        $structures = $mcdb->getAllStructures();
        for ($s=0; $s<sizeof($structures); $s++) {
            unset($structures[$s]['DBBackupGUID']);
            unset($structures[$s]['DBStructuresID']);
            unset($structures[$s]['StructureID']);
            unset($structures[$s]['GameServerID']);
            unset($structures[$s]['MapName']);
            unset($structures[$s]['PosZ']);
            unset($structures[$s]['RotX']);
            unset($structures[$s]['RotY']);
            unset($structures[$s]['RotZ']);
            unset($structures[$s]['Data']);
            if ($structures[$s]['AccountID'] != 0) {
                $structures[$s]['Name'] = $mcdb->getPlayerSteamName($structures[$s]['AccountID']);
            }
            else {
                $structures[$s]['Name'] = false;
            }
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Structures'] = $structures;
        echo json_encode($return);
        exit(0);
    }
    
    else if ($cmd == "getStructureParts") {
        if ($structureGUID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - structureGUID";
            echo json_encode($return);
            exit(0);
        }

        $structureParts = $mcdb->getStructureParts($structureGUID);
        for ($s=0; $s<sizeof($structureParts); $s++) {
            unset($structureParts[$s]['DBBackupGUID']);
            unset($structureParts[$s]['DBStructurePartID']);
            unset($structureParts[$s]['StructurePartID']);
            unset($structureParts[$s]['StructureGUID']);
            unset($structureParts[$s]['PosZ']);
            unset($structureParts[$s]['RotZ']);
            unset($structureParts[$s]['ParentStructurePartGUIDs']);
        }

        if (!$structureParts) {
            $return['errors'] = $mcdb->getErrorLog();
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['StructureParts'] = $structureParts;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getStructureItems") {
        if ($structureGUID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - structureGUID";
            echo json_encode($return);
            exit(0);
        }

        $structureItems = $mcdb->getStructureItems($structureGUID);

        for ($s=0; $s<sizeof($structureItems); $s++) {
            unset($structureItems[$s]['ItemID']);
            unset($structureItems[$s]['DBBackupGUID']);
            unset($structureItems[$s]['DBItemID']);
            unset($structureItems[$s]['ParentGUID']);
            unset($structureItems[$s]['Data']['skin']);
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['StructureItems'] = $structureItems;
        echo json_encode($return);
        exit(0);
    }

    else if ($cmd == "getStructurePartItems") {
        if ($structurePartGUID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - structurePartGUID";
            echo json_encode($return);
            exit(0);
        }

        $structurePartItems = $mcdb->getStructurePartItems($structurePartGUID);

        for ($s=0; $s<sizeof($structurePartItems); $s++) {
            unset($structurePartItems[$s]['ItemID']);
            unset($structurePartItems[$s]['DBBackupGUID']);
            unset($structurePartItems[$s]['DBItemID']);
            unset($structurePartItems[$s]['ParentGUID']);
            unset($structurePartItems[$s]['Data']['skin']);
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['StructurePartItems'] = $structurePartItems;
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
