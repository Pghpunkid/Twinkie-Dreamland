<?php

    include("api-core.php");

    
    $sql = "SELECT COUNT(*) FROM DB_Players WHERE SteamID='".$steamId."';";

?>
