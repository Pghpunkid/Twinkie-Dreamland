var player_positions = [];
var entity_positions = [];
var players = [];
var plot_radius = 150;
var icon_bases_position = new Array();
var icon_tent_position = new Array();
var icon_metal_parts_position = new Array();
var icon_metal_part_spawn = [
        //North Hayward Valley
        { "x": -304.5,     "y": 674.375 },
        { "x": -329.25,    "y": 717.625 },
        { "x": -348.75,    "y": 732.625 },
        { "x": -371.5,     "y": 731.75 },

        //Paint Shop
        { "x": -428.125,   "y": 498.1875 },
        { "x": -430.75,    "y": 462.875 },
        { "x": -447.25,    "y": 454.875 },
        { "x": -433,       "y":  424.625 },

        //Hayward Valley
        { "x": -514.75,    "y": 917.3125 },
        { "x": -537.75,    "y": 916.9375 },
        { "x": -549.25,    "y": 916.9375 },
        { "x": -553.125,   "y": 896.0625 },
        { "x": -553.125,   "y": 882.9375 },
        { "x": -600,       "y": 997.75 },
        { "x": -618.5,     "y": 997.25 },

        //Cedar Park
        { "x": -816,       "y": 876.9375 },
        { "x": -891.375,   "y": 1018.1875 },
        { "x": -913.5,     "y": 1019.3125 },
        { "x": -925.375,   "y": 1032.3125 },

        //Plane Crash
        { "x": -1075.9375, "y": 1002.59375 },

        //Clyde Hill
        { "x": -1108.5,    "y": 1221.875 },
        { "x": -651.375,   "y": 1322.8125 },
        { "x": -742.125,   "y": 1365.8125 },

        //Central
        { "x": -1303.3125, "y": 1235.78125 },
        { "x": -1384.875,  "y": 1207.8125 },

        //Warehouses
        { "x": -1108,      "y": 1502.6875 }, // "x":6053.92, "y":3726.6
        { "x": -1117.125,  "y": 1488.6875 },

        //Cape Bay
        { "x": -832,       "y": 1764.375 },
        { "x": -860.75,    "y": 1763.375 },
        { "x": -907.75,    "y": 1800.625 },

        //Pine Parks
        { "x": -1451.125,  "y": 983.9375 },

        //Brightmoor
        { "x": -1469.75,   "y": 1398.375 },
        { "x": -1501.5,    "y": 1381.375 },

        //Lumber Yard
        { "x": -1698.1875, "y": 1432.53125 },
        { "x": -1703.0625, "y": 1442.09375 },
        { "x": -1712,      "y": 1438.21875 },
        { "x": -1711.8125, "y": 1425.59375 },

        //Brightmoor Tower
        { "x": -1755,      "y": 1552.25 },

        //Pinecrest Derby
        { "x": -1679.25,   "y": 1097.625 },
        { "x": -1715.5,    "y": 1117.875 },

        //Pinecrest
        { "x": -1618,      "y": 998.875 },
        { "x": -1700.25,   "y": 898.875 },
        { "x": -1729.125,  "y": 707.1875 },

        //Airport
        { "x": -1849.125, "y": 625.9375 },
        { "x": -1841.375, "y": 615.8125 },
        { "x": -1849.625, "y": 582.8125 },
        { "x": -1806,     "y": 517.375 },

        //Stairway to Hope
        { "x": -1757.5,   "y": 335.875 },

        //Rocky Ripple
        { "x": -1636.75,  "y": 374.4375 },
        { "x": -1631.125, "y": 388.5625 },
        { "x": -1611.25,  "y": 383.125 },
        { "x": -1582.25,  "y": 403.375 },

        //Horseshoe Beach
        { "x": -1493,     "y": 321.875 },
        { "x": -1473.75,  "y": 332.875 },
        { "x": -1456.25,  "y": 316.125 },

        //Woodhaven South
        { "x": -1314.625, "y": 316.78125 },

        //Turtle Bay
        { "x": -1026.25,  "y": 103.375 },
        { "x": -1006.75,  "y": 122.625 },
        { "x": -996.75,   "y": 138.125 }
];
var primary_vehicles = [
    "f100truck",
    "suv_basic",
    "sedan_police",
    "sedan_taxi",
    "sedan_taxi_engoa",
    "sedan_taxi_blix",
    "sedan_base",
    "dune_buggy",
    "truck_semi",
    "truck_5ton",
    "armored_truck_army",
    "armored_truck_swat"
];
var secondary_vehicles = [
    "fishing_boat",
    "jetski",
    "quadbike",
    "dirtbike",
    "bicycle",
    "tractor",
    "towcar"
];
var last_backup = [];

