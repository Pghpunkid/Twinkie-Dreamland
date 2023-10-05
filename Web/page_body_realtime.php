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
            <h1 class="uppercase">Realtime Map</h1>
            <hr/>
            <div id="system-message" class="alert alert-warning" role="alert"'.($maintenance_mode?'':' style="display:none;"').'>
                The database is doing a routine backup so we can show you the latest information! '."Don't worry,".' it will be back shortly!
            </div>
            <div id="content" class="row" '.(!$maintenance_mode?'':' style="display:none;"').'>
                <div class="col-12">
                    <script>
                        var token = '.$token.';
                        var initializeMiscreatedMap = true;
                    </script>

                    <div id="miscreated-map"></div>
                    <br/>
                    <div id="infractions"></div>
                    <div style="display:none;">
                        <h4>Log</h4>
                        <textarea id="maplog" class="form-control" style="font-size:12px;width:100%;height:100px;" readonly="readonly"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br/>';

?>
