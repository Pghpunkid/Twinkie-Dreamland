var initializeMiscreatedAdminMap = true;
var player_plots=[];
var player_positions=[];
var vehicle_positions=[];
var backups = [];
var current_backup = [];

var rolling_player_stats = [];
var rolling_player_week_stats = [];
var active_players = 0;
var inactive_players = 0;
var total_players = 0;

var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

var requests = false;
var activeRequestID = false;
var timer = false;

const requestStatus = [
    {desc: "Initial",                                           value:"Initial"},
    {desc: "Canceled",                                          value:"Canceled"},
    {desc: "In Progress",                                       value:"In Progress"},
    {desc: "Review",                                            value:"Review"},
    {desc: "On Hold",                                           value:"On Hold"},
    {desc: "Complete",                                          value:"Complete"}
];

const requestTypes = [
    {desc: "Mod Creation/Change",                               value:"Mod Creation/Change"},
    {desc: "Bug Fix",                                           value:"Bug Fix"},
    {desc: "Web Site Change/Update",                            value:"Web Site Change/Update"}
];

const requestItems = [
    {desc: "Primary Weapon (Firearm)",                          value:"Primary Weapon (Firearm)"},
    {desc: "Primary Weapon (Melee)",                            value:"Primary Weapon (Melee)"},
    {desc: "Secondary Weapon (Firearm)",                        value:"Secondary Weapon (Firearm)"},
    {desc: "Secondary Weapon (Melee)",                          value:"Secondary Weapon (Melee)"},
    {desc: "Vehicle",                                           value:"Vehicle"},
    {desc: "Base Building (Size)",                              value:"Base Building (Size)"},
    {desc: "Base Building (Materials/Equipment/Decorations)",   value:"Base Building (Materials/Equipment/Decorations)"},
    {desc: "Food Or Drink Items",                               value:"Food Or Drink Items"},
    {desc: "Medical Items",                                     value:"Medical items"},
    {desc: "A.I. (Zombies/Animals)",                            value:"A.I. (Zombies/Animals)"},
    {desc: "Weather/Time Cycles",                               value:"Weather/Time Cycles"},
    {desc: "Clothing Items",                                    value:"Clothing Items"},
    {desc: "Towable Items",                                     value:"Towable Items"},
    {desc: "Supply Drops/Crashes",                              value:"Supply Drops/Crashes"},
    {desc: "Player/UI Function",                                value:"Player/UI Function"},
    {desc: "Website Feature Update",                            value:"Website Feature Update"}
];

$(function() {
    $(document).on('click','#v-pills-admin-maps-tab',function(e) { e.preventDefault(); showMaps(); });
    $(document).on('click','#v-pills-admin-change-requests-tab',function(e) { e.preventDefault(); showChangeRequests(); });
    $(document).on('click','#v-pills-admin-player-stats-tab',function(e) { e.preventDefault(); showPlayerStats(); });

    $(document).on('change','#cr-list-filter', function(e) { e.preventDefault(); onCRFilterChange($(this).val()); });
    $(document).on('change','#request-status', function(e) { e.preventDefault(); onCRStatusChange($(this).val()); });
    $(document).on('click','.request', function(e) { e.preventDefault(); onCRClick($(this).attr('data-request-id')); });
    $(document).on('click', '#change-request-back', function(e) { e.preventDefault(); onCRBack(); });
    $(document).on('click', '#change-request-new-note', function(e) { e.preventDefault(); onCRNewNote(); });
    $(document).on('click', '#change-request-save', function(e) { e.preventDefault(); onCRSave(); });
    $(document).on('click', '.noteDel', function(e) { e.preventDefault(); onCRDeleteNote($(this).attr('data-note-id')); });
    $(document).on('click', '#save-note', function(e) { e.preventDefault(); onCRSaveNewNote(); });

    if (initializeMiscreatedAdminMap !== undefined && initializeMiscreatedAdminMap === true && token !== false) {
        initializeMap();
        console.log("A");
        getData(function(result) {
            if (result) {
                initializeDailyChart();
                initializeWeeklyChart();
                populateActivePlayers();
                drawTelemetry();
                console.log("Admin Map Initialized.");
                $('#system-message').hide();
            }
            else {
                $('#system-message').show();
            }
        });
    }

    populateForm();
});