var playerPos = false;
var entityPos = false;
var vehiclePos = false;
var basesPos = false;

var playerSelected = false;
var selectedPlayerData = false;
var selectedMarker = false;

var miscreatedMapControl = false;

$(function() {
    pushLog("Page Loaded.");
    if (token === false) {
        console.log("No Token Defined.");
        pushLog("No Token Defined.");
    }

    $(document).on('click', function(e) {
        var eclass = $(this).attr('class');
        var eid = $(this).attr('id');
        console.log("Document Click:"+eclass+" "+eid);
    });
});

function initializeMap() {
    pushLog("initializeMap()");
    drawMap();
    updateMapData();
}

function updateMapData() {
    getData(function(result){
        if (result) {
            pushLog("Data Fetched.");
            drawTelemetry();
        }
        else {
            pushLog("Data Fetched Failed.");
        }
    });

    setTimeout(function() {
        updateMapData();
    }.bind(this), 15000);
    console.log("Refreshing in 15 seconds..");
    pushLog("Refreshing in 15 seconds..");
}

function getPlayerBySteamID(steamId) {
    for (var p=0; p<players.length; p++) {
        if (players[p].SteamID == steamId) {
            return players[p];
        }
    }
    return false;
}

function isInRangeOfPlotSign(x, y) {
    var idx = -1;
    var idx_dist = 150;

    for (var e=0; e<entity_positions.length; e++) {
        if (entity_positions[e].class == "PlotSign") {
            var xDif = Math.abs(entity_positions[e].x - x);
            var yDif = Math.abs(entity_positions[e].y - y);
            var distance = Math.sqrt((xDif * xDif) + (yDif * yDif));

            if (distance < idx_dist) {
                idx_dist = distance;
                idx = e;
            }
        }
    }

    if (idx == -1)
        return false;
    else
        return entity_positions[idx];
}

function getVehicleGrade(vehicle) {
    for (var v=0; v<primary_vehicles.length; v++) {
        if (vehicle == primary_vehicles[v]) {
            return "primary";
        }
    }

    for (var v=0; v<secondary_vehicles.length; v++) {
        if (vehicle == secondary_vehicles[v]) {
            return "secondary";
        }
    }
    return false;
}

