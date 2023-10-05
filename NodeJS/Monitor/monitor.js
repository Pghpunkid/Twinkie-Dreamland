var misrcon = require('node-misrcon');
var rconconfig = require('./config');
var dbconfig = require('./dbconfig');
var io = require('socket.io');
var mysql = require('mysql');
var fs = require('fs');
var util = require('util');
var https = require('https');
var express = require('express');

//General Settings
var debug = false;

//MySQL
var db_config = {
    host     : dbconfig.host,
    user     : dbconfig.user,
    password : dbconfig.password,
    database : dbconfig.database,
};
var sqlConnected = false;
var connection;
var firstConnection = true;
var sqlQueue = new Array();

//SocketIO
var clients = [];
var httpsApp = express();

var httpsOptions = {
    key: fs.readFileSync('/etc/letsencrypt/live/api.twinkiedreamland.com/privkey.pem'),
    cert: fs.readFileSync('/etc/letsencrypt/live/api.twinkiedreamland.com/fullchain.pem')
};
var httpsPort = 9000;
var httpsServer = https.createServer(httpsOptions, httpsApp);
var io_https = false;

var local_player_list = new Array();

//Weather information
var weatherInfo = {
	"ClearSky": {
		"english": "Clear",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "ClearSky_Day.svg",
		"night_icon": "ClearSky_Night.svg",
        "audio":""
	},
	"LightRain": {
		"english": "Rain",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "LightRain.svg",
		"night_icon": "LightRain.svg",
        "audio":""
	},
	"HeavyRainThunder": {
		"english": "T-Storms",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "HeavyRainThunder.svg",
		"night_icon": "HeavyRainThunder.svg",
        "audio":""
	},
	"HeavyStorm": {
	/*	"english": "T-Storms",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "HeavyStorm.svg",
		"night_icon": "HeavyStorm.svg",
        "audio":""*/
        "english": "Windy",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "ClearSkyWindy.svg",
		"night_icon": "ClearSkyWindy.svg",
        "audio":""
	},
	"TornadoStorm": {
		"english": "Tornado",
		"emergencyWeather": true,
        "emergencyStatement": "The Orca Island Weather Service advises everyone seek shelter due to Tornadic activity.",
		"day_icon": "TornadoStorm.svg",
		"night_icon": "TornadoStorm.svg",
        "audio": "OIWS-Tornado-FX.mp3"
	},
	"TornadoStorm_Tornado": {
		"english": "Tornado",
		"emergencyWeather": true,
        "emergencyStatement": "The Orca Island Weather Service advises everyone seek shelter due to Tornadic activity.",
		"day_icon": "TornadoStorm.svg",
		"night_icon": "TornadoStorm.svg",
        "audio": "OIWS-Tornado-FX.mp3"
	},
	"TornadoRainThunder": {
		"english": "Tornado",
		"emergencyWeather": true,
        "emergencyStatement": "The Orca Island Weather Service advises everyone seek shelter due to Tornadic activity.",
		"day_icon": "TornadoStorm.svg",
		"night_icon": "TornadoStorm.svg",
        "audio": "OIWS-Tornado-FX.mp3"
	},
	"TornadoRainThunder_Tornado": {
		"english": "Tornado",
		"emergencyWeather": true,
        "emergencyStatement": "The Orca Island Weather Service advises everyone seek shelter due to Tornadic activity.",
		"day_icon": "TornadoStorm.svg",
		"night_icon": "TornadoStorm.svg",
        "audio": "OIWS-Tornado-FX.mp3"
	},
	"LightFog": {
		"english": "Fog",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "Fog.svg",
		"night_icon": "Fog.svg",
        "audio":""
	},
	"MediumFog": {
		"english": "Fog",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "Fog.svg",
		"night_icon": "Fog.svg",
        "audio":""
	},
	"HeavyFog": {
		"english": "Fog",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "Fog.svg",
		"night_icon": "Fog.svg",
        "audio":""
	},
	"Rainbow": {
		"english": "Clear",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "ClearSky_Day.svg",
		"night_icon": "ClearSky_Night.svg",
        "audio":""
	},
	"RainbowHalf": {
		"english": "Clear",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "ClearSky_Day.svg",
		"night_icon": "ClearSky_Night.svg",
        "audio":""
	},
	"TheMist": {
		"english": "The Mist",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "TheMist.svg",
		"night_icon": "TheMist.svg"
	},
	"RadStorm": {
		"english": "Radiation Storm",
		"emergencyWeather": true,
        "emergencyStatement": "The Orca Island Weather Service advises everyone seek shelter/don HAZMAT equipment due to Radiation Storm.",
		"day_icon": "RadStorm.svg",
		"night_icon": "RadStorm.svg",
        "audio": "OIWS-RadStorm-FX.mp3"
	},
	"RadStorm_Peak": {
		"english": "Radiation Storm",
		"emergencyWeather": true,
        "emergencyStatement": "The Orca Island Weather Service advises everyone seek shelter/don HAZMAT equipment due to Radiation Storm.",
		"day_icon": "RadStorm.svg",
		"night_icon": "RadStorm.svg",
        "audio": "OIWS-RadStorm-FX.mp3"
	},
	"RadStorm_Outro": {
		"english": "Radiation Storm",
		"emergencyWeather": true,
        "emergencyStatement": "The Orca Island Weather Service issues statement: Radiation Storm should be over soon.",
		"day_icon": "RadStorm.svg",
		"night_icon": "RadStorm.svg",
        "audio": "OIWS-RadStorm-FX.mp3"
	},
	"NuclearFlashFreeze": {
		"english": "Nuclear Freeze",
		"emergencyWeather": true,
        "emergencyStatement": "The Orca Island Weather Service advises everyone seek shelter due to Nuclear Freeze.",
		"day_icon": "NuclearFlashFreeze.svg",
		"night_icon": "NuclearFlashFreeze.svg",
        "audio": "OIWS-NuclearFreeze-FX.mp3"
	},
	"NuclearFlashFreeze_Peak": {
		"english": "Nuclear Freeze",
		"emergencyWeather": true,
        "emergencyStatement": "The Orca Island Weather Service advises everyone seek shelter due to Nuclear Freeze.",
		"day_icon": "NuclearFlashFreeze.svg",
		"night_icon": "NuclearFlashFreeze.svg",
        "audio": "OIWS-NuclearFreeze-FX.mp3"
	},
	"NuclearFlashFreeze_Outro": {
		"english": "Nuclear Freeze",
		"emergencyWeather": true,
        "emergencyStatement": "The Orca Island Weather Service: Nuclear Freeze should be over soon.",
		"day_icon": "NuclearFlashFreeze.svg",
		"night_icon": "NuclearFlashFreeze.svg",
        "audio": "OIWS-NuclearFreeze-FX.mp3"
	},
	"Snow": {
		"english": "Snow",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "Snow.svg",
		"night_icon": "Snow.svg",
        "audio":""
	},
	"Snow_Outro": {
		"english": "Snow",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "Snow.svg",
		"night_icon": "Snow.svg",
        "audio":""
	},
	"ClearSkyWindySnow_Outro": {
		"english": "Snow",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "ClearSkyWindy.svg",
		"night_icon": "ClearSkyWindy.svg",
        "audio":""
	},
	"ClearSkyWindy": {
		"english": "Windy",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "ClearSkyWindy.svg",
		"night_icon": "ClearSkyWindy.svg",
        "audio":""
	},
	"ClearSkyStormy": {
		"english": "Rain",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "HeavyRain.svg",
		"night_icon": "HeavyRain.svg",
        "audio":""
	},
	"StormyDistantThunder": {
		"english": "T-Storms",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "Cloudy.svg",
		"night_icon": "Cloudy.svg",
        "audio":""
	},
	"MediumRain": {
		"english": "Rain",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "HeavyRain.svg",
		"night_icon": "HeavyRain.svg",
        "audio":""
	},
	"HeavyRain": {
		"english": "Rain",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "HeavyRainThunder.svg",
		"night_icon": "HeavyRainThunder.svg",
        "audio":""
	},
	"AcidRain": {
		"english": "Acid Rain",
		"emergencyWeather": true,
        "emergencyStatement": "The Orca Island Weather Service advises everyone seek shelter due to Acid Rain.",
		"day_icon": "AcidRain.svg",
		"night_icon": "AcidRain.svg",
        "audio": "OIWS-AcidRain-FX.mp3"
	},
	"AcidRain_Peak": {
		"english": "Acid Rain",
		"emergencyWeather": true,
        "emergencyStatement": "The Orca Island Weather Service advises everyone seek shelter due to Acid Rain.",
		"day_icon": "AcidRain.svg",
		"night_icon": "AcidRain.svg",
        "audio": "OIWS-AcidRain-FX.mp3"
	},
	"Blizzard": {
		"english": "Blizzard",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "Blizzard.svg",
		"night_icon": "Blizzard.svg",
        "audio":""
	},
	"BlizzardThunder": {
		"english": "Blizzard",
		"emergencyWeather": false,
        "emergencyStatement": "",
		"day_icon": "Blizzard.svg",
		"night_icon": "Blizzard.svg",
        "audio":""
	}
};