function getData(callback) {
    if (token !== false) {
        getBackups(function(data) {
            if (data.Status) {
                backups = data.Backups;
                current_backup = data.CurrentBackup;
            }
            getRecentPlayers(function(data) {
                if (data.Status) {
                    player_positions = data.Players;
                }
                getStructures(function(data) {
                    if (data.Status) {
                        player_plots = data.Structures;
                    }
                    getVehicles(function(data) {
                        if (data.Status) {
                            vehicle_positions = data.Vehicles;
                        }
                        getPlayerStats(function(data) {
                            if (data.Status) {
                                rolling_player_stats = data.Players;

                                getPlayerWeekStats(function(data) {
                                    if (data.Status) {
                                        rolling_player_week_stats = data.Players;

                                        getActivePlayerStats(function(data) {
                                            if (data.Status) {
                                                active_players = data.Active;
                                                inactive_players = data.Inactive;
                                                total_players = data.Total;
                                                callback(true);
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    });
                });
            });
        });
    }
    else {
        callback(false);
    }
}

function getBackups(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/stats.php?param=getBackups&token='+token, function(data) {
        callback(data);
    });
}

function getPlayers(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/players.php?param=getAll&token='+token, function(data) {
        callback(data);
    });
}

function getRecentPlayers(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/players.php?param=getRecent&token='+token, function(data) {
        callback(data);
    });
}

function getStructures(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/structures.php?param=getAllStructures&token='+token, function(data) {
        callback(data);
    });
}

function getVehicles(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/vehicles.php?param=getAll&token='+token, function(data) {
        callback(data);
    });
}

function getPlayerStats(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/stats.php?param=getRecentStats&token='+token, function(data) {
        callback(data);
    });
}

function getPlayerWeekStats(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/stats.php?param=getBiWeeklyStats&token='+token, function(data) {
        callback(data);
    });
}

function getActivePlayerStats(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/stats.php?param=getActivePlayerStats&token='+token, function(data) {
        callback(data);
    });
}

function initializeDailyChart() {
    var hours = [];
    var player_count = [];
    for (var r=0; r<rolling_player_stats.length; r++) {
        var dataDate = new Date(rolling_player_stats[r].Hour);

        var hour = dataDate.getHours();
        var ampm = "AM";
        if (hour >= 12) {
            ampm="PM";
        }
        if (hour >= 13) {
            hour-=12;
        }
        if (hour == 0) {
            hour=12;
        }

        hours.push(hour+" "+ampm);
        player_count.push(rolling_player_stats[r].PlayerCount);
    }

    var ctx = document.getElementById("rolling_players_day").getContext("2d");
    ctx.canvas.width = window.innerWidth;
    //ctx.canvas.height = window.innerHeight * 0.9;
    ctx.canvas.height = 500;
    var myChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: hours,
            datasets: [{
                label: "Player Count",
                data: player_count,
                backgroundColor: "rgba(222, 163, 55, 1)",
                borderColor: "rgba(222, 163, 55, 1)",
                borderWidth: 1
            }],

        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 1,
                        stepSize: 1,
                        fontColor: 'white'
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontColor: 'white'
                    }
                }]
            },
            legend: {
               labels: {
                   // This more specific font property overrides the global property
                   fontColor: 'white'
               }
           },
        }
    });
}


