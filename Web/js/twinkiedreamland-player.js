var clan_data = [];
var clan_plots = [];
var clan_positions = [];
var player_plot = [];
var player_position = [];
var vehicle_positions = [];
var last_backup = [];

$(function() {
    $(document).on('click','#v-pills-player-tab',function(e) { e.preventDefault(); showPlayerInformation(); });
    $(document).on('click','#v-pills-clan-tab',function(e) { e.preventDefault(); showClanInformation(); });
    $(document).on('click','#v-pills-structures-tab',function(e) { e.preventDefault(); showStructuresInformation(); });

    if (initializeMiscreatedMap !== undefined && initializeMiscreatedMap === true && token !== false) {
        initializeMap();
        $('.leaflet-control-layers').hide();
        getData(function(data){
            drawTelemetry();
            console.log("Map Initialized.");
        });
    }
    else if (token === false) {
        console.log("No Token Defined.");
    }
});

function drawTelemetry() {
    $('#last-update').html('<i class="text-muted">Last Update: '+last_backup.BackupDateTime+'</i>');

    for (var c=0; c<clan_plots.length; c++) {
        console.log("ClanPlot: x:"+clan_plots[c].PosX +"y:"+clan_plots[c].PosY);
        if (isValid(clan_plots[c].PosX) && isValid(clan_plots[c].PosY)) {
            var msg = "<strong>"+clan_plots[c].Name+"'s Property</strong><br/>"+
            clan_plots[c].OverallObjects+" Objects<br/>"+
            "Health: "+clan_plots[c].Health+"%";

            var marker = L.marker(XYToLatLng([clan_plots[c].PosX,clan_plots[c].PosY]), {icon: getIcon('PlotSignPacked'), riseOnHover: true}).bindPopup(msg);
            icon_clan_plot.push(marker);
        }
    }

    //Clan Positions
    for (var c=0; c<clan_positions.length; c++) {
        if (isValid(clan_positions[c].PosX) && isValid(clan_positions[c].PosY)) {
            var msg = '<strong>'+clan_positions[c].Name+'</strong><br/>'+
            'Radiation Level: '+clan_positions[c].Radiation+'%<br/>'+
            'Temperature: '+clan_positions[c].Temperature+'&deg;C<br/>'+
            'Water: '+clan_positions[c].Water+'%<br/>'+
            'Food: '+clan_positions[c].Food+'%<br/>'+
            'Health: '+clan_positions[c]['Health']+'%<br/>'+
            'Last Seen: '+clan_positions[c]['LastSeen'];
            console.log("ClanPositions: x:"+clan_positions[c].PosX +" y:"+clan_positions[c].PosY+" player:"+clan_positions[c].Name);
            var marker = L.marker(XYToLatLng([clan_positions[c].PosX,clan_positions[c].PosY]), {icon: getIcon('marker_icon_green'), riseOnHover: true}).bindPopup(msg);
            icon_clan_position.push(marker);
        }
    }

    var html = ''+
    '<table id="clan-info-table" class="table td-data-table">'+
    '   <tbody>'+
    '       <tr><td class="w-50">Clan Name</td><td class="w-50">'+(clan_data.InClan == true?clan_data.ClanName:"None")+'</td></tr>'+
    '       <tr><td class="w-50">Members</td><td class="w-50">';
                for (var m=0; m<clan_positions.length; m++) {
                    html+= '<a href="https://steamcommunity.com/profiles/[U:1:'+clan_positions[m].AccountID+']" target="_blank">'+clan_positions[m].Name+'</a><br/>';
                }
            html +='</td></tr>'+
    '       </tbody>'+
    '   </tbody>'+
    '</table>';
    $('#clan-info').html(html);

    //Player Plot
    var html = ''+
    '<table id="player-structures-info-table" class="table td-data-table">'+
    '    <tr><td class="w-50">Base Structure Count</td><td class="w-50">'+player_plot.OverallObjects+'</td></tr>'+
    '    <tr><td class="w-50">Overall Health</td><td class="w-50">'+player_plot.Health+' %</td></tr>'+
    '</table>';
    $('#structures-info').html(html);

    var msg = "<strong>"+player_plot.Name+"'s Property</strong><br/>"+
    player_plot.OverallObjects+" Objects<br/>"+
    "Health: "+player_plot.Health+"%";

    if (player_plot.PosX !== null && player_plot.PosY !== null) {
        var marker = L.marker(XYToLatLng([player_plot.PosX,player_plot.PosY]), {icon: getIcon('PlotSignPacked'), riseOnHover: true}).bindPopup(msg);
        icon_player_plot.push(marker);
    }

    //Player Position
    var html = ''+
    '<table id="player-info-table" class="table td-data-table">'+
    '    <tr><td class="w-50">Name</td><td class="w-50">'+player_position.Name+'</td></tr>'+
    '    <tr><td class="w-50">Radiation Level</td><td class="w-50">'+player_position.Radiation+' %</td></tr>'+
    '    <tr><td class="w-50">Temperature</td><td class="w-50">'+player_position.Temperature+' &deg;C</td></tr>'+
    '    <tr><td class="w-50">Water</td><td class="w-50">'+player_position.Water+' %</td></tr>'+
    '    <tr><td class="w-50">Food</td><td class="w-50">'+player_position.Food+' %</td></tr>'+
    '    <tr><td class="w-50">Health</td><td class="w-50">'+player_position.Health+' %</td></tr>'+
    '</table>';
    $('#player-info').html(html);

    var msg = '<strong>'+player_position.Name+'</strong><br/>'+
    'Radiation Level: '+player_position.Radiation+'%<br/>'+
    'Temperature: '+player_position.Temperature+'&deg;C<br/>'+
    'Water: '+player_position.Water+'%<br/>'+
    'Food: '+player_position.Food+'%<br/>'+
    'Health: '+player_position.Health+'%<br/>'+
    'Last Seen: '+player_position.LastSeen;

    var marker = L.marker(XYToLatLng([player_position.PosX,player_position.PosY]), {icon: getIcon('marker_icon'), riseOnHover: true}).bindPopup(msg);
    icon_player_position.push(marker);

    //Player Vehicles
    for (var v=0; v<vehicle_positions.length; v++) {
        var msg = "<strong>"+vehicle_positions[v].Name+"</strong><br/>"+
        (vehicle_positions[v].Engine?"Oil Level: "+vehicle_positions[v].Data.oil+"%<br/>":"")+
        (vehicle_positions[v].Engine?"Fuel Level: "+vehicle_positions[v].Data.dieselfuel+"%<br/>":"")+
        "Abandoned In: "+calculateTimeLeft(vehicle_positions[v].AbandonTimer)+"<br/>";

        var marker = L.marker(XYToLatLng([vehicle_positions[v].PosX,vehicle_positions[v].PosY]), {icon: getIcon(vehicle_positions[v].Category), riseOnHover: true}).bindPopup(msg);
        icon_vehicle_position.push(marker);
    }

    initializeMap();
}