function drawTelemetry() {
    icon_entity_position = new Array();
    icon_player_position = new Array();
    icon_vehicle_position = new Array();
    icon_bases_position = new Array();
    icon_metal_parts_position = new Array();
    icon_tent_position = new Array();

    //Metal Parts
    for (var p=0; p<icon_metal_part_spawn.length; p++) {
        var msg = '<strong>Metal Base Part Spawn</strong><br/>';
        var marker = L.marker([icon_metal_part_spawn[p].x,icon_metal_part_spawn[p].y], {icon: getIcon('marker_icon_green'), riseOnHover: true}).bindPopup(msg, {closeButton: false});
        icon_metal_parts_position.push(marker);
    }

    //Player Position
    var markerFound = false;
    for (var p=0; p<player_positions.length; p++) {
        var msg = '<strong>'+player_positions[p].name+'</strong><br/>'+
        'Health: '+player_positions[p].health+'%<br/>';

        var marker = L.marker(XYToLatLng([player_positions[p].x,player_positions[p].y]), {icon: getIcon('marker_icon'), riseOnHover: true}).bindPopup(msg, {closeButton: false}).on('click', function(e) {
            playerClick(e);
        });
        marker.miscreatedData = player_positions[p];

        if (playerSelected != false && selectedPlayerData.id == player_positions[p].id) {
            selectedMarker = marker;
            markerFound = true;
        }

        icon_player_position.push(marker);
    }

    if (playerSelected && !markerFound) {
        playerSelected = false;
        selectedPlayerData = false;
        selectedMarker = false;
    }

    //Entity Positions
    for (var e=0; e<entity_positions.length; e++) {
        var msg = "<strong>"+entity_positions[e].class+"</strong><br/>"+
        "Type: "+entity_positions[e].type+"<br/>"+
        "Coords: X:"+entity_positions[e].x+" Y:"+entity_positions[e].y+" Z:"+entity_positions[e].z+"<br/>";

        if (entity_positions[e].type == "vehicle") {
            if (getIcon(entity_positions[e].class) == undefined) {
                console.log("Icon Undefined:"+entity_positions[e].class);
            }

            var plot = isInRangeOfPlotSign(entity_positions[e].x,entity_positions[e].y);
            if (plot !== false) {
                var owner = getPlayerBySteamID(plot.owner);
                var grade = getVehicleGrade(entity_positions[e].class);

                if (owner !== false) {
                    if (grade == "primary") {
                        players[owner.idx].primary_vehicles_owned++;
                    }
                    else if (grade == "secondary") {
                        players[owner.idx].secondary_vehicles_owned++;
                    }

                    msg = msg + "Owner: "+owner.Name+"<br/>";
                }
            }

            var marker = L.marker(XYToLatLng([entity_positions[e].x,entity_positions[e].y]), {icon: getIcon(entity_positions[e].class), riseOnHover: true}).bindPopup(msg).on('click', function(e) {
                entityClick(e);
            });
            marker.miscreatedData = entity_positions[e];
            icon_vehicle_position.push(marker);
        }
        else if (entity_positions[e].type == "basepart") {
            if (entity_positions[e].class == "PlotSign") {
                if (getIcon('PlotSignPacked') == undefined) {
                    console.log("Icon Undefined:"+entity_positions[e].class);
                }
                var owner = getPlayerBySteamID(entity_positions[e].owner);
                if (owner !== false) {
                    msg = msg + "Owner: "+owner.Name+"<br/>";
                }
                else {
                    msg = msg + "Owner ID: "+entity_positions[e].owner+"<br/>";
                }
                var marker = L.marker(XYToLatLng([entity_positions[e].x,entity_positions[e].y]), {icon: getIcon('PlotSignPacked'), riseOnHover: true}).bindPopup(msg).on('click', function(e) {
                    entityClick(e);
                });
                marker.miscreatedData = entity_positions[e];
                icon_bases_position.push(marker);
            }
        }
        else if (entity_positions[e].type == "tent") {
            var marker = L.marker(XYToLatLng([entity_positions[e].x,entity_positions[e].y]), {icon: getIcon(entity_positions[e].class), riseOnHover: true}).bindPopup(msg).on('click', function(e) {
                entityClick(e);
            });
            marker.miscreatedData = entity_positions[e];
            icon_tent_position.push(marker);
        }
        else if (entity_positions[e].class == "AirDropCrate") {
            msg += "<br/>";
            if (entity_positions[e].inventory.length > 0) {
                var l=0;
                for (var i=0; i<entity_positions[e].inventory.length; i++) {
                    if (getIcon(entity_positions[e].inventory[i].item) == undefined) {
                        console.log("Icon Undefined:"+entity_positions[e].inventory[i].item);
                    }
                    msg += "<img src='"+getIcon(entity_positions[e].inventory[i].item).options.iconUrl+"' title='"+entity_positions[e].inventory[i].item+"' width='32px' />";
                    l++;
                    if (l % 5 == 0 && l > 0) {
                        msg += '<br/>';
                    }
                }
            }
            else {
                msg += "<i>Empty</i>";
            }
            if (getIcon('AirDropCrate') == undefined) {
                console.log("Icon Undefined:"+entity_positions[e].class);
            }
            var marker = L.marker(XYToLatLng([entity_positions[e].x,entity_positions[e].y]), {icon: getIcon('AirDropCrate'), riseOnHover: true}).bindPopup(msg).on('click', function(e) {
                entityClick(e);
            });
            marker.miscreatedData = entity_positions[e];
            icon_entity_position.push(marker);
        }
        else if (entity_positions[e].class == "AirPlaneCrash") {
            if (getIcon('PlaneCrash') == undefined) {
                console.log("Icon Undefined:"+entity_positions[e].class);
            }
            var marker = L.marker(XYToLatLng([entity_positions[e].x,entity_positions[e].y]), {icon: getIcon('PlaneCrash'), riseOnHover: true}).bindPopup(msg).on('click', function(e) {
                entityClick(e);
            });
            marker.miscreatedData = entity_positions[e];
            icon_entity_position.push(marker);
        }
        else if (entity_positions[e].class == "WoodCrate") {
            msg += "<br/>";
            if (entity_positions[e].inventory.length > 0) {
                var l=0;
                for (var i=0; i<entity_positions[e].inventory.length; i++) {
                    if (getIcon(entity_positions[e].inventory[i].item) == undefined) {
                        console.log("Icon Undefined:"+entity_positions[e].inventory[i].item);
                    }
                    msg += "<img src='"+getIcon(entity_positions[e].inventory[i].item).options.iconUrl+"' title='"+entity_positions[e].inventory[i].item+"' width='32px' />";
                    l++;
                    if (l % 5 == 0 && l > 0) {
                        msg += '<br/>';
                    }
                }
            }
            else {
                msg += "<i>Empty</i>";
            }
            if (getIcon('WoodCrate') == undefined) {
                console.log("Icon Undefined:"+entity_positions[e].class);
            }
            var marker = L.marker(XYToLatLng([entity_positions[e].x,entity_positions[e].y]), {icon: getIcon('WoodCrate'), riseOnHover: true}).bindPopup(msg).on('click', function(e) {
                entityClick(e);
            });
            marker.miscreatedData = entity_positions[e];
            icon_entity_position.push(marker);
        }
        else if (entity_positions[e].class == "UFOCrash") {
            if (getIcon('marker_icon_red') == undefined) {
                console.log("Icon Undefined:"+entity_positions[e].class);
            }
            var marker = L.marker(XYToLatLng([entity_positions[e].x,entity_positions[e].y]), {icon: getIcon('marker_icon_red'), riseOnHover: true}).bindPopup(msg).on('click', function(e) {
                entityClick(e);
            });
            marker.miscreatedData = entity_positions[e];
            icon_entity_position.push(marker);
        }
        else if (entity_positions[e].class == "UFOCrate") {
            msg += "<br/>";
            if (entity_positions[e].inventory.length > 0) {
                for (var i=0; i<entity_positions[e].inventory.length; i++) {
                    msg += entity_positions[e].inventory[i].item+"<br/>";
                }
            }
            else {
                msg += "<i>Empty</i>";
            }
            if (getIcon('marker_icon_red') == undefined) {
                console.log("Icon Undefined:"+entity_positions[e].class);
            }
            var marker = L.marker(XYToLatLng([entity_positions[e].x,entity_positions[e].y]), {icon: getIcon('marker_icon_red'), riseOnHover: true}).bindPopup(msg).on('click', function(e) {
                entityClick(e);
            });
            marker.miscreatedData = entity_positions[e];
            icon_entity_position.push(marker);
        }
        else {
            if (getIcon('marker_icon_red') == undefined) {
                console.log("Icon Undefined:"+entity_positions[e].class);
            }
            var marker = L.marker(XYToLatLng([entity_positions[e].x,entity_positions[e].y]), {icon: getIcon('marker_icon_red'), riseOnHover: true}).bindPopup(msg).on('click', function(e) {
                entityClick(e);
            });
            marker.miscreatedData = entity_positions[e];
            icon_entity_position.push(marker);
        }
    }

    //Players
    var html = "";
    for (var p=0; p<players.length; p++) {
        if (players[p].primary_vehicles_owned > 1) {
            html += '<div class="alert alert-danger-custom" role="alert">';
            html += '<strong>Potential Infraction</strong><br/>';
            html += 'Name: '+players[p].Name+'<br/>';
            html += 'Infraction: '+players[p].primary_vehicles_owned+' Primary Vehicles';
            html += '</div>';
        }
        if (players[p].secondary_vehicles_owned > 1) {
            html += '<div class="alert alert-danger-custom" role="alert">';
            html += '<strong>Potential Infraction</strong><br/>';
            html += 'Name: '+players[p].Name+'<br/>';
            html += 'Infraction: '+players[p].secondary_vehicles_owned+' Secondary Vehicles';
            html += '</div>';
        }
    }
    $('#infractions').html(html);
    updateMap();
}

