var misrcon = require('node-misrcon');
var rconconfig = require('./config');
var fs = require('fs');
var util = require('util');
var announcements = require('./announcements');
var periodicmsg = require('./periodicmsg');
var announcementsIdx = 0;
var periodicMsgIdx = 0;
var runtime = 0;

server = new misrcon.NodeMisrcon({
    ip: rconconfig.theIP,
    port: rconconfig.thePORT,
    password: rconconfig.thePASSWORD
});

logMessage("Starting up..");
setTimeout(function() {
    main();
}, 5000);

function main() {
    logMessage("Announcer Started.");
    tickTock();
}

function tickTock() {
    if (runtime % periodicmsg.timer == 0) {
        if (periodicMsgIdx >= periodicmsg.list.length) {
            periodicMsgIdx = 0;
        }
        logMessage("Announcement: "+util.inspect(periodicmsg.list[periodicMsgIdx]));
        sendMessage(periodicmsg.list[periodicMsgIdx], function(data2) {
            logMessage("sendMessage: "+util.inspect(data2));
        });
        periodicMsgIdx++;
    }

    if (runtime % announcements.timer == 5) {
        if (announcementsIdx >= announcements.list.length) {
            announcementsIdx = 0;
        }
        logMessage("Announcement: "+util.inspect(announcements.list[announcementsIdx]));
        sendSay(announcements.list[announcementsIdx], function(data2) {
            logMessage("sendMessage: "+util.inspect(data2));
        });
        announcementsIdx++;
    }

    runtime++;
    setTimeout(function() {
        tickTock();
    }, 1000);
}

function sendMessage(message, callback) {
    var cmd = 'sv_chat '+message;
    serverCMD(cmd, function(data) {
        callback(data);
    });
}

function sendSay(message, callback) {
    var cmd = 'sv_say '+message;
    serverCMD(cmd, function(data) {
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
    console.log("[ANC]["+generateDate()+"] "+text);
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

    var writeStr = "[ANC]["+datetimeStr+"] "+text;

    fs.exists("server.log", function(exists) {
        if (!exists) {
            fs.writeFile("server.log", writeStr+"\n", function(err) {
                if(err) {
                    console.log("LOG FILE ERROR:"+err);
                }
            });
        }
        else {
            fs.appendFile("server.log", writeStr+"\n", function(err) {
                if(err) {
                    console.log("LOG FILE ERROR:"+err);
                }
            });
        }
    });
}