function getData(callback) {
    if (token !== false) {
        getLastBackup(function(data) {
            if (data.Status) {
                last_backup = data.LastBackup;
            }
            getClanPlots(function(data) {
                if (data.Status) {
                    clan_plots = data.Plots;
                }

                getClanPositions(function(data) {
                    if (data.Status) {
                        clan_positions = data.Positions;
                        clan_data = data;
                    }

                    getPlayerPlot(function(data) {
                        if (data.Status) {
                            player_plot = data.Plot;
                        }

                        getPlayerPosition(function(data) {
                            if (data.Status) {
                                player_position = data.Position;
                            }

                            getVehiclePositions(function(data) {
                                if (data.Status) {
                                    vehicle_positions = data.Vehicles;
                                    callback(true);
                                }
                            });
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

function getLastBackup(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/stats.php?param=getLastBackup&token='+token, function(data) {
        callback(data);
    });
}

function getClanPlots(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/map.php?param=getClanPlots&accountID='+accountId+'&token='+token, function(data) {
        callback(data);
    });
}

function getClanPositions(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/map.php?param=getClanPositions&accountID='+accountId+'&token='+token, function(data) {
        callback(data);
    });
}

function getPlayerPlot(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/map.php?param=getPlayerPlot&accountID='+accountId+'&token='+token, function(data) {
        callback(data);
    });
}

function getPlayerPosition(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/map.php?param=getPlayerPosition&accountID='+accountId+'&token='+token, function(data) {
        callback(data);
    });
}

function getVehiclePositions(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/map.php?param=getVehiclePositions&accountID='+accountId+'&token='+token, function(data) {
        callback(data);
    });
}

function showPlayerInformation() {
    console.log("Clicked showPlayerInformation");
    $('#page-heading').html("Player Information");
    $('#player-info').show();
    $('#clan-info').hide();
    $('#structures-info').hide();
}

function showClanInformation() {
    $('#page-heading').html("Clan Information");
    $('#player-info').hide();
    $('#clan-info').show();
    $('#structures-info').hide();
}

function showStructuresInformation() {
    $('#page-heading').html("Structure Information");
    $('#player-info').hide();
    $('#clan-info').hide();
    $('#structures-info').show();
}

function initializeMap() {
    var playerPlot = L.layerGroup(icon_player_plot);
    var playerPos = L.layerGroup(icon_player_position);
    var clanPlots = L.layerGroup(icon_clan_plot);
    var clanPos = L.layerGroup(icon_clan_position);
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
        "Player Plots": playerPlot,
        "Player Position": playerPos,
        "Clan Plots": clanPlots,
        "Clan Position": clanPos,
        "Vehicles": vehiclePos
    };

    if (miscreatedmap) {
        miscreatedmap.remove();
        miscreatedmap = false;
    }

    miscreatedmap = L.map('miscreated-map', {
        crs: L.CRS.pr,
        layers: [mapimage, playerPos, playerPlot, clanPos, clanPlots, vehiclePos]
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

function isValid(val) {
    if (val == null)
        return false;
    if (val == undefined)
        return false;
    return true;
}