function getData(callback) {
    if (token !== false) {
        checkToken(function(data) {
            if (!data.KeyValid) {
                pushLog("TokenInvalid: "+JSON.stringify(data));
                updateToken(function(data) {
                    if (data.Status) {
                        pushLog("Token Updated.");
                        token = data.UpdatedKey;
                        continueDataFetch(function(result) {
                            callback(result);
                        });
                    }
                });
            }
            else {
                pushLog("TokenValid");
                continueDataFetch(function(result) {
                    callback(result);
                });
            }
        });
    }
    else {
        callback(false);
    }
}

function continueDataFetch(callback) {
    getRecentPlayers(function(data) {
        if (data.Status) {
            players = data.Players;
            for (var p=0; p<players.length; p++) {
                players[p].idx = p;
                players[p].primary_vehicles_owned = 0;
                players[p].secondary_vehicles_owned = 0;
            }
        }
        pushLog("getPlayerPosition");
        getPlayerPosition(function(data) {
            if (data.Status) {
                if (data.Data) {
                    player_positions = data.Data.players;
                }
            }
            pushLog("getEntityPositions");
            getEntityPositions(function(data) {
                if (data.Status) {
                    entity_positions = data.Data.entities;
                    callback(true);
                }
            });
        });
    });

}

function checkToken(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/token.php?param=verify&token='+token, function(data) {
        callback(data);
    });
}

