var http = require('http');
var fs = require('fs');

var webHost = '104.129.30.83';
var webPort = 64002;
var webPath = "../../API/api/v1.0/dynamic/";

function timeFetches() {
    setTimeout(function() {
        performFetches();
    }, 60000);
}

function performFetches() {
    //Entities
    fetch("entities", function(result, data) {
        if (result) {
            writeFile(webPath+'entities.json', data, function(result) {
                console.log(result);
                if (result) {
                    console.log("entities.json updated.");

                    //Players
                    fetch("players", function(result, data) {
                        if (result) {
                            writeFile(webPath+'players.json', data, function(result) {
                                console.log(result);
                                if (result) {
                                    console.log("players.json updated.");
                                }
                            });
                        }
                    });
                    //End Players
                }
            });
        }
    });
    //End Entities

    timeFetches();
}
performFetches();

function fetch(endpoint, callback) {
    var options = {
        host: webHost,
        port: webPort,
        path: '/'+endpoint
    }

    var request = http.request(options, function (res) {
        var data = '';
        res.on('data', function (chunk) {
            data += chunk;
        });

        res.on('end', function () {
            callback(true, data);
        });
    });

    request.on('error', function (e) {
        callback(false, false);
    });

    request.end();
}

function writeFile(path, data, callback) {
    fs.writeFile(path, data, function (err) {
        if (err) {
            console.log(err);
            callback(false);
        }
        else {
            callback(true);
        }
    });
}