function initializeWeeklyChart() {
    var days = [];
    var player_count = [];
    for (var r=0; r<rolling_player_week_stats.length; r++) {
        var dataDate = new Date(rolling_player_week_stats[r].Date);

        days.push(months[dataDate.getMonth()]+" "+(dataDate.getDate()+1));
        player_count.push(rolling_player_week_stats[r].PlayerCount);
    }

    var ctx = document.getElementById("rolling_players_week").getContext("2d");
    ctx.canvas.width = window.innerWidth;
    //ctx.canvas.height = window.innerHeight * 0.9;
    ctx.canvas.height = 500;
    var myChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: days,
            datasets: [{
                label: "Player Count",
                data: player_count,
                backgroundColor: "rgba(222, 163, 55, 1)",
                borderColor: "rgba(222, 163, 55, 1)",
                borderWidth: 1
            }],
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        precision: 1,
                        stepSize: 1,
                        fontColor: 'white'
                    }
                }],
                xAxes: [{
                    ticks: {
                        fontColor: 'white'
                    }
                }]
            },
            legend: {
               labels: {
                   // This more specific font property overrides the global property
                   fontColor: 'white'
               }
           },
        }
    });
}

function populateActivePlayers() {
    $('#total_players').html(total_players);
    $('#total_active_players').html(active_players);
    $('#total_inactive_players').html(inactive_players);
}

function drawTelemetry() {
    $('#last-update').html('<i class="text-muted">Last Update: '+current_backup.BackupDateTime+'</i>');

    if (player_plots.length != 0) {
        for (var p=0; p<player_plots.length; p++) {
            var msg = "<strong>"+player_plots[p].ClassName+"</strong><br/>"+
            "Owned: "+(!player_plots[p].Name?"No":"Yes")+"<br/>"+
            (player_plots[p].Name?"Placed By: "+player_plots[p]['Name']+"<br/>":"")+
            "Despawns In: "+calculateTimeLeft(player_plots[p]['AbandonTimer']);
            var marker = L.marker(XYToLatLng([player_plots[p].PosX,player_plots[p].PosY]), {icon: getIcon(player_plots[p].ClassName), riseOnHover: true}).bindPopup(msg);
            icon_player_plot.push(marker);
        }
    }

    if (player_positions.length != 0) {
        var playerList = "<table class='table td-data-table'>";
        playerList += "<thead><tr><th>Player Name</th><th>Steam ID</th><th>Last Seen</th></tr></thead><tbody>";
        for (var p=0; p<player_positions.length; p++) {

            playerList += "<tr><td>"+player_positions[p]['Name']+"</td><td>"+player_positions[p]['SteamID']+"</td><td>"+player_positions[p]['LastSeen']+"</td></tr>";
            var msg= '<strong>'+player_positions[p]['Name']+'</strong><br/>'+
            'Radiation Level: '+player_positions[p]['Radiation']+'%<br/>'+
            'Temperature: '+player_positions[p]['Temperature']+'&deg;C<br/>'+
            'Water: '+player_positions[p]['Water']+'%<br/>'+
            'Food: '+player_positions[p]['Food']+'%<br/>'+
            'Health: '+player_positions[p]['Health']+'%<br/>'+
            'Last Seen: '+player_positions[p]['LastSeen'];
            var marker = L.marker(XYToLatLng([player_positions[p].PosX,player_positions[p].PosY]), {icon: getIcon('marker_icon'), riseOnHover: true}).bindPopup(msg);
            icon_player_position.push(marker);
        }
        playerList += "</tbody></table>";
        $('#recent-players').html(playerList);
    }

    for (var v=0; v<vehicle_positions.length; v++) {
        if (vehicle_positions[v]['Name'] == "Bicycle" || vehicle_positions[v]['Name'] == "Dirtbike") {
            vehicle_positions[v]['Name'] = "Quadbike";
        }
        var msg="<strong>"+vehicle_positions[v]['Name']+"</strong><br/>"+
        (vehicle_positions[v]['Engine']?"Oil Level: "+vehicle_positions[v]['Data']['oil']+"%<br/>":"")+
        (vehicle_positions[v]['Engine']?"Fuel Level: "+vehicle_positions[v]['Data']['dieselfuel']+"%<br/>":"")+
        "Abandoned In: "+calculateTimeLeft(vehicle_positions[v]['AbandonTimer'])+"<br/>";
        var marker = L.marker(XYToLatLng([vehicle_positions[v].PosX,vehicle_positions[v].PosY]), {icon: getIcon(vehicle_positions[v].Category), riseOnHover: true}).bindPopup(msg);
        icon_vehicle_position.push(marker);
    }

    initializeMap();
}