function updateToken(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/token.php?param=update&token='+token, function(data) {
        callback(data);
    });
}

function getPlayerPosition(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/realtime.php?param=players&token='+token, function(data) {
        callback(data);
    });
}

function getRecentPlayers(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/players.php?param=getRecent&token='+token, function(data) {
        callback(data);
    });
}

function getEntityPositions(callback) {
    $.get('https://api.twinkiedreamland.com/api/v1.0/realtime.php?param=entities&token='+token, function(data) {
        callback(data);
    });
}

function drawMap() {
    playerPos = L.layerGroup(icon_player_position);
    entityPos = L.layerGroup(icon_entity_position);
    vehiclePos = L.layerGroup(icon_vehicle_position);
    basesPos = L.layerGroup(icon_bases_position);
    partsPos = L.layerGroup(icon_metal_parts_position);
    tentsPos = L.layerGroup(icon_tent_position);

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
        "Player Position": playerPos,
        "Entities Position": entityPos,
        "Vehicle Position": vehiclePos,
        "Base Position": basesPos,
        "Metal Parts Spawn": partsPos,
        "Tent Position": tentsPos
    };

    if (miscreatedmap) {
        miscreatedmap.remove();
        miscreatedmap = false;
    }

    miscreatedmap = L.map('miscreated-map', {
        crs: L.CRS.pr,
        layers: [mapimage, playerPos, entityPos, vehiclePos, basesPos]
    });

    miscreatedMapControl = L.control.layers(baseLayers, overlays, {hideSingleBase: true}).addTo(miscreatedmap);
    miscreatedmap.setView(XYToLatLng([mapSize/2,mapSize/2]), 2);

    miscreatedmap.on('click', function(e) {
        playerSelected = false;
        selectedPlayerData = false;
        selectedMarker = false;

        var popLocation= e.latlng;
        console.log('{ "x": '+popLocation.lat+ ', "y": '+popLocation.lng+" }");
    });

    console.log("Map Drawn.");
    pushLog("Map Drawn.");

}

