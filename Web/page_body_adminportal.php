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
            <h1 class="uppercase">Admin Portal</h1>
            <div id="last-update"></div>
            <hr/>
            <div id="system-message" class="alert alert-warning" role="alert"'.($maintenance_mode?'':' style="display:none;"').'>
                The database is doing a routine backup so we can show you the latest information! '."Don't worry,".' it will be back shortly!
            </div>
            <div id="content" class="row" '.(!$maintenance_mode?'':' style="display:none;"').'>
                <div class="col-2">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link nav-link-admin-panel active" id="v-pills-admin-maps-tab" data-toggle="pill" href="#" role="tab" aria-controls="v-pills-admin-maps" aria-selected="true">Maps</a>
                        <a class="nav-link nav-link-admin-panel" id="v-pills-admin-player-stats-tab" data-toggle="pill" href="#" role="tab" aria-controls="v-pills-admin-stats" aria-selected="false">Player Stats</a>
                        <a class="nav-link nav-link-admin-panel" id="v-pills-admin-change-requests-tab" data-toggle="pill" href="#" role="tab" aria-controls="v-pills-admin-stats" aria-selected="false">Change Requests</a>
                    </div>
                </div>
                <div class="col-10">
                    <script>
                        var token = '.$token.';
                        var initializeMiscreatedMap = true;
                    </script>

                    <div id="map">
                        <div id="miscreated-map">
                        </div>
                        <br/>
                        <div id="recent-players">
                        </div>
                    </div>

                    <div id="backup-page" style="display: none;">
                    </div>

                    <div id="player-stats-page" style="display: none;">
                        <h4>Simultaneous Player Count</h4>

                        <div class="card card-content-parent">
                            <div class="card-body card-content">
                                <h5 class="card-title">Last 24 Hours</h5>
                                <hr/>
                                <canvas id="rolling_players_day" width="100%" height="100px"></canvas>
                            </div>
                        </div>
                        <br/>
                        <div class="card card-content-parent">
                            <div class="card-body card-content">
                                <h5 class="card-title">Last 2 Weeks</h5>
                                <hr/>
                                <canvas id="rolling_players_week" width="100%" height="100px"></canvas>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="card card-content-parent">
                                    <div class="card-body card-content">
                                        <h5 class="card-title">Unique Players</h5>
                                        <hr/>
                                        <div class="center stats-player-display" id="total_players"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="card card-content-parent">
                                    <div class="card-body card-content">
                                        <h5 class="card-title">Active Players</h5>
                                        <hr/>
                                        <div class="center stats-player-display" id="total_active_players"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="card card-content-parent">
                                    <div class="card-body card-content">
                                        <h5 class="card-title">Inactive Players</h5>
                                        <hr/>
                                        <div class="center stats-player-display" id="total_inactive_players"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="change-request-page" style="display: none;">
                        <div id="change-requests-list">
                            <div class="float-right" style="position: relative; top:-10px;">
                                <select class="form-control" id="cr-list-filter">
                                    <option value="active" selected>Active</option>
                                    <option value="all">All</option>
                                    <option value="canceled">Canceled</option>
                                    <option value="completed">Completed</option>
                                    <option value="in-progress">In Progress</option>
                                    <option value="on-hold">On Hold</option>
                                </select>
                            </div>
                            <h4>Change Requests</h4>
                            <div id="change-requests"></div>
                        </div>
                        <div id="change-requests-item" style="display:none;">
                            <div class="form-group">
                                <label for="request-type">Request Type:</label>
                                <select id="request-type" class="form-control">

                                </select>
                            </div>
                            <div class="form-group">
                                <label for="request-item">Request Item:</label>
                                <select id="request-item" class="form-control">
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="request-status">Request Status:</label>
                                <select id="request-status" class="form-control">
                                </select>
                            </div>
                            <div class="form-group" id="version-option" style="display:none;">
                                <label for="request-completed-version">Completed Version:</label>
                                <input id="request-completed-version" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="request-short-detail">Short Description:</label>
                                <input id="request-short-detail" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label for="request-detail">Details:</label>
                                <textarea rows="4" id="request-detail" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="request-author">Name/Discord ID</label>
                                <input id="request-author" class="form-control"/>
                            </div>
                            <strong>Notes:</strong>
                            <div id="request-notes"></div>
                            <br/>
                            <div class="float-right">
                                <button id="change-request-back" class="btn btn-sunray">Back</button>
                                <button id="change-request-new-note" class="btn btn-sunray">New Note</button>
                                <button id="change-request-save" class="btn btn-success">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="new-note-modal" tabindex="-1" role="dialog" aria-labelledby="new-note-title" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="new-note-title">New Note</h5>
                        </div>
                        <div class="modal-body" id="new-note-body">
                            <div id="new-note-danger"  style="display: none;" class="alert alert-danger"  role="alert"></div>
                            <div id="new-note-warning" style="display: none;" class="alert alert-warning" role="alert"></div>
                            <div id="new-note-success" style="display: none;" class="alert alert-success" role="alert"></div>
                            <div class="form-group">
                                <label for="note-detail">Note Text:</label>
                                <textarea rows="4" id="note-detail" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer" id="new-note-footer">
                            <button type="button" id="cancel-note" class="btn btn-light" data-dismiss="modal">Close</button>
                            <button type="button" id="save-note" class="btn btn-light">Save Note</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br/>';

?>