function showMaps() {
    $('#page-heading').html("Server Maps");
    $('#backup-page').hide();
    $('#map').show();
    $('#player-stats-page').hide();
    $('#change-request-page').hide();
    initializeMap();
}

function showPlayerStats() {
    $('#page-heading').html("Player Stats");
    $('#backup-page').hide();
    $('#map').hide();
    $('#player-stats-page').show();
    $('#change-request-page').hide();
}

function showChangeRequests() {
    $('#page-heading').html("Change Requests");

    $('#change-requests-list').show();
    $('#change-requests-item').hide();

    $('#backup-page').hide();
    $('#map').hide();
    $('#player-stats-page').hide();
    $('#change-request-page').show();
    getChangeRequests('active');
}

function initializeMap() {
    var playerPlot = L.layerGroup(icon_player_plot);
    var playerPos = L.layerGroup(icon_player_position);
    var vehiclePos = L.layerGroup(icon_vehicle_position);

    var mapimage = L.tileLayer('https://api.twinkiedreamland.com/map/mapAgent.php?mapName=island&Z={z}&X={x}&Y={y}', {
        maxZoom: 7,
        minZoom: 1,
        zoomControl: true,
        zoomSnap: 1,
        noWrap: true,
        attribution: '<a href="https://twinkiedreamland.com">Twinkie Dreamland</a>',
        crs: L.CRS.Simple,
        noWrap: true,
        maxBounds: [[-90, -180], [90, 180]],
        tms: true,
        attributionControl: false,
        trackResize: true,
        renderer: L.Canvas,
        center: { lat: -47, lng: -23 }
    });

    var baseLayers = {
        "Map": mapimage
    };

    var overlays = {
        "Structures": playerPlot,
        "Players": playerPos,
        "Vehicles": vehiclePos
    };

    if (miscreatedmap) {
        miscreatedmap.remove();
        miscreatedmap = false;
    }

    miscreatedmap = L.map('miscreated-map', {
        crs: L.CRS.pr,
        layers: [mapimage, playerPos, playerPlot, vehiclePos]
    });

    L.control.layers(baseLayers, overlays, {hideSingleBase: true}).addTo(miscreatedmap);
    miscreatedmap.setView(XYToLatLng([mapSize/2,mapSize/2]), 2);
}

function XYToLatLng(xy) {
    var x = xy[0];
    var y = xy[1];

    var scaleFactor = 0.955;

    x = (x / 4);
    y = (y / 4) - 2048;

    x = x + 48;
    y = y - 48;

    x = x * scaleFactor;
    y = y * scaleFactor;

    var coords = new Array(y,x);
    return coords;
}

function LatLngToXY(latlng) {
    var x = latlng.lng;
    var y = latlng.lat;

    x = (x * 4);
    y = (y * 4) + 8196;

    x = x - 60;
    y = y - 40;

    var coords = new Array(x,y);
    return coords;
}

function calculateTimeLeft(seconds) {
    var hours = Math.floor(seconds/3600);
    var minutes = Math.floor(seconds/60) % 60;

    var timeStr = "";
    if (hours > 0) {
        timeStr += hours+"h ";
    }
    timeStr += minutes+"m";
    return timeStr;
}

function onCRFilterChange(filter) {
    getChangeRequests(filter);
}

