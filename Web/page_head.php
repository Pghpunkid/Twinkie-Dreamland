<?php
    echo "
        <head>
            <meta charset=\"utf-8\">
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
            <meta name=\"description\" content=\"\">
            <title>Woody's Twinkie Dreamland - Time to Nut Up, or Shut Up!</title>
            <link href=\"css/bootstrap.min.css\" rel=\"stylesheet\">
            <link href=\"css/twinkie-dreamland.css?t=".time()."\" rel=\"stylesheet\">
            <link href=\"css/leaflet.css\" rel=\"stylesheet\">
            <link href=\"css/Chart.min.css\" rel=\"stylesheet\">
            <link href=\"css/miscreated-map.css\" rel=\"stylesheet\">
            <script>
                var isDev = ".($isDev?'true':'false').";
                var initializeMiscreatedMap = false;
                var accountId = $accountId;
                var token = '$token';
                var loggedIn = ".($loggedIn?"true":"false").";
                var playerName = ".($loggedIn?"'".$steamprofile['personaname']."'":"false").";
                ".($page_name == "/admin-portal" || $page_name == "/admin-portal.php"?"var initializeMiscreatedAdminMap = false":"").
            "</script>
        </head>
    ";
?>
