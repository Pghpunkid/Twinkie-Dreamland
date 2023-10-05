/*var testCoords = [
    {
        "lng":{"dir":"N","deg":48,"min":37,"sec":17.1},
        "lat":{"dir":"W","deg":122,"min":59,"sec":6.6},
        "desc":"Clyde Hill - Near Trader",
        "correction":true
    },
    {
        "lng":{"dir":"N","deg":48,"min":37,"sec":36.5},
        "lat":{"dir":"W","deg":122,"min":59,"sec":40.8},
        "desc":"Hayward Valley - Near Clyde Hill Road",
        "correction":true
    },
    {
        "lng":{"dir":"N","deg":48,"min":37,"sec":22.9},
        "lat":{"dir":"W","deg":123,"min":0,"sec":11.1},
        "desc":"Cedar Park Gas Station",
        "correction":true
    },
    {
        "lng":{"dir":"N","deg":48,"min":36,"sec":58.9},
        "lat":{"dir":"W","deg":122,"min":59,"sec":33.4},
        "desc":"Highway Intersection",
        "correction":true
    },

    {
        "lng":{"dir":"N","deg":48,"min":39,"sec":1.44},
        "lat":{"dir":"W","deg":123,"min":3,"sec":3.6},
        "desc":"Top Left",
        "correction":false
    },
    {
        "lng":{"dir":"N","deg":48,"min":39,"sec":1.44},
        "lat":{"dir":"W","deg":122,"min":56,"sec":33},
        "desc":"Top Right",
        "correction":false
    },
    {
        "lng":{"dir":"N","deg":48,"min":34,"sec":42.24},
        "lat":{"dir":"W","deg":123,"min":3,"sec":3.6},
        "desc":"Bottom Left",
        "correction":false
    },
    {
        "lng":{"dir":"N","deg":48,"min":34,"sec":42.24},
        "lat":{"dir":"W","deg":122,"min":56,"sec":33},
        "desc":"Bottom Right",
        "correction":false
    },
    {
        "lng":{"dir":"N","deg":48,"min":36,"sec":51.8},
        "lat":{"dir":"W","deg":122,"min":59,"sec":48.3},
        "desc":"Center",
        "correction":false
    },
    {
        "lng":{"dir":"N","deg":48,"min":37,"sec":56.6},
        "lat":{"dir":"W","deg":123,"min":1,"sec":25.9},
        "desc":"Left Top Quarter",
        "correction":false
    },
    {
        "lng":{"dir":"N","deg":48,"min":36,"sec":51.8},
        "lat":{"dir":"W","deg":123,"min":1,"sec":25.9},
        "desc":"Left Middle Quarter",
        "correction":false
    }
];*/

$(function() {

    if (initializeMiscreatedMap !== undefined && initializeMiscreatedMap === true) {

        initializeMap();
        /*for (var co=0; co<testCoords.length; co++) {
            var lat = ConvertDMSToDD(testCoords[co].lat.deg, testCoords[co].lat.min, testCoords[co].lat.sec, testCoords[co].lat.dir);
            var lng = parseFloat(ConvertDMSToDD(testCoords[co].lng.deg, testCoords[co].lng.min, testCoords[co].lng.sec, testCoords[co].lng.dir));

            var msg = "<strong>Player Position</strong><br/>";
            msg += msg+testCoords[co].lng.dir+" "+testCoords[co].lng.deg+"&deg; "+testCoords[co].lng.min+"' "+testCoords[co].lng.sec+'"<br/>';
            msg += msg+testCoords[co].lat.dir+" "+testCoords[co].lat.deg+"&deg; "+testCoords[co].lat.min+"' "+testCoords[co].lat.sec;

            var c = GPSToLatLng([lat,lng], testCoords[co].correction);
            var y = parseInt(c[0]);
            var x = parseInt(c[1]);

            var marker = L.marker([y, x], {icon: getIcon('marker_icon'), riseOnHover: true}).bindPopup(msg);
            icon_player_position.push(marker);
            console.log("Lat:"+lat+" Lon:"+lng+" X:"+x+" Y:"+y);
        }

        initializeMap();*/
        $('.leaflet-control-layers').hide();
    }

    $('#survivotron-help').click(function(e) {
        e.preventDefault();
    });

    $('#locate').click(function(e) {
        e.preventDefault();
        console.log("Locating..");
        icon_player_position = new Array();

        var n_deg = parseInt($('#n-deg').val());
        var n_minutes = parseInt($('#n-minutes').val());
        var n_seconds = parseFloat($('#n-seconds').val());

        var w_deg = parseInt($('#w-deg').val());
        var w_minutes = parseInt($('#w-minutes').val());
        var w_seconds = parseFloat($('#w-seconds').val());

        var lat = ConvertDMSToDD(w_deg, w_minutes, w_seconds, 'W');
        var lng = parseFloat(ConvertDMSToDD(n_deg, n_minutes, n_seconds, 'N'));

        var msg = "<strong>Player Position</strong><br/>";
        msg += msg+"N "+n_deg+"&deg; "+n_minutes+"' "+n_seconds+'"<br/>';
        msg += msg+"W "+w_deg+"&deg; "+w_minutes+"' "+w_seconds+'"';

        console.log("Lat:"+lat+" Lon:"+lng+" Msg:"+msg);
        var c = GPSToLatLng([lat,lng], true);
        var y = parseInt(c[0]);
        var x = parseInt(c[1]);

        console.log("X:"+x+" Y:"+y);

        var marker = L.marker([y, x], {icon: getIcon('marker_icon'), riseOnHover: true}).bindPopup(msg);
        icon_player_position.push(marker);

        initializeMap();
        miscreatedmap.panTo([y, x]);
    });
});

function ConvertDMSToDD(degrees, minutes, seconds, direction) {
    var dd = degrees + (minutes/60) + (seconds/(60*60));

    if (direction == "S" || direction == "W") {
        dd = dd * -1;
    }
    // Don't do anything for N or E
    return dd;
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

function GPSToLatLng(latlng, correction) {
    var lat = parseFloat(latlng[0]);
    var lng = parseFloat(latlng[1]);

    /*Public Const _MAP_LON_RIGHT_DD As Double = -122.9425
    Public Const _MAP_LON_LEFT_DD As Double = -123.051
    Public Const _MAP_LAT_TOP_DD As Double = 48.6504
    Public Const _MAP_LAT_BOTTOM_DD As Double = 48.5784*/

    var minLat = -123.051;  // Left
    var maxLat = -122.9425; // Right
    var minLng = 48.5784;   // Bottom
    var maxLng = 48.6504;   // Top

    var latScale = (lat - minLat) / (maxLat - minLat);
    var x = latScale * 2048;
    //console.log("lat:"+lat+" latSub:"+(lat - minLat)+" latScale:"+latScale+" x:"+x);

    var lngScale = (lng - minLng) / (maxLng - minLng);
    var y = (lngScale * 2048)-2048;
    //console.log("lng:"+lng+" lngSub:"+(lng - minLng)+" lngScale:"+lngScale+" y:"+y);

    if (correction === false) {
        x = x + 40; // 48
        y = y - 40; // 48

        var scaleFactor = 0.964; //0.955
        x = x * scaleFactor;
        y = y * scaleFactor;
    }
    else {
        x = x + 50; // 48
        y = y - 100; // 48

        var scaleFactor = 0.9321; //0.955
        x = x * scaleFactor;
        y = y * scaleFactor;
    }

    var coords = new Array(y,x);
    return coords;
}
