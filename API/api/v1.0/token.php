<?php

    include("api-core.php");

    if ($cmd == "request" && !isAddressWhitelisted($_SERVER['REMOTE_ADDR'])) {
        $return['Status'] = false;
        $return['Message'] = "Unauthorized";
        $return['Address'] = $_SERVER['REMOTE_ADDR'];
        echo json_encode($return);
        exit(0);
    }

    if ($cmd == "request") {
        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Token'] = generate_api_token();
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "verify") {
        if ($token == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing or Malformed Parameter - token";
            echo json_encode($return);
            exit(0);
        }

        $validKey = check_api_token($token);

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Key'] = $token;
        $return['KeyValid'] = false;
        if ($validKey) {
            $return['KeyValid'] = true;
        }
        echo json_encode($return);
        exit(0);
    }
    else if ($cmd == "update") {
        if ($token == "") {
            $return['Status'] = false;
            $return['Message'] = "Missing or Malformed Parameter - token";
            echo json_encode($return);
            exit(0);
        }

        $newToken = generate_api_token();
        $validKey = check_token_validity($token, $newToken);

        if (!$validKey) {
            $return['Status'] = false;
            $return['Message'] = "Key Not Authentic";
            $return['Key'] = $token;
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Key'] = $token;
        $return['UpdatedKey'] = $newToken;
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
