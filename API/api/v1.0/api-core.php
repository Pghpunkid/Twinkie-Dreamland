<?php
    include('api-settings.php');
    include('api-functions.php');
    include('api-key-management.php');
    include('class.MCDBAPI.php');
    include('class.TwinkieDreamland.php');

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    $token                  = (isset($_GET['token'])?$_GET['token']:'');

    $cmd                    = (isset($_GET['param'])?$_GET['param']:'');
    $accountID              = (isset($_GET['accountID'])?$_GET['accountID']:'');
    $vehicleGUID            = (isset($_GET['vehicleGUID'])?$_GET['vehicleGUID']:'');
    $structureGUID          = (isset($_GET['structureGUID'])?$_GET['structureGUID']:'');
    $structurePartGUID      = (isset($_GET['structurePartGUID'])?$_GET['structurePartGUID']:'');
    $itemGUID               = (isset($_GET['itemGUID'])?$_GET['itemGUID']:'');
    $clanID                 = (isset($_GET['clanID'])?$_GET['clanID']:'');
    $className              = (isset($_GET['className'])?$_GET['className']:'');

    $return = array();

    $mcdb = new MCDBAPI();
    if (!$mcdb) {
        $return['Status'] = false;
        $return['Message'] = "DB Unreachable";
        echo json_encode($return);
        exit(0);
    }

    $twdl = new TwinkieDreamland();
    if (!$twdl) {
        $return['Status'] = false;
        $return['Message'] = "DB Unreachable";
        echo json_encode($return);
        exit(0);
    }
    
    $return['MaintenanceMode'] = false;
    if ($mcdb->checkMaintenanceMode()) {
        $return['MaintenanceMode'] = true;
    }

    $vehicleDetailJSON = '{
        "armored_truck_army":{
            "Name":"Armored Truck (Army)",
            "Engine": true,
            "OilCapacity":8000,
            "FuelCapacity":150000
        },
        "armored_truck_police":{
            "Name":"Armored Truck (SWAT)",
            "Engine": true,
            "OilCapacity":8000,
            "FuelCapacity":150000
        },
        "bicycle":{
            "Name":"Bicycle",
            "Engine": false,
            "OilCapacity":0,
            "FuelCapacity":0
        },
        "dirtbike":{
            "Name":"Dirtbike",
            "Engine": true,
            "OilCapacity":3000,
            "FuelCapacity":150000
        },
        "dune_buggy":{
            "Name":"Dune Buggy",
            "Engine": true,
            "OilCapacity":6000,
            "FuelCapacity":75000
        },
        "f100truck":{
            "Name":"Pickup",
            "Engine": true,
            "OilCapacity":6000,
            "FuelCapacity":110000
        },
        "fishing_boat":{
            "Name":"Fishing Boat",
            "Engine": true,
            "OilCapacity":4000,
            "FuelCapacity":40000
        },
        "jetski":{
            "Name":"Jetski",
            "Engine": true,
            "OilCapacity":4000,
            "FuelCapacity":50000
        },
        "party_bus":{
            "Name":"Bus",
            "Engine": true,
            "OilCapacity":10000,
            "FuelCapacity":250000
        },
        "quadbike":{
            "Name":"Quadbike",
            "Engine": true,
            "OilCapacity":3000,
            "FuelCapacity":20000
        },
        "sedan_base":{
            "Name":"Sedan",
            "Engine": true,
            "OilCapacity":6000,
            "FuelCapacity":65000
        },
        "sedan_police":{
            "Name":"Sedan (Police)",
            "Engine": true,
            "OilCapacity":6000,
            "FuelCapacity":90000
        },
        "sedan_taxi":{
            "Name":"Sedan (Taxi)",
            "Engine": true,
            "OilCapacity":6000,
            "FuelCapacity":65000
        },
        "suv_basic":{
            "Name":"SUV",
            "Engine": true,
            "OilCapacity":6000,
            "FuelCapacity":100000
        },
        "towcar":{
            "Name":"Tow Car",
            "Engine": true,
            "OilCapacity":8000,
            "FuelCapacity":150000
        },
        "tractor":{
            "Name":"Tractor",
            "Engine": true,
            "OilCapacity":6000,
            "FuelCapacity":150000
        },
        "truck_5ton":{
            "Name":"5 Ton Truck",
            "Engine": true,
            "OilCapacity":8000,
            "FuelCapacity":150000
        },
        "truck_semi":{
            "Name":"Semi",
            "Engine": true,
            "OilCapacity":12000,
            "FuelCapacity":300000
        }
    }';
    $vehicleDetail = json_decode($vehicleDetailJSON,true);
?>