function onCRClick(id) {
    activeRequestID = id;
    var request = requests[activeRequestID];

    console.log("RequestID:"+request.RequestID);
    $('#request-type').val(request.RequestType);
    $('#request-item').val(request.RequestItem);
    $('#request-short-detail').val(request.RequestShortDescription);
    $('#request-detail').val(request.RequestDescription.replaceAll('&quot;','"'));
    $('#request-status').val(request.Status);
    $('#request-author').val(request.Requestor);
    $('#request-completed-version').val(request.CompletedVersion);

    if (request.Status == 'Complete') {
        $('#version-option').show();
    }
    else {
        $('#version-option').hide();
    }

    var notes = "";
    if (request.Notes.length > 0) {
        for (var n=0; n<request.Notes.length; n++) {
            var note = request.Notes[n];
            var deleteOption = '<span class="badge badge-danger noteDel" data-note-id="'+note.RequestNoteID+'">Delete</span>';
            notes += "<hr/><div class='row'><div class='col-sm-3'>"+note.NoteAuthor+"<br/>"+note.NoteDateTimeEng+'<br/>'+deleteOption+"</div><div class='col-sm-9'>"+note.NoteDescription+"</div></div>";
        }
    }
    else {
        notes = "<div class='center'>No Notes</div>";
    }
    notes+="<br/>";

    console.log(notes);
    $('#request-notes').html(notes);
    $('#change-requests-list').hide();
    $('#change-requests-item').show();
}

function onCRBack() {
    activeRequestID = false;
    getChangeRequests($('#cr-list-filter').val());
    $('#change-requests-list').show();
    $('#change-requests-item').hide();
}

function onCRSave() {
    $('#request-type').attr('disabled','disabled');
    $('#request-item').attr('disabled','disabled');
    $('#request-short-detail').attr('disabled','disabled');
    $('#request-detail').attr('disabled','disabled');
    $('#request-status').attr('disabled','disabled');
    $('#request-author').attr('disabled','disabled');
    $('#request-completed-version').attr('disabled','disabled');

    $('#change-request-back').attr('disabled','disabled');
    $('#change-request-new-note').attr('disabled','disabled');
    $('#change-request-save').attr('disabled','disabled');

    $.post(
        'change-requests-async.php',
        {
            "cmd":                      "updateCR",
            "requestID":                requests[activeRequestID].RequestID,
            "requestType":              $('#request-type').val(),
            "requestItem":              $('#request-item').val(),
            "requestShortDetail":       $('#request-short-detail').val(),
            "requestDetail":            $('#request-detail').val(),
            "requestStatus":            $('#request-status').val(),
            "requestAuthor":            $('#request-author').val(),
            "requestCompletedVersion":  $('#request-completed-version').val()
        },
        function(data) {
            if (data.status == "Ok") {
                alert("Change Request saved!");
                refreshCR();
            }
            else {
                alert("Unable to save Change Request:"+data.response);
            }

            $('#request-type').removeAttr('disabled');
            $('#request-item').removeAttr('disabled');
            $('#request-short-detail').removeAttr('disabled');
            $('#request-detail').removeAttr('disabled');
            $('#request-status').removeAttr('disabled');
            $('#request-author').removeAttr('disabled');
            $('#request-completed-version').removeAttr('disabled');

            $('#change-request-back').removeAttr('disabled');
            $('#change-request-new-note').removeAttr('disabled');
            $('#change-request-save').removeAttr('disabled');
        },
        'json'
    );
}

function onCRNewNote() {
    $('#note-detail').val('');
    $('#save-note').removeAttr('disabled');
    $('#new-note-success').html('');
    $('#new-note-success').hide();
    $('#new-note-danger').html('');
    $('#new-note-danger').hide();
    $('#new-note-warning').html('');
    $('#new-note-warning').hide();
    $('#cancel-note').removeAttr('disabled');
    $('#note-detail').removeAttr('disabled');
    $('#new-note-modal').modal({
        keyboard: false,
        show: true
    });
}