function updateMap() {
    var disablePlayers = miscreatedmap.hasLayer(playerPos);
    var disableEntities = miscreatedmap.hasLayer(entityPos);
    var disableVehicles = miscreatedmap.hasLayer(vehiclePos);
    var disableBases = miscreatedmap.hasLayer(basesPos);
    var disableParts = miscreatedmap.hasLayer(partsPos);
    var disableTents = miscreatedmap.hasLayer(tentsPos);

    if (disablePlayers) {
        miscreatedmap.removeLayer(playerPos);
    }
    else if (playerSelected) {
        playerSelected = false;
        selectedPlayerData = false;
        selectedMarker = false;
    }
    if (disableEntities) {
        miscreatedmap.removeLayer(entityPos);
    }
    if (disableVehicles) {
        miscreatedmap.removeLayer(vehiclePos);
    }
    if (disableBases) {
        miscreatedmap.removeLayer(basesPos);
    }
    if (disableParts) {
        miscreatedmap.removeLayer(partsPos);
    }
    if (disableTents) {
        miscreatedmap.removeLayer(tentsPos);
    }
    miscreatedMapControl.remove();

    playerPos = L.layerGroup(icon_player_position);
    entityPos = L.layerGroup(icon_entity_position);
    vehiclePos = L.layerGroup(icon_vehicle_position);
    basesPos = L.layerGroup(icon_bases_position);
    partsPos = L.layerGroup(icon_metal_parts_position);
    tentsPos = L.layerGroup(icon_tent_position);

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
        "Player Position": playerPos,
        "Entities Position": entityPos,
        "Vehicle Position": vehiclePos,
        "Base Position": basesPos,
        "Metal Parts Spawn": partsPos,
        "Tent Position": tentsPos
    };

    miscreatedMapControl = L.control.layers(baseLayers, overlays, {hideSingleBase: true});

    if (disablePlayers) {
        playerPos.addTo(miscreatedmap);
    }
    if (disableEntities) {
        entityPos.addTo(miscreatedmap);
    }
    if (disableVehicles) {
        vehiclePos.addTo(miscreatedmap);
    }
    if (disableBases) {
        basesPos.addTo(miscreatedmap);
    }
    if (disableParts) {
        partsPos.addTo(miscreatedmap);
    }
    if (disableTents) {
        tentsPos.addTo(miscreatedmap);
    }
    miscreatedMapControl.addTo(miscreatedmap);

    if (playerSelected) {
        selectedMarker.openPopup();
        miscreatedmap.panTo(selectedMarker.getLatLng());
    }

    console.log("Map Redrawn.");
    pushLog("Map Redrawn.");
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

function playerClick(e) {
    var playerData = e.target.miscreatedData;

    if (playerSelected === false || selectedPlayerData.id != playerData.id) {
        console.log("Clicked");
        playerSelected = true;
        selectedPlayerData = playerData;
    }
    else if (selectedPlayerData.id == playerData.id) {
        console.log("Not Clicked");
        playerSelected = false;
        selectedPlayerData = false;
    }
}

function entityClick(e) {
    if (playerSelected) {
        playerSelected = false;
        selectedPlayerData = false;
        selectedMarker = false;
    }
}

function pushLog(message) {
    $('#maplog').append(message+"\n");
    $('#maplog').scrollTop($('#maplog')[0].scrollHeight);
}