//Initialize MySQL Connection.
handleDisconnect();

var server =  false;

//Status
var runtime = 0;
var statusInterval = 30;
var status = {};

//Start once MySQL is connected.
function main() {
    startSocketIO();
    tickTock();
}

//Main loop
function tickTock() {
	setTimeout(function() {
        tickTock();
    }, 1000);

    server = new misrcon.NodeMisrcon({
        ip: rconconfig.theIP,
        port: rconconfig.thePORT,
        password: rconconfig.thePASSWORD
    });

	if (runtime % statusInterval == 0) {
		getStatus(function(data) {
            //logMessage("getStatus:"+util.inspect(data));
            if (data !== false) {
                status = processStatus(data);

                delete status.gamerules;
                delete status.level;
                delete status.version;
                delete status.ip;
                delete status.round_time_remaining;

                status.weatherReport = weatherInfo[status.weather];

                if (debug) {
                    logMessage("Server Data:"+util.inspect(status));
                }

                //Update DB
                if (status.player_list.length > 0) {
                    for (var p=0; p<status.player_list.length; p++) {
                        var player = status.player_list[p];
                        logMessage("player:"+util.inspect(player));

                        var query = "UPDATE DB_SteamNames SET LastSeen=SYSDATE(), LastIP="+mysql_escape(player.ip)+" WHERE SteamID="+mysql_escape(player.steam)+";";
                        mysql_query(query, function(rows,fields, err) {
                            if (err) {
                                logMessage("Problem updating "+player.name+": "+err+" Query:"+query);
                                logError("Problem updating "+player.name+": "+err+" Query:"+query);
                            }
                            else {
                                if (debug) {
                                    logMessage("Hello "+player.name+".");
                                }
                            }
                        });
                    }
                }

                var d = new Date();
                if (d.getMinutes() % 5 == 0) {
                    if (debug) {
                        logMessage("Player Snapshot:"+util.inspect(status.player_list));
                    }

                    var playerCount = status.current_players;
                    var maxPlayerCount = status.max_players;
                    if (playerCount != undefined && maxPlayerCount != undefined) {
                        var query = "INSERT INTO DB_PlayerSnapshot SET PlayerCount="+playerCount+", MaxPlayerCount="+maxPlayerCount+", PlayerList="+mysql_escape(JSON.stringify(status.player_list))+", UpdateDateTime=SYSDATE();";
                        mysql_query(query,function(rows,fields, err) {
                            if (err) {
                                logMessage("Problem logging player snapshot: "+err+" Query:"+query);
                                logError("Problem logging player snapshot: "+err+" Query:"+query);
                            }
                        });
                    }
                }

                //Remove sensitive/unused data.
                for (var p=0; p<status.player_list.length; p++) {
                    delete status.player_list[p].entID;
                    delete status.player_list[p].ip;
                    delete status.player_list[p].profile;
                }

                //Trying to send Steam names with list so theres no "Survivor" names.
                var player_list=[].concat(status.player_list);
                updatePlayerList(player_list);
                delete status.player_list;

                io_https.emit('status', status);
                sendCurrentPlayerList();
            }
            else {
                status = {};
            }
        });
	}
	runtime++;
}