function onCRSaveNewNote() {
    var detail = $('#note-detail').val();
    if (detail == "") {
        $('#new-note-warning').html('Your note cannot be blank.');
        if (timer !== false) {
            clearTimeout(timer);
        }
        $('#new-note-warning').slideUp(500);
        $('#new-note-warning').slideDown(500);
        timer = setTimeout(function() {
            $('#new-note-warning').slideUp(500);
            timer = false;
        }, 3000);
        return;
    }
    $('#note-detail').attr('disabled','disabled');
    $('#save-note').attr('disabled','disabled');
    $('#cancel-note').attr('disabled','disabled');

    $.post(
        'change-requests-async.php',
        {
            "cmd":  "submitCRNote",
            "note": $('#note-detail').val(),
            "author": playerName,
            "requestID": requests[activeRequestID].RequestID
        },
        function(data) {
            if (data.status == "Ok") {
                $('#new-note-success').html('Note saved!');
                $('#new-note-success').slideDown(500);
                setTimeout(function() {
                    $('#new-note-modal').modal('hide');
                    refreshCR();
                },3000);

            }
            else {
                $('#new-note-danger').html('Unable to save note.<br/>'+data.response);
                $('#new-note-danger').slideDown(500);
                setTimeout(function() {
                    $('#new-note-modal').modal('hide');
                },3000)
            }
        },
        'json'
    );
}

function onCRDeleteNote(noteID) {
    console.log("onCRDeleteNote");
    $.post(
        'change-requests-async.php',
        {
            "cmd":  "deleteCRNote",
            "noteID": noteID,
            "requestID": requests[activeRequestID].RequestID
        },
        function(data) {
            if (data.status == "Ok") {
                console.log("onCRDeleteNote OK");
                refreshCR();
            }
            else {
                alert("There was an issue deleting that note."+data.response);
            }
        },
        'json'
    );
}

function onCRStatusChange(status) {
    if (status == 'Complete') {
        $('#version-option').show();
    }
    else {
        $('#version-option').hide();
    }
}

function refreshCR() {
    console.log('refreshCR:');
    var currentRequestID = requests[activeRequestID].RequestID;

    getChangeRequests($('#cr-list-filter').val(), function(result) {
        if (result) {
            for (var x=0; x<requests.length; x++) {
                if (currentRequestID == requests[x].RequestID) {
                    console.log("refreshCR:"+requests[x].RequestID);
                    onCRClick(x);
                    return;
                }
            }
        }
    });
}

function populateForm() {
    var html = '';
    for (var r=0; r<requestTypes.length; r++) {
        html += "<option value='"+requestTypes[r].value+"'>"+requestTypes[r].desc+"</option>";
    }
    $('#request-type').html(html);

    var html = '';
    for (var r=0; r<requestItems.length; r++) {
        html += "<option value='"+requestItems[r].value+"'>"+requestItems[r].desc+"</option>";
    }
    $('#request-item').html(html);

    var html = '';
    for (var r=0; r<requestStatus.length; r++) {
        html += "<option value='"+requestStatus[r].value+"'>"+requestStatus[r].desc+"</option>";
    }
    $('#request-status').html(html);
}

function getChangeRequests(filter, callback) {

    console.log("getChangeRequests");
    $.post(
        'change-requests-async.php',
        {
            'cmd': 'fetchAll',
            'filter': filter
        },
        function(data) {
            if (data.status == "Ok") {
                console.log("getChangeRequests OK");
                var html = '<table class="table table-light" id="cr-table"><thead><tr><th class="col-sm-5">Type</th><th class="col-sm-2">Author</th><th class="col-sm-3">Request</th><th class="col-sm-4">Status</th></tr></thead><tbody>';
                requests = data.requests;
                if (requests.length != 0) {
                    for (var r=0; r<requests.length; r++) {
                        var request = requests[r];
                        var status = request.Status;
                        if (request.Completed == 'Y') {
                            status += " - "+request.CompletedVersion;
                        }
                        html += '<tr data-request-id="'+r+'" class="request"><td>'+request.RequestType+" > "+request.RequestItem+"</td><td>"+request.Requestor+"</td><td>"+request.RequestShortDescription+"</td><td>"+status+"</td></tr>";
                    }
                }
                else {
                    html += '<tr><td class="center" colspan="4">No Change Requests To Show</td></tr>';
                }

                html += "</tbody></table>";
                $('#change-requests').html(html);
                if (callback != undefined) {
                    callback(true);
                }
            }
            else {
                alert(data.response);
                if (callback != undefined) {
                    callback(false);
                }
            }
        },
        'json'
    );
}
