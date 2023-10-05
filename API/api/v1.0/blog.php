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

    if ($cmd == "get") {
        $data = $twdl->getPostData();

        for ($p=0; $p<sizeof($data['Posts']); $p++) {
            $published = new DateTime($data['Posts'][$p]['PublishedDateTime']);
            $data['Posts'][$p]['EnglishDateTime'] = $published->format($api_datetime_format);
        }

        $return['Status'] = true;
        $return['Message'] = "Success";
        $return['Query'] = $data['Query'];
        $return['Posts'] = $data['Posts'];
        $return['Page'] = $data['Page'];
        $return['PageCount'] = $data['PageCount'];
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