//SocketIO Functions
function startSocketIO() {
    io_https = io.listen(httpsServer);
    httpsServer.listen(httpsPort);

    logMessage("SocketIO HTTPS Listening on "+httpsPort+".");

    //HTTPS Server
    io_https.on('connection', function(socket) {
        //logMessage('HTTPS Connection');
        onSocketConnect(socket);

        socket.on('disconnect',function(data) {
            //logMessage('HTTPS Disconnection');
            onSocketDisconnect(socket);
        });

        socket.on('identify',function(data) {
            //logMessage('HTTPS Indentified: '+data.playerName);
            onSocketIdentify(socket, data);
        });
    });
}

function onSocketConnect(socket) {
    var client = {};
    client.socketId = socket.id;
    addClient(client);

    var clientId = getClientIdBySocketId(socket.id);
    socket.emit("status",status);
    sendCurrentPlayerListForSocket(socket)
    setClientParam(clientId, 'startTime', timestamp());
}

function onSocketDisconnect(socket) {
    var clientId = getClientIdBySocketId(socket.id);

    var endTime = timestamp();
    var startTime = getClientParam(clientId, 'startTime');
    var loggedIn = getClientParam(clientId, 'loggedIn');
    var accountId = getClientParam(clientId, 'accountId');
    var pageName = getClientParam(clientId, 'pageName');
    var playerName = getClientParam(clientId, 'playerName');
    var ipAddress = socket.request.connection.remoteAddress;

    var query = "INSERT INTO DB_WebsiteVisits SET "+
                    "VisitorIP="+mysql_escape(ipAddress)+", "+
                    "PageName="+mysql_escape(pageName)+", "+
                    "VisitTime="+(endTime-startTime)+", "+
                    "LoggedIn="+mysql_escape((loggedIn=="true"?"Y":"N"))+", "+
                    "AccountID="+mysql_escape(accountId)+", "+
                    "PlayerName="+mysql_escape(playerName)+", "+
                    "VisitDateTime=SYSDATE();";
    mysql_query(query,function(rows,fields, err) {
        if (err) {
            logMessage("Problem logging website visit: "+err+" Query:"+query);
            logError("Problem logging website visit: "+err+" Query:"+query);
        }
    });

    removeClientBySocketId(socket.id);
}

