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

    if ($cmd == "getBackups") {
        $backups = $mcdb->getBackups();
        for ($b=0; $b<sizeof($backups); $b++) {
            $bDT = new DateTime($backups[$b]['BackupDateTime']);
            $backups[$b]['BackupDateTime'] = $bDT->format($api_datetime_format);
        }

        $currentBackup = $mcdb->getCurrentBackup();
        $cbDT = new DateTime($currentBackup['BackupDateTime']);
        $currentBackup['BackupDateTime'] = $cbDT->format($api_datetime_format);

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Backups'] = $backups;
        $return['CurrentBackup'] = $currentBackup;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getLastBackup") {
        $lastBackup = $mcdb->getLastBackup();
        $lbDT = new DateTime($lastBackup['BackupDateTime']);
        $lastBackup['BackupDateTime'] = $lbDT->format($api_datetime_format);

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['LastBackup'] = $lastBackup;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getRecentStats") {
        $hours = $mcdb->getRecentPlayerCountBreakdown();

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Players'] = $hours;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getWeeklyStats") {
        $hours = $mcdb->getWeeklyPlayerCountBreakdown();

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Players'] = $hours;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getBiWeeklyStats") {
        $hours = $mcdb->getBiWeeklyPlayerCountBreakdown();

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Players'] = $hours;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getActivePlayerStats") {
        $stats = $mcdb->getActivePlayerStats();

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Total'] = $stats['Total'];
        $return['Active'] = $stats['Active'];
        $return['Inactive'] = $stats['Inactive'];
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
