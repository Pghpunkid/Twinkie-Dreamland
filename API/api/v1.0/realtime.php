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

    if ($cmd == "entities") {
        $data = json_decode(file_get_contents('dynamic/entities.json'), true);

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Data'] = $data;
        echo json_encode($return);
        exit(0);
    }
    if ($cmd == "entities_test") {
        $data = json_decode(file_get_contents('dynamic/entities_test.json'), true);

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Data'] = $data;
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "players") {
        $data = json_decode(file_get_contents('dynamic/players.json'), true);

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Data'] = $data;
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
