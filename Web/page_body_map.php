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
            <h1 class="uppercase">Map</h1>
            <div id="last-update"></div>
            <hr/>
            <div id="content" class="row">
                <div class="col-12">
                    <script>
                        var initializeMiscreatedMap = true;
                    </script>
                    <div id="miscreated-map"></div>
                    <br/>
                    <h3 class="uppercase">Locator</h3>
                    <hr/>
                    Use GPS coordinates from a Survivotron to find where you are on the map!<br/>
                    <br/>
                    <div class="center">
                        <table class="table center" style="color:white;border:none;">
                            <tr>
                                <td style="border-top:0px!important;">N</td>
                                <td style="border-top:0px!important;"><input id="n-deg"></input> &deg;</td>
                                <td style="border-top:0px!important;"><input id="n-minutes"></input> \'</td>
                                <td style="border-top:0px!important;"><input id="n-seconds"></input> "</td>
                            </tr>
                            <tr>
                                <td style="border-top:0px!important;">W</td>
                                <td style="border-top:0px!important;"><input id="w-deg"></input> &deg;</td>
                                <td style="border-top:0px!important;"><input id="w-minutes"></input> \'</td>
                                <td style="border-top:0px!important;"><input id="w-seconds"></input> "</td>
                            </tr>
                        </table>
                    </div>
                    <a href="#" id="locate" class="btn btn-primary float-right">Show Location</a>
                </div>
            </div>
        </div>
    </div>
    <br/>';

?>