function onSocketIdentify(socket, data) {
    var clientId = getClientIdBySocketId(socket.id);

    if (clientId !== false) {
        setClientParam(clientId, 'playerName', data.playerName);
        setClientParam(clientId, 'loggedIn', data.loggedIn);
        setClientParam(clientId, 'accountId', data.accountId);
        setClientParam(clientId, 'pageName', data.pageName);
    }
}

function addClient(data) {
    clients.push(data);
}

function setClientParam(clientId, key, value) {
    clients[clientId][key] = value;
}

function getClientParam(clientId, key) {
    return clients[clientId][key];
}

function getClientBySocketId(socketId) {
    for (var i=0; i<clients.length; i++) {
        if (clients[i].socketId == socketId) {
            return clients[i];
        }
    }
    return false;
}

function getClientIdBySocketId(socketId) {
    for (var i=0; i<clients.length; i++) {
        if (clients[i].socketId == socketId) {
            return i;
        }
    }
    return false;
}

function removeClientBySocketId(socketId) {
    for (var i=0; i<clients.length; i++) {
        if (clients[i].socketId == socketId) {
            clients.splice(i,1);
            return true;
        }
    }
    return false;
}

// Get Status of Miscreated Server.
function getStatus(callback) {
    serverCMD('status', function(data) {
        callback(data);
    });
}

