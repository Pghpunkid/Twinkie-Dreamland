<?php

    include('core.php');

    $command = post('cmd');
    $return = array();

    $recaptcha_secret = "6Lcs0lAdAAAAAOJoqFGXfMD2pk1qT4Z4BazG0-Xu";

    $dbi = new Mysqli($db_host, $db_user, $db_pass, $cr_db);

    if ($command == "submit") {
        $request_captcha =       post('request-captcha');
        $request_item =          $dbi->real_escape_string(htmlspecialchars(post('request-item')));
        $request_type =          $dbi->real_escape_string(htmlspecialchars(post('request-type')));
        $request_short_detail =  $dbi->real_escape_string(htmlspecialchars(post('request-short-detail')));
        $request_detail =        $dbi->real_escape_string(htmlspecialchars(post('request-detail')));
        $request_author =        $dbi->real_escape_string(htmlspecialchars(post('request-author')));

        $return['response'] = '';

        //https://www.google.com/recaptcha/api/siteverify
        //secret	Required. The shared key between your site and reCAPTCHA.
        //response	Required. The user response token provided by the reCAPTCHA client-side integration on your site.

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,            "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST,           1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     "secret=".$recaptcha_secret."&response=".$request_captcha);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close ($ch);

        $data = json_decode($output, true);

        if ($data['success'] !== true) {
            $return['response'] = 'There was a problem submitting your change request. Please try again.';
            $return['status'] = 'Fail';
            $return['data'] = $data;
            echo json_encode($return);
            exit(0);
        }

        $sql = "INSERT INTO change_requests SET ".
        'RequestItem="'.$request_item.'",'.
        'RequestType="'.$request_type.'",'.
        'RequestShortDescription="'.$request_short_detail.'",'.
        'RequestDescription="'.$request_detail.'",'.
        'Status="Initial",'.
        'Requestor="'.$request_author.'",'.
        'CompletedVersion=NULL,'.
        'Completed="N"';

        $result = $dbi->query($sql);
        if (!$result) {
            $return['response'] = 'There was a problem submitting your change request. '.$dbi->error;
            $return['status'] = 'Fail';
            echo json_encode($return);
            exit(0);
        }

        //Notify Dev!
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => "https://api.pushover.net/1/messages.json",
            CURLOPT_POSTFIELDS => array(
                "token" => "aqh6eu3vgq3f2j4akkgusdjob7498c",
                "user" => "uCZ62J343TqsTvV8ikPK96EWtQbaXF",
                "html" => "1",
                "title" => "Change Request Submitted",
                "message" => "A new change request <b>".$request_short_detail."</b> was submitted by <b>".$request_author."</b>.",
            ),
            CURLOPT_SAFE_UPLOAD => true,
            CURLOPT_RETURNTRANSFER => true,
        ));
        curl_exec($ch);
        curl_close($ch);

        $return['status'] = 'Ok';
        echo json_encode($return);
        exit(0);
    }
    else if ($command == "fetchAll") {
        if (!$loggedIn) {
            $return['status'] = 'Fail';
            $return['response'] = 'Not Logged In';
            echo json_encode($return);
            exit(0);
        }

        $filter = post('filter');
        $filter_query = "";
        if ($filter == 'active') {
            $filter_query = " WHERE ".
            "(Completed != 'Y' OR (Completed = 'Y' AND TO_DAYS(SYSDATE()) - TO_DAYS(CompletedDateTime) <= 7))".
             " AND ".
            "(Status != 'Canceled' OR (Status = 'Canceled' AND TO_DAYS(SYSDATE()) - TO_DAYS(CompletedDateTime) <= 7))";
        }
        else if ($filter == 'completed') {
            $filter_query = " WHERE Completed = 'Y'";
        }
        else if ($filter == 'canceled') {
            $filter_query = " WHERE Status = 'Canceled'";
        }
        else if ($filter == 'in-progress') {
            $filter_query = " WHERE Status = 'In Progress'";
        }
        else if ($filter == 'on-hold') {
            $filter_query = " WHERE Status = 'On Hold'";
        }

        $requests = [];

        $sql = "SELECT *, DATE_FORMAT(RequestDateTime, '%b %e %Y %l:%i%p') AS RequestDateTimeEng, DATE_FORMAT(CompletedDateTime, '%b %e %Y %l:%i%p') AS CompletedDateTimeEng FROM change_requests".$filter_query;
        $result = $dbi->query($sql);
        if (!$result) {
            $return['status'] = 'Fail';
            $return['response'] = 'Failed to get change requests.'.$dbi->error;
            echo json_encode($return);
            exit(0);
        }

        while ($row = $result->fetch_assoc()) {
            $sql2 = "SELECT *, DATE_FORMAT(NoteDateTime, '%b %e %Y %l:%i%p') AS NoteDateTimeEng FROM change_request_notes WHERE RequestID=".$row['RequestID'].";";
            $result2 = $dbi->query($sql2);
            $notes = [];

            if ($result2) {
                while ($row2 = $result2->fetch_assoc()) {
                    $row2['RequestDescription'] = htmlspecialchars_decode($row2['RequestDescription'], ENT_NOQUOTES);
                    array_push($notes, $row2);
                }
            }

            $row['Notes'] = $notes;
            array_push($requests, $row);
        }

        $return['status'] = 'Ok';
        $return['requests'] = $requests;
        echo json_encode($return);
        exit(0);
    }
    else if ($command == 'updateCR') {
        if (!$loggedIn) {
            $return['status'] = 'Fail';
            $return['response'] = 'Not Logged In';
            echo json_encode($return);
            exit(0);
        }

        $requestID =                    $dbi->real_escape_string(htmlspecialchars(post('requestID')));
        $requestType =                  $dbi->real_escape_string(htmlspecialchars(post('requestType')));
        $requestItem =                  $dbi->real_escape_string(htmlspecialchars(post('requestItem')));
        $requestShortDetail =           $dbi->real_escape_string(htmlspecialchars(post('requestShortDetail')));
        $requestDetail =                $dbi->real_escape_string(htmlspecialchars(post('requestDetail')));
        $requestStatus =                $dbi->real_escape_string(htmlspecialchars(post('requestStatus')));
        $requestAuthor =                $dbi->real_escape_string(htmlspecialchars(post('requestAuthor')));
        $requestCompletedVersion =      $dbi->real_escape_string(htmlspecialchars(post('requestCompletedVersion')));

        $sql = 'UPDATE change_requests SET '.
            'RequestType="'.$requestType.'", '.
            'RequestItem="'.$requestItem.'", '.
            'RequestShortDescription="'.$requestShortDetail.'", '.
            'RequestDescription="'.$requestDetail.'", '.
            'Status="'.$requestStatus.'", '.
            'Requestor="'.$requestAuthor.'", '.
            'CompletedVersion='.($requestStatus == "Complete"?'"'.$requestCompletedVersion.'"':"NULL").' '.
            ($requestStatus == 'Complete' || $requestStatus == 'Canceled'?', CompletedDateTime=SYSDATE()':'').
            'WHERE RequestID='.$requestID;
        $result = $dbi->query($sql);
        if (!$result) {
            $return['status'] = 'Fail';
            $return['response'] = $dbi->error.' '.$sql;
            echo json_encode($return);
            exit(0);
        }
        $return['status'] = 'Ok';
        echo json_encode($return);
        exit(0);
    }
    else if ($command == 'submitCRNote') {
        if (!$loggedIn) {
            $return['status'] = 'Fail';
            $return['response'] = 'Not Logged In';
            echo json_encode($return);
            exit(0);
        }

        $note =         $dbi->real_escape_string(htmlspecialchars(post('note')));
        $author =       $dbi->real_escape_string(htmlspecialchars(post('author')));
        $requestID =    $dbi->real_escape_string(htmlspecialchars(post('requestID')));

        $sql = 'INSERT INTO change_request_notes SET RequestID='.$requestID.', NoteDescription="'.$note.'", NoteAuthor="'.$author.'", NoteDateTime=SYSDATE()';
        $result = $dbi->query($sql);

        if (!$result) {
            $return['status'] = 'Fail';
            $return['response'] = $dbi->error;
            echo json_encode($return);
            exit(0);
        }
        $return['status'] = 'Ok';
        echo json_encode($return);
        exit(0);
    }
    else if ($command == 'deleteCRNote') {
        if (!$loggedIn) {
            $return['status'] = 'Fail';
            $return['response'] = 'Not Logged In';
            echo json_encode($return);
            exit(0);
        }

        $noteID    =    $dbi->real_escape_string(htmlspecialchars(post('noteID')));
        $requestID =    $dbi->real_escape_string(htmlspecialchars(post('requestID')));

        $sql = 'DELETE FROM change_request_notes WHERE RequestID='.$requestID.' AND RequestNoteID='.$noteID;
        $result = $dbi->query($sql);
        if (!$result) {
            $return['status'] = 'Fail';
            $return['response'] = $dbi->error;
            echo json_encode($return);
            exit(0);
        }
        $return['status'] = 'Ok';
        echo json_encode($return);
        exit(0);
    }
    else {
        $return['status'] = 'Fail';
        $return['response'] = 'Unknown Operation';
        echo json_encode($return);
        exit(0);
    }

    function post($idx) {
        if (isset($_POST[$idx])) {
            return $_POST[$idx];
        }
        else {
            return false;
        }
    }
?>
