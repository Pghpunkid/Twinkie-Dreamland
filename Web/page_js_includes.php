<?php
    echo '
    <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/socket.io-2.3.0.slim.js"></script>
    <script type="text/javascript" src="js/popper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/leaflet.js"></script>
    <script type="text/javascript" src="js/Chart.min.js"></script>';

    if ($page_name == "/admin-portal" || $page_name == "/admin-portal.php") {
        echo '
        <script type="text/javascript" src="https://api.twinkiedreamland.com/map/icons/basic/icons_B64L.php"></script>
        <script type="text/javascript" src="js/miscreated-map.js"></script>';
    }
    else if ($page_name == "/player-portal" || $page_name == "/player-portal.php") {
        echo '
        <script type="text/javascript" src="https://api.twinkiedreamland.com/map/icons/basic/icons_B64L.php"></script>
        <script type="text/javascript" src="js/miscreated-map.js"></script>';
    }
    else if ($page_name == "/realtime" || $page_name == "/realtime.php") {
        echo '
        <script type="text/javascript" src="https://api.twinkiedreamland.com/map/icons/all/icons_B64L.php"></script>
        <script type="text/javascript" src="js/miscreated-map.js"></script>';
    }
    else if ($page_name == "/map" || $page_name == "/map.php") {
        echo '
        <script type="text/javascript" src="https://api.twinkiedreamland.com/map/icons/basic/icons_B64L.php"></script>
        <script type="text/javascript" src="js/miscreated-map.js"></script>';
    }
    else if ($page_name == "/change-request" || $page_name == "/change-request.php") {
        echo '
        <script type="text/javascript" src="js/twdl-cr-list.js"></script>';
    }
    else if ($page_name == "/new-change-request" || $page_name == "/new-change-request.php") {
        echo '
        <script type="text/javascript" src="js/twdl-cr-form.js"></script>
        <script async src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"></script>';
    }

    echo '
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/realtimecomm.js?t='.time().'"></script>
    <script type="text/javascript" src="js/twinkiedreamland-general.js?t='.time().'"></script>';

    if ($page_name == "/admin-portal" || $page_name == "/admin-portal.php") {
        echo '<script type="text/javascript" src="js/twinkiedreamland-admin.js?t='.time().'"></script>';
    }
    else if ($page_name == "/player-portal" || $page_name == "/player-portal.php") {
        echo '<script type="text/javascript" src="js/twinkiedreamland-player.js?t='.time().'"></script>';
    }
    else if ($page_name == "/realtime" || $page_name == "/realtime.php") {
        echo '<script type="text/javascript" src="js/twinkiedreamland-realtime.js?t='.time().'"></script>';
    }
    else if ($page_name == "/map" || $page_name == "/map.php") {
        echo '<script type="text/javascript" src="js/twinkiedreamland-map.js?t='.time().'"></script>';
    }

?>