function serverCMD(cmd, callback) {
    var commandResponse = server.send(cmd);
	commandResponse.then(function(response) {
        callback(response);
        return;
	}).catch( function(err) {
        callback(false);
        return;
    });
}

//Parse Response
function processStatus(data) {
    var lines = data.split("\n");
    var settings = {};
    var players = [];
    settings.player_list = [];

    var settingsParse = true;
    var playersParse = false;


    for (var l=0; l<lines.length; l++) {
        if (lines[l] == "-----------------------------------------") {
            lines.splice(l,1);
        }
        if (lines[l] == "Server Status:") {
            lines.splice(l,1);
        }
        if (lines[l] == "") {
            lines.splice(l,1);
        }
        if (lines[l] == " -----------------------------------------") {
            //Toggle to Player list
            lines.splice(l,1);
            settingsParse = false;
            playersParse = true;
        }
        if (lines[l] == "Connection Status:") {
            lines.splice(l,1);
        }

        if (settingsParse) {
            if (lines[l] != "" && lines[l] != undefined) {
                var setting = lines[l].split(": ");
                var rule = setting[0].replace(/ /g, "_");
                var value = setting[1];

                if (rule == 'players') {
                    var player_count = value.split('/');
                    settings['current_players'] = player_count[0];
                    settings['max_players'] = player_count[1];
                }
                else {
                    settings[rule] = value;
                }
            }
        }
        else if (playersParse) {
            if (lines[l] != "" && lines[l] != undefined) {
                var player = lines[l].split("' ");

                var tmpPlayer = {};
                for (var p=0; p<player.length; p++) {
                    var playerItem = player[p];
                    playerItem = playerItem.replace(/'/g,"");
                    playerItem = playerItem.replace(/ /g,"");
                    playerItem = playerItem.split(":",3);

                    var key = playerItem[0];
                    key = key.replace(/ /g,"");

                    var value = playerItem[1];
                    if (playerItem[2] !== undefined) {
                        playerItem[1] += ":"+playerItem[2];
                    }
                    value = value.replace(/ /g,"");
                    tmpPlayer[key] = value;
                }
                players.push(tmpPlayer);
            }
        }
    }
    settings.player_list = players;
    return settings;
}


//*****************************************************************************
// General Functions
//*****************************************************************************

function timestamp() {
    return Math.floor(Date.now()/1000);
}

function generateDate() {
	var d = new Date();
	var dateString = ((d.getMonth()+1)<10?"0"+(d.getMonth()+1):(d.getMonth()+1)) + "/" + (d.getDate()<10?"0"+d.getDate():d.getDate()) + "/" + d.getFullYear() + " " + (d.getHours()<10?"0"+d.getHours():d.getHours()) + ":" + (d.getMinutes()<10?"0"+d.getMinutes():d.getMinutes()) + ":"  + (d.getSeconds()<10?"0"+d.getSeconds():d.getSeconds());
	return dateString;
}

function logMessage(text) {
    console.log("[MON]["+generateDate()+"] "+text);
    var date = new Date();
    var months = "JanFebMarAprMayJunJulAugSepOctNovDec";

    var month = months.substr((date.getMonth()*3),3);
    var dom = date.getDate();
    var year = date.getFullYear();
    var hour = date.getHours();
    var mins = date.getMinutes();
    var secs = date.getSeconds();

    var datetimeStr = "";

    //if (lastGPSTime == "") {
        datetimeStr = datetimeStr = month+" "+
        (dom < 10?"0"+dom:dom)+" "+
        year+" "+
        (hour < 10?"0"+hour:hour)+":"+
        (mins < 10?"0"+mins:mins)+":"+
        (secs < 10?"0"+secs:secs);
    //}
    //else {
    //    datetimeStr = lastGPSTime;
    //}

    var writeStr = "["+datetimeStr+"] "+text;

    fs.exists("server.log", function(exists) {
        if (!exists) {
            fs.writeFile("server.log", writeStr+"\n", function(err) {
                if(err) {
                    console.log("ERROR: LOG FILE:"+err);
                }
            });
        }
        else {
            fs.appendFile("server.log", writeStr+"\n", function(err) {
                if(err) {
                    console.log("ERROR: LOG FILE:"+err);
                }
            });
        }
    });
}

function logError(text) {
    console.log("["+generateDate()+"] "+text);
    var date = new Date();
    var months = "JanFebMarAprMayJunJulAugSepOctNovDec";

    var month = months.substr((date.getMonth()*3),3);
    var dom = date.getDate();
    var year = date.getFullYear();
    var hour = date.getHours();
    var mins = date.getMinutes();
    var secs = date.getSeconds();

    var datetimeStr = "";

    //if (lastGPSTime == "") {
        datetimeStr = datetimeStr = month+" "+
        (dom < 10?"0"+dom:dom)+" "+
        year+" "+
        (hour < 10?"0"+hour:hour)+":"+
        (mins < 10?"0"+mins:mins)+":"+
        (secs < 10?"0"+secs:secs);
    //}
    //else {
    //    datetimeStr = lastGPSTime;
    //}

    var writeStr = "[MON]["+datetimeStr+"] "+text;

    fs.exists("errors.log", function(exists) {
        if (!exists) {
            fs.writeFile("errors.log", writeStr+"\n", function(err) {
                if(err) {
                    console.log("ERROR: LOG FILE:"+err);
                }
            });
        }
        else {
            fs.appendFile("errors.log", writeStr+"\n", function(err) {
                if(err) {
                    console.log("ERROR: LOG FILE:"+err);
                }
            });
        }
    });
}

// MySQL
function processSQLQueue() {
	if (sqlQueue.length > 0) {
		if (sqlConnected == true) {
		    var query = sqlQueue[0].query;
			connection.query(query, function(err, rows, fields) {
				if (err) {
					logMessage('ERROR: MySQL Query: '+err);
                    logError('ERROR: MySQL Query: '+err);
					sqlQueue.splice(0,1);
				}
				else {
					//It was successful.
					sqlQueue.splice(0,1);
				}
				if (sqlQueue.length > 0) {
				    reindex_array_keys(sqlQueue);
					processSQLQueue();
				}
			});
		}
		else {
			logMessage("ERROR: MySQL NOT CONNECTED. Attempting Reconnection..");
            logError("ERROR: MySQL NOT CONNECTED. Attempting Reconnection..");
		  	handleDisconnect();
		}
	}
}

function mysql_escape(data) {
    return connection.escape(data);
}

function mysql_query(query, callback) {
    if (sqlConnected == true) {
        connection.query(query, function(err, rows, fields) {
            callback(rows,fields,err);
        });
    }
    else {
        callback(false,false,false);
    }
}

function handleDisconnect() {
	connection = mysql.createConnection(db_config);             			// Recreate the connection, since
	                                                           				// the old one cannot be reused.
	connection.connect(function(err) {                          			// The server is either down
		if(err) {                                                 			// or restarting (takes a while sometimes).
			logMessage('MySQL Cannot Reconnect:'+ err.code);
            logError('MySQL Cannot Reconnect:'+ err.code);
			setTimeout(handleDisconnect, 5000);                     		// We introduce a delay before attempting to reconnect,
			sqlConnected = false;
		}                                                         			// to avoid a hot loop, and to allow our node script to
		else {                                                    			// process asynchronous requests in the meantime.
			logMessage("MySQL Connected.");                                 // If you're also serving http, display a 503 error.
			sqlConnected = true;
			if (firstConnection == true) {
				//initialize Message
                main();
				firstConnection = false;
			}
		}
	});

	connection.on('error', function(err) {
		logMessage('ERROR:'+ err.code);		// Connection to the MySQL server is usually lost due to either server restart, or a
        logError('ERROR:'+ err.code);
		sqlConnected = false;												// connnection idle timeout (the wait_timeout server variable configures this)
		handleDisconnect();
	});
}

function isPlayerInLocalPlayerList(steamID) {
    for (var p=0; p<local_player_list.length; p++) {
        if (local_player_list[p].steam == steamID) {
            return true;
        }
    }
    return false;
}

function getLocalPlayerIdx(steamID) {
    for (var p=0; p<local_player_list.length; p++) {
        if (local_player_list[p].steam == steamID) {
            return p;
        }
    }
    return false;
}


function isPlayerInServerPlayerList(steamID, player_list) {
    for (var p=0; p<player_list.length; p++) {
        var player = player_list[p];
        if (player.steam == steamID) {
            return true;
        }
    }
    return false;
}

function addPlayerInLocalPlayerList(player, callback) {
    var query = "SELECT * FROM DB_SteamNames WHERE SteamID="+mysql_escape(player.steam)+";";
    player.joined = Math.floor(Date.now()/1000);
    mysql_query(query,function(rows, fields, err) {
        if (err) {
            logMessage("ERROR: Problem finding "+player.name+": "+err+" Query:"+query);
            logError("ERROR: Problem finding "+player.name+": "+err+" Query:"+query);
            player.steam_name = "undefined";
        }
        else {
            if (rows.length == 1) {
                logMessage("Player "+player.id+": "+player.name+" Steam Name Found. Populating..");
                player.steam_name = rows[0].Name;
            }
            else {
                logMessage("Player "+player.id+": "+player.name+" Steam Name Not Found. Populating undefined..");
                player.steam_name = "undefined";
            }
        }
        logMessage("Pushing Player "+player.id+": "+player.name+"..");
        local_player_list.push(JSON.parse(JSON.stringify(player)));
        logMessage(util.inspect(local_player_list));
        callback();
    });
}

function removePlayerFromLocalPlayerList(steamID, callback) {
    for (var p=0; p<local_player_list.length; p++) {
        logMessage(local_player_list[p].steam+" "+steamID);
        if (local_player_list[p].steam == steamID) {
            local_player_list.splice(p,1);
            callback();
        }
    }
}

function updatePlayerList(player_list) {
    //Checking for players in server not in our controlled list.
    for (var p=0; p<player_list.length; p++) {
        var player = player_list[p];
        if (!isPlayerInLocalPlayerList(player.steam) && player.state == 3) {
            logMessage("Adding Player "+player.id+": "+player.name+"..");
            addPlayerInLocalPlayerList(player, function() {
                logMessage("Added Player "+player.id+": "+player.name);
                sendCurrentPlayerList();
            });
        }
        else {
            var idx = getLocalPlayerIdx(player.steam);
            local_player_list[idx].ping = player.ping;
            logMessage("Updated Player "+player.id+": "+player.name+"..");
        }
    }

    //Checking for players in our controlled list but not in the server.
    for (var p=0; p<local_player_list.length; p++) {
        var player = local_player_list[p];
        if (!isPlayerInServerPlayerList(player.steam, player_list)) {
            logMessage("Removing Player "+player.id+": "+player.name+" ("+player.steam+")..");
            removePlayerFromLocalPlayerList(player.steam, function() {
                logMessage("Removed Player "+player.id+": "+player.name);
                sendCurrentPlayerList();
            });
        }
    }
}

function sendCurrentPlayerList() {
    var data = {};
    data.current_time = (Date.now()/1000);
    data.players = JSON.parse(JSON.stringify(local_player_list));
    io_https.emit('playerList', data);
}

function sendCurrentPlayerListForSocket(socket) {
    var data = {};
    data.current_time = Math.floor(Date.now()/1000);
    data.players = local_player_list;
    socket.emit('playerList', data);
}
