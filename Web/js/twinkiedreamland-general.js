$(function() {
    $(document).on('click','#joinGame',function(e) { e.preventDefault(); $('#joinModal').modal('show'); });
    $('#player-list-info').click(function(e) {
        e.preventDefault();
        $('#playerlist-modal').modal('show');
    });

    if (initializeMiscreatedMap !== undefined && initializeMiscreatedMap === true && token !== false) {
        initializeMap();
    }
});

function initializeMap() {
    var playerPlot = L.layerGroup(icon_player_plot);
    var playerPos = L.layerGroup(icon_player_position);
    var clanPlots = L.layerGroup(icon_clan_plot);
    var clanPos = L.layerGroup(icon_clan_position);
    var vehiclePos = L.layerGroup(icon_vehicle_position);

    //https://www.twinkiedreamland.com/maps/mapAgent.php?mapName=island&Z={z}&X={x}&Y={y}&wr=6&wg=66&wb=150
    var mapimage = L.tileLayer('https://api.twinkiedreamland.com/map/mapAgent.php?mapName=island&Z={z}&X={x}&Y={y}&wr=0&wg=0&wb=0', {
        maxZoom: 7,
        minZoom: 1,
        zoomControl: true,
        zoomSnap: 1,
        noWrap: true,
        attribution: '<a href="https://www.twinkiedreamland.com">Twinkie Dreamland</a>',
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
    };

    if (miscreatedmap) {
        miscreatedmap.remove();
        miscreatedmap = false;
    }

    miscreatedmap = L.map('miscreated-map', {
        crs: L.CRS.pr,
        layers: [mapimage, playerPos]
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
