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
        if ($vehicleGUID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - vehicleGUID";
            echo json_encode($return);
            exit(0);
        }

        $vehicle = $mcdb->getVehicle($vehicleGUID);
        unset($vehicle['VehicleID']);
        unset($vehicle['DBVehicleID']);
        unset($vehicle['DBBackupGUID']);
        unset($vehicle['ParentGUID']);
        unset($vehicle['Data']['skin']);

        if (isset($vehicleDetail[$vehicles[$v]['Category']])) {
            $details = $vehicleDetail[$vehicle['Category']];
            if ($details['Engine']) {
                $vehicle['Data']['oil'] = round(($vehicle['Data']['oil'] / $details['OilCapacity'])*100);
                $vehicle['Data']['dieselfuel'] = round(($vehicle['Data']['dieselfuel'] / $details['FuelCapacity'])*100);
            }
            $vehicles[$v]['Name'] = $details['Name'];
            $vehicles[$v]['Engine'] = $details['Engine'];
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Vehicle'] = $vehicle;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getAll") {
        $vehicles = $mcdb->getAllVehicles();

        for ($v=0; $v<sizeof($vehicles); $v++) {
            unset($vehicles[$v]['GameServerID']);
            unset($vehicles[$v]['DBBackupGUID']);
            unset($vehicles[$v]['MapName']);
            unset($vehicles[$v]['DBVehicleID']);
            unset($vehicles[$v]['PosZ']);
            unset($vehicles[$v]['RotX']);
            unset($vehicles[$v]['RotY']);
            unset($vehicles[$v]['RotZ']);
            unset($vehicles[$v]['Data']['skin']);

            if (isset($vehicleDetail[$vehicles[$v]['Category']])) {
                $details = $vehicleDetail[$vehicles[$v]['Category']];
                if ($details['Engine']) {
                    $vehicles[$v]['Data']['oil'] = round(($vehicles[$v]['Data']['oil'] / $details['OilCapacity'])*100);
                    $vehicles[$v]['Data']['dieselfuel'] = round(($vehicles[$v]['Data']['dieselfuel'] / $details['FuelCapacity'])*100);
                }
                $vehicles[$v]['Name'] = $details['Name'];
                $vehicles[$v]['Engine'] = $details['Engine'];
            }
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Vehicles'] = $vehicles;
        echo json_encode($return);
        exit(0);
    }
    if ($cmd == "getAllNearPlayerPlot") {
        if ($accountID == "") {
            $return['Status'] = false;
            $return['Message'] = "Malformed or Missing Parameter - accountID";
            echo json_encode($return);
            exit(0);
        }

        $plot = $mcdb->getPlayerPlotSign($accountID);

        if (!$plot) {
            $return['Status'] = false;
            $return['Message'] = "No Player Plot Sign";
            echo json_encode($return);
            exit(0);
        }

        $return['Coords'] = array();
        $return['Coords']['X'] = $plot['PosX'];
        $return['Coords']['Y'] = $plot['PosY'];
        $return['Coords']['Radius'] = $api_plot_radius;

        $vehicles = $mcdb->getVehiclesNear($plot['PosX'],$plot['PosY'],$api_plot_radius);

        for ($v=0; $v<sizeof($vehicles); $v++) {
            unset($vehicles[$v]['GameServerID']);
            unset($vehicles[$v]['DBBackupGUID']);
            unset($vehicles[$v]['MapName']);
            unset($vehicles[$v]['DBVehicleID']);
            unset($vehicles[$v]['PosZ']);
            unset($vehicles[$v]['RotX']);
            unset($vehicles[$v]['RotY']);
            unset($vehicles[$v]['RotZ']);
            unset($vehicles[$v]['Data']['skin']);

            if (isset($vehicleDetail[$vehicles[$v]['Category']])) {
                $details = $vehicleDetail[$vehicles[$v]['Category']];
                if ($details['Engine']) {
                    $vehicles[$v]['Data']['oil'] = round(($vehicles[$v]['Data']['oil'] / $details['OilCapacity'])*100);
                    $vehicles[$v]['Data']['dieselfuel'] = round(($vehicles[$v]['Data']['dieselfuel'] / $details['FuelCapacity'])*100);
                }
                $vehicles[$v]['Name'] = $details['Name'];
                $vehicles[$v]['Engine'] = $details['Engine'];
            }
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Vehicles'] = $vehicles;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "getVehicleItems") {
        if ($vehicleGUID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - vehicleGUID";
            echo json_encode($return);
            exit(0);
        }

        $vehicleItems = $mcdb->getVehicleItems($vehicleGUID);

        for ($v=0; $v<sizeof($vehicleItems); $v++) {
            unset($vehicleItems[$v]['ItemID']);
            unset($vehicleItems[$v]['DBBackupGUID']);
            unset($vehicleItems[$v]['DBItemID']);
            unset($vehicleItems[$v]['ParentGUID']);
            unset($vehicleItems[$v]['Data']['skin']);
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['VehicleItems'] = $vehicleItems;
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
