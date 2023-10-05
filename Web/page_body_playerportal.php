<?php
    set_time_limit(0);

    $token = 'false';
    $maintenance_mode = false;

    $request = json_decode(file_get_contents("https://api.twinkiedreamland.com/api/v1.0/token.php?param=request"),true);
    if ($request['Status']) {
        $token = '"'.$request['Token'].'"';
        $maintenance_mode = $request['MaintenanceMode'];
    }

    echo '<div class="main">
        <div class="container">
            <br/>
            <h1 class="uppercase">Player Portal</h1>
            <div id="last-update"></div>
            <hr/>
            <div id="system-message" class="alert alert-warning" role="alert"'.($maintenance_mode?'':' style="display:none;"').'>
                The database is doing a routine backup so we can show you the latest information! '."Don't worry,".' it will be back shortly!
            </div>
            <div id="content" class="row"'.(!$maintenance_mode?'':' style="display:none;"').'>
                <div class="col-2">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link nav-link-player-portal active" id="v-pills-player-tab" data-toggle="pill" href="#" role="tab" aria-controls="v-pills-player" aria-selected="true">Player</a>
                        <a class="nav-link nav-link-player-portal" id="v-pills-structures-tab" data-toggle="pill" href="#" role="tab" aria-controls="v-pills-structures" aria-selected="false">Structures</a>
                        <a class="nav-link nav-link-player-portal" id="v-pills-clan-tab" data-toggle="pill" href="#" role="tab" aria-controls="v-pills-clan" aria-selected="false">Clan</a>
                    </div>
                </div>
                <div class="col-10">
                    <script>
                        var token = '.$token.';
                        var initializeMiscreatedMap = true;
                    </script>
                    <div id="miscreated-map"></div>
                    <br/>
                    <div id="player-info">
                        <table id="player-info-table" class="table td-data-table">
                        </table>
                    </div>
                    <div id="structures-info" style="display:none;"></div>
                    <div id="clan-info" style="display:none;">
                        <table id="clan-info-table" class="table td-data-table">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br/>';

?>
