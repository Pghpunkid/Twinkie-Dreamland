# Twinkie-Dreamland
A while back, I developed and operated a server for a game called Miscreated. The server was called Twinkie Dreamland, paying homage to Zombieland's character played by Woody Harrelson, who was on a mission to find Twinkie's in a post-apocalyptic zombie-infested world, similar to Miscreated. We even added our own Twinkie to the game, just to make sure we weren't misleading anyone. And yes, they were rare.

There are 5 parts to this project:
1. Website
2. Live Monitor/Announcement Feed
3. API
4. Backup Processor
5. The Game Server itself

This repository covers the first 4.

# Website
The website itself had a ton of features. For players, there was a map to find their clan mates bases and monitor their base health, a timer until their base automatically got deleted, the same for vehicles at their base, and a tool to map coordinates from the Survivatron GPS in the game. 

It would tell you the weather, time, the amount of players online, and who they were. It also contained a live EAS system that would warn you about hazardous weather events, and notify you of what the event was. It also included a change request feature to make requests for server features.

If a player was an administrator of the server, there was a realtime map that was present with key objects like airdrops, plane crash, tents, as well as vehicles and players. This was fed real time from the Live Monitor/Announcement Feed, to limit requests to the game server directly, for security, and reliability purposes.

All of this was linked to the game accounts using their Steam login, via Steam's API.

# Live Monitor/Accouncement Feed
The game server supports some level of direct communication to perform some operations. This would allow me to periodically make an announcement with a message with tips and tricks to help players. It also helped serve data to the website for use in the live server data events like the EMS and weather/player data.

# API
This API allows the website and other services to grab data from the MySQL server, which is periodically pulled via the Backup processor. This keeps the data semi-current, but prevents issues with read/write collisions with the games database.

The API is completely freestanding and is accessed by generating tokens from whitelisted IP addresses. This cannot be obtained from any other location. (See $api_whitelisted_addresses in API/V1.0/api-settings.php). Once the token is held, it can be used to get data from the API. This only ever touches the MySQL server, so as to not bombard the game server with requests.

The API also served up the map images for leaflet. In order to generate the images, I leveraged GD2 from PHP to do all the work. The sliced files are not here, because when packed, they are still over a gigabyte. (See API/maps/slice.php)

# Backup Processor
This is pulled periodically and is how the player data is mapped. Base data and Player data is derived from a SQLite database the game server creates. That file is copied via SFTP to the host server (Webserver was seperate from the game server), data extracted, and re-entered into a Mysql database every 4 hours or on demand if requested. 

# The Game Server Itself
The game server allows for a HTTP Endpoint to be used to access data. This file is intentionally not published, because if the port is not secure (its the game port +4), it can be used to grab the data. This file is obtainable by simply playing the game, but is obfuscated to deter malicious use.

All it does is provide specific endpoints for the data to be called, retrieved in JSON format, then be cached using NodeJS and relayed to requesting clients via websockets. Javascript then maps it accordingly, as well as refreshes every minute or so.

# So why is this here?
Two reasons. Show my work, and show others how to do something similar for the game. Its kind of complicated, but its not impossible. I believe it to be secure, and to my knowledge, it was (aside from the whole port +4 issue).

This project has been dead for over a year, and there may be issues with this code. But this is the last snapshot of it.

https://youtu.be/eQpTNy5E3gM
