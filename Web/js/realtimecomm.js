var address = 'https://twinkiedreamland.com:9000';
var devAddress = 'https://dev.twinkiedreamland.com:9100';

if (isDev) {
    address = devAddress;
}

var weather = false;
var status = false;
var time = false;

var worldHour = false;
var worldMinute = false;
var worldRate = 11.5;
var updateTimeTimer = false;

var previousEmergency = false;

var emergencySiren = document.getElementById('weather-alert-siren');
var emergencySirenAcidRain = document.getElementById('weather-alert-siren-acidrain');
var emergencySirenNuclearFreeze = document.getElementById('weather-alert-siren-nuclearfreeze');
var emergencySirenRadStorm = document.getElementById('weather-alert-siren-radstorm');
var emergencySirenTornado = document.getElementById('weather-alert-siren-tornado');
var emergencyClear = document.getElementById('weather-alert-clear');

var playerList = new Array();

var volume = 0.05;

$(function() {
    console.log("Ready.");
    var socket = io(address, {secure: true});

    socket.on("connect_error", function(data)  {
        console.log("ERROR");
        console.log(data);
    });

    socket.on('connect', function() {
        console.log("Connected.");
        socket.emit('identify',{pageName:window.location.pathname, accountId:accountId, loggedIn:loggedIn, playerName:playerName});
    });

    socket.on('disconnect', function() {
        console.log("Disconnected.");
    });

    socket.on('playerList', function(data) {
        console.log("Player List:");
        console.log(data);

        playerList = data.players;
        timeStamp = data.current_time;
        var html = "";

        html += '<table class="table" style="border:none;">';
        html += '   <thead style="border:none;">';
        html += '       <tr><th class="uppercase" style="border-top:none;">ID</th><th class="uppercase" style="border-top:none;">Player</th><th class="uppercase" style="border-top:none;">Ping</th><th class="uppercase" style="border-top:none;">Time</th></tr>';
        html += '   </thead>';
        html += '   <tbody>';

        $('#current-players').html(playerList.length);
        if (playerList.length > 0) {
            for (var p=0; p<playerList.length; p++) {
                if (playerList[p].state == 3) {
                    if (playerList[p].name == "Survivor" && playerList[p].steam_name != "undefined") {
                        playerList[p].name = playerList[p].steam_name;
                    }
                    html += '       <tr><td style="border:none;width:10%;">'+playerList[p].id+'</td><td style="border:none;width:70%;">'+playerList[p].name+'</td><td style="border:none;width:10%;">'+playerList[p].ping+'</td><td style="border:none;width:10%;">'+convertToHM(timeStamp - playerList[p].joined)+'</td></tr>';
                }
            }
        }
        else {
            html += '       <tr><td colspan="4" style="text-align:center;border:none;">No Players Online</td></tr>';
        }
        html += '   </tbody>';
        html += '</table>';
        $('#playerlist-modal-body').html(html);
    });

    socket.on('status', function(data) {
        console.log("Server Update:");
        console.log(data);

        status = data;
        weather = data.weatherReport;
        time = data.time;

        if (data.max_players != undefined) {
            $('#max-players').html(data.max_players);
        }

        if (weather != undefined) {
            $('#weather-description').html(data.weatherReport.english);
            if (isDayTime(data.time)) {
                $('#weather-icon').attr('src','images/weather/'+data.weatherReport.day_icon);
            }
            else {
                $('#weather-icon').attr('src','images/weather/'+data.weatherReport.night_icon);
            }

            if (data.weatherReport.emergencyWeather == true) {
                $('#weather-alert').html("<strong>Severe Weather Alert:</strong> "+data.weatherReport.emergencyStatement).fadeIn('slow');
                if (previousEmergency == false) {
                    playEmergencySiren(data.weatherReport.english);
                }
                previousEmergency = true;
            }
            else {
                $('#weather-alert').fadeOut('slow');
                if (previousEmergency == true) {
                    emergencyClear.volume = volume;
                    emergencyClear.play();
                }
                previousEmergency = false;
            }

            var restartTime = data.next_restart_in.split(":");
            $('#next-restart').html("Server restart in "+parseInt(restartTime[0])+"h "+parseInt(restartTime[1])+"m");
        }
        if (time != undefined) {
            timeSet(time);
        }
    });

    $('#test-siren').click(function(e) {
        e.preventDefault();
        emergencySiren.volume = volume;
        emergencySiren.play();
    });

    $('#test-clear').click(function(e) {
        e.preventDefault();
        emergencyClear.volume = volume;
        emergencyClear.play();
    });
});

function playEmergencySiren(weatherType) {
    if (weatherType == "Acid Rain") {
        emergencySirenAcidRain.volume = volume;
        emergencySirenAcidRain.play();
    }
    else if (weatherType == "Nuclear Freeze") {
        emergencySirenNuclearFreeze.volume = volume;
        emergencySirenNuclearFreeze.play();
    }
    else if (weatherType == "Radiation Storm") {
        emergencySirenRadStorm.volume = volume;
        emergencySirenRadStorm.play();
    }
    else if (weatherType == "Tornado") {
        emergencySirenTornado.volume = volume;
        emergencySirenTornado.play();
    }
    else {
        emergencySiren.volume = volume;
        emergencySiren.play();
    }
}

function isDayTime(time) {
    var hm = time.split(":");
    if (hm[0] < 6 || hm[0] > 17) {
        return false;
    }
    return true;
}

function timeSet(time) {
    var hhmm = time.split(":");

    if (worldMinute != false) {
        //console.log("worldMinute:"+worldMinute+" newWorldMinute:"+hhmm[1]+" Difference:"+(worldMinute-hhmm[1]));
    }

    worldHour = hhmm[0];
    worldMinute = hhmm[1];
    drawTime();
    clearTimeout(updateTimeTimer);
    updateTime();
}

function updateTime() {
    updateTimeTimer = setTimeout(
        function() {
            worldMinute++;
            if (worldMinute > 59) {
                worldMinute = 0;
                worldHour++;
            }

            if (worldHour > 23) {
                worldHour = 0;
            }
            updateTime();
            drawTime();
        }, worldRate * 1000
    );
}

function drawTime() {
    var ampm = "AM";
    var hour = parseInt(worldHour);
    var minute = parseInt(worldMinute);
    minute = (minute>9?minute:"0"+minute);
    if (hour > 11) {
        ampm = "PM";
    }
    if (hour > 12) {
        hour = hour % 12;
    }
    if (hour == 0) {
        hour = 12;
    }

    $('#time').html(hour+":"+minute+" "+ampm);
}

function convertToHM(seconds) {
    var hours = Math.floor(seconds / 3600);
    minutes = Math.floor((seconds / 60) % 60);
    if (minutes < 10) {
        minutes = "0" + minutes;
    }
    return hours+":"+minutes;
}
