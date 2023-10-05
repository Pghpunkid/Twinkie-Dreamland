
//Map Information
var factorx = 0.125;
var factory = 0.125;
var mapSize = 8192;
var miscreatedmap = false;
var smoothing = 0;

var icon_player_plot = new Array();
var icon_player_position = new Array();
var icon_clan_plot = new Array();
var icon_clan_position = new Array();
var icon_vehicle_position = new Array();
var icon_entity_position = new Array();

const MAP_PROJECTION_SIZE = 8192;
const MAP_PROJECTION_ZOOM = 5;

var icons = new Array();

//Icons
for (var i=0; i<itemList.length; i++) {
    icons[itemList[i].name] = L.icon({
            iconUrl: itemList[i].image,
            iconSize: [48, 48],
            iconAnchor: [25, 43],
            popupAnchor: [0, -26]
    });
}

icons['sedan_police'] = icons['sedan_base'];
icons['sedan_taxi'] = icons['sedan_base'];
icons['sedan_taxi_engoa'] = icons['sedan_base'];
icons['sedan_taxi_fullout'] = icons['sedan_base'];
icons['sedan_taxi_blix'] = icons['sedan_base'];
icons['bicycle'] = icons['quadbike'];
icons['dirtbike'] = icons['quadbike'];
icons['plotsign'] = icons['plotsignpacked'];
icons['campingtentblue'] = icons['packedcampingtentblue'];
icons['campingtentbrown'] = icons['packedcampingtentbrown'];
icons['campingtentgreen'] = icons['packedcampingtentgreen'];
icons['campingtentorange'] = icons['packedcampingtentorange'];
icons['campingtentpurple'] = icons['packedcampingtentpurple'];
icons['campingtentred'] = icons['packedcampingtentred'];
icons['campingtentyellow'] = icons['packedcampingtentyellow'];
icons['campingtent'] = icons['packedcampingtent'];
icons['easycamptentblue'] = icons['packedeasycamptentblue'];
icons['easycamptentbrown'] = icons['packedeasycamptentbrown'];
icons['easycamptentgreen'] = icons['packedeasycamptentgreen'];
icons['easycamptentorange'] = icons['packedeasycamptentorange'];
icons['easycamptentpurple'] = icons['packedeasycamptentpurple'];
icons['easycamptentred'] = icons['packedeasycamptentred'];
icons['easycamptentyellow'] = icons['packedeasycamptentyellow'];
icons['puptentblue'] = icons['packedpuptentblue'];
icons['puptentbrown'] = icons['packedpuptentbrown'];
icons['puptentgreen'] = icons['packedpuptentgreen'];
icons['puptentred'] = icons['packedpuptentred'];
icons['puptenttan'] = icons['packedpuptenttan'];
icons['trekkingtentblue'] = icons['packedtrekkingtentblue'];
icons['trekkingtentbrown'] = icons['packedtrekkingtentbrown'];
icons['trekkingtentgreen'] = icons['packedtrekkingtentgreen'];
icons['trekkingtentorange'] = icons['packedtrekkingtentorange'];
icons['trekkingtentpurple'] = icons['packedtrekkingtentpurple'];
icons['trekkingtentred'] = icons['packedtrekkingtentred'];
icons['trekkingtentyellow'] = icons['packedtrekkingtentyellow'];
icons['trekkingtent'] = icons['packedtrekkingtent'];
icons['twopersontentblue'] = icons['packedtwopersontentblue'];
icons['twopersontentbrown'] = icons['packedtwopersontentbrown'];
icons['twopersontentgreen'] = icons['packedtwopersontentgreen'];
icons['twopersontentorange'] = icons['packedtwopersontentorange'];
icons['twopersontentpurple'] = icons['packedtwopersontentpurple'];
icons['twopersontentred'] = icons['packedtwopersontentred'];
icons['twopersontentyellow'] = icons['packedtwopersontentyellow'];

function getIcon(iconName) {
    iconName = iconName.toLowerCase();
    var char = iconName.substring(0, 1);
    if (isNumeric(char)) {
        iconName = "a"+iconName;
    }

    if (icons[iconName] == undefined) {
        console.log("Icon Undefined: "+iconName);
    }
    //console.log("getIcon: "+iconName+" - "+char);
    return icons[iconName];
}

function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

//Map Stuff
L.CRS.pr = L.extend({}, L.CRS.Simple, {
    projection: L.Projection.LonLat,
    transformation: new L.Transformation(factorx, 0, -factory, 0),
    // Changing the transformation is the key part, everything else is the same.
    // By specifying a factor, you specify what distance in meters one pixel occupies (as it still is CRS.Simple in all other regards).
    // In this case, I have a tile layer with 256px pieces, so Leaflet thinks it's only 256 meters wide.
    // I know the map is supposed to be 2048x2048 meters, so I specify a factor of 0.125 to multiply in both directions.
    // In the actual project, I compute all that from the gdal2tiles tilemapresources.xml,
    // which gives the necessary information about tilesizes, total bounds and units-per-pixel at different levels.


    // Scale, zoom and distance are entirely unchanged from CRS.Simple
    scale: function(zoom) {
    return Math.pow(2, zoom);
    },

    zoom: function(scale) {
    return Math.log(scale) / Math.LN2;
    },

    distance: function(latlng1, latlng2) {
    var dx = latlng2.lng - latlng1.lng,
      dy = latlng2.lat - latlng1.lat;

    return Math.sqrt(dx * dx + dy * dy);
    },
    infinite: true
});
