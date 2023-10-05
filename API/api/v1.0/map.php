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

    if ($cmd == "getClanPlots") {
        if ($accountID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - accountID";
            echo json_encode($return);
            exit(0);
        }

        $clan = $mcdb->getPlayerClan($accountID);
        $clanMembers = $mcdb->getClanMembers($clan['ClanID']);

        $plots = array();
        for ($c=0; $c<sizeof($clanMembers); $c++) {
            $plot = array();
            $plotSign = $mcdb->getPlayerPlotSign($clanMembers[$c]['AccountID']);

            $overallHealth = 0;
            if ($plotSign !== false) {
                $overallDamage = 0;
                $overallMaxHealth = 0;
                $structureItems = $mcdb->getStructureParts($plotSign['StructureGUID']);
                for ($s=0; $s<sizeof($structureItems); $s++) {
                    $overallMaxHealth += $structureItems[$s]['MaxHealth'];
                    $overallDamage += (isset($structureItems[$s]['Data']['damage'])?$structureItems[$s]['Data']['damage']:0);
                }
                $overallHealth = $overallMaxHealth - $overallDamage;
                $overallHealth = round(($overallHealth/$overallMaxHealth)*100,1);
            }

            $plot['PosX'] = $plotSign['PosX'];
            $plot['PosY'] = $plotSign['PosY'];
            $plot['Name'] = $clanMembers[$c]['MemberName'];
            $plot['Health'] = $overallHealth;
            $plot['OverallObjects'] = sizeof($structureItems);

            if ($clanMembers[$c]['AccountID'] != $accountID) {
                array_push($plots, $plot);
            }
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Plots'] = $plots;
        echo json_encode($return);
        exit(0);

    }
    else if ($cmd == "getClanPositions") {
        if ($accountID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - accountID";
            echo json_encode($return);
            exit(0);
        }

        $clan = $mcdb->getPlayerClan($accountID);
        $inClan = false;

        $players = array();
        if ($clan) {
            $inClan = true;
            $clanMembers = $mcdb->getClanMembers($clan['ClanID']);
            for ($c=0; $c<sizeof($clanMembers); $c++) {
                $player = $mcdb->getPlayer($clanMembers[$c]['AccountID']);

                $lastSeen = $mcdb->getPlayerLastSeen($player['AccountID']);
                if ($lastSeen != "--") {
                    $lastSeenDate = new DateTime($lastSeen);
                    $player['LastSeen'] = $lastSeenDate->format($api_datetime_format);
                }
                else {
                    $player['LastSeen'] = $lastSeen;
                }

                $player['Name'] = $mcdb->getPlayerSteamName($clanMembers[$c]['AccountID']);
                if ($clanMembers[$c]['AccountID'] != $accountID) {
                    array_push($players,$player);
                }


            }
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['InClan'] = $inClan;
        $return['ClanName'] = $clan['ClanName'];
        $return['Positions'] = $players;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == 'getPlayerPlot') {
        if ($accountID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - accountID";
            echo json_encode($return);
            exit(0);
        }

        $plot = array();
        $plotSign = $mcdb->getPlayerPlotSign($accountID);

        $overallHealth = 0;
        if ($plotSign !== false) {
            $overallDamage = 0;
            $overallMaxHealth = 0;
            $structureItems = $mcdb->getStructureParts($plotSign['StructureGUID']);
            for ($s=0; $s<sizeof($structureItems); $s++) {
                $overallMaxHealth += $structureItems[$s]['MaxHealth'];
                $overallDamage += (isset($structureItems[$s]['Data']['damage'])?$structureItems[$s]['Data']['damage']:0);
            }
            $overallHealth = $overallMaxHealth - $overallDamage;
            $overallHealth = round(($overallHealth/$overallMaxHealth)*100,1);
        }

        $plot['PosX'] = $plotSign['PosX'];
        $plot['PosY'] = $plotSign['PosY'];
        $plot['Name'] = $mcdb->getPlayerSteamName($accountID);
        $plot['Health'] = $overallHealth;
        $plot['OverallObjects'] = sizeof($structureItems);

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Plot'] = $plot;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == 'getPlayerPosition') {
        if ($accountID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - accountID";
            echo json_encode($return);
            exit(0);
        }

        $player = $mcdb->getPlayer($accountID);
        $player['Name'] = $mcdb->getPlayerSteamName($accountID);

        $lastSeen = $mcdb->getPlayerLastSeen($player['AccountID']);
        if ($lastSeen != "--") {
            $lastSeenDate = new DateTime($lastSeen);
            $player['LastSeen'] = $lastSeenDate->format($api_datetime_format);
        }
        else {
            $player['LastSeen'] = $lastSeen;
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Position'] = $player;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == 'getVehiclePositions') {
        if ($accountID == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing Parameter - accountID";
            echo json_encode($return);
            exit(0);
        }

        $vehicles = array();

        //Personal Vehicles
        $plot = $mcdb->getPlayerPlotSign($accountID);
        if ($plot) {
            $plot_vehicles = $mcdb->getVehiclesNear($plot['PosX'],$plot['PosY'],$api_plot_radius);
            for ($v=0; $v<sizeof($plot_vehicles); $v++) {
                unset($plot_vehicles[$v]['GameServerID']);
                unset($plot_vehicles[$v]['DBBackupGUID']);
                unset($plot_vehicles[$v]['DBVehicleID']);
                unset($plot_vehicles[$v]['PosZ']);
                unset($plot_vehicles[$v]['RotX']);
                unset($plot_vehicles[$v]['RotY']);
                unset($plot_vehicles[$v]['RotZ']);
                unset($plot_vehicles[$v]['Data']['skin']);
                unset($plot_vehicles[$v]['MapName']);
                unset($plot_vehicles[$v]['VehicleID']);

                if (isset($vehicleDetail[$plot_vehicles[$v]['Category']])) {
                    $details = $vehicleDetail[$plot_vehicles[$v]['Category']];
                    if ($details['Engine']) {
                        $plot_vehicles[$v]['Data']['oil'] = round(($plot_vehicles[$v]['Data']['oil'] / $details['OilCapacity'])*100);
                        $plot_vehicles[$v]['Data']['dieselfuel'] = round(($plot_vehicles[$v]['Data']['dieselfuel'] / $details['FuelCapacity'])*100);
                    }
                    $plot_vehicles[$v]['Name'] = $details['Name'];
                    $plot_vehicles[$v]['Engine'] = $details['Engine'];
                }

                array_push($vehicles, $plot_vehicles[$v]);
            }
        }

        //Clan Vehicles
        $clan = $mcdb->getPlayerClan($accountID);
        $clanMembers = $mcdb->getClanMembers($clan['ClanID']);
        for ($c=0; $c<sizeof($clanMembers); $c++) {
            if ($clanMembers[$c]['AccountID'] != $accountID) {
                $plot = $mcdb->getPlayerPlotSign($clanMembers[$c]['AccountID']);
                if ($plot) {
                    $plot_vehicles = $mcdb->getVehiclesNear($plot['PosX'],$plot['PosY'],$api_plot_radius);
                    for ($v=0; $v<sizeof($plot_vehicles); $v++) {
                        unset($plot_vehicles[$v]['GameServerID']);
                        unset($plot_vehicles[$v]['DBBackupGUID']);
                        unset($plot_vehicles[$v]['DBVehicleID']);
                        unset($plot_vehicles[$v]['PosZ']);
                        unset($plot_vehicles[$v]['RotX']);
                        unset($plot_vehicles[$v]['RotY']);
                        unset($plot_vehicles[$v]['RotZ']);
                        unset($plot_vehicles[$v]['Data']['skin']);
                        unset($plot_vehicles[$v]['MapName']);
                        unset($plot_vehicles[$v]['VehicleID']);

                        if (isset($vehicleDetail[$plot_vehicles[$v]['Category']])) {
                            $details = $vehicleDetail[$plot_vehicles[$v]['Category']];
                            if ($details['Engine']) {
                                $plot_vehicles[$v]['Data']['oil'] = round(($plot_vehicles[$v]['Data']['oil'] / $details['OilCapacity'])*100);
                                $plot_vehicles[$v]['Data']['dieselfuel'] = round(($plot_vehicles[$v]['Data']['dieselfuel'] / $details['FuelCapacity'])*100);
                            }
                            $plot_vehicles[$v]['Name'] = $details['Name'];
                            $plot_vehicles[$v]['Engine'] = $details['Engine'];
                        }

                        array_push($vehicles, $plot_vehicles[$v]);
                    }
                }
            }
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Vehicles'] = $vehicles;
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
