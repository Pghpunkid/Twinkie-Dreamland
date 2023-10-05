<?php
    class MCDBAPI {

        private $db = false;
        private $backupGUID = false;
        private $backup = false;
        private $SQLStats;
        private $errorLog;
        private $maxFood = 1500;
        private $maxWater = 1750;

        function __construct() {
            $this->SQLStats = array();
            $this->errorLog = array();

            //Connect to server.
            if (!$this->initializeDB()) {
                $this->logError("init - Unable to intialize database");
                return false;
            }

            //Select last backup.
            if (!$this->useLastBackup()) {
                $this->logError("init - Unable to select backup");
                return false;
            }

            if ($this->checkMaintenanceMode()) {
                return false;
            }
        }

        private function initializeDB() {
            include('api-settings.php');

            $this->db = new mysqli($api_db_host, $api_db_user, $api_db_pass, $api_db_db);
            if (!$this->db) {
                $this->logError($this->db->error." - ".$api_db_host.", ".$api_db_user.", ".$api_db_pass.", ".$api_db_db);
                return false;
            }
            return true;
        }

        private function logError($error) {
            array_push($this->errorLog,$error);
            return true;
        }

        public function getErrorLog() {
            return $this->errorLog;
        }

        /* Backup Related Functions */
        public function checkMaintenanceMode() {
            $query = "SELECT * FROM DB_SystemVars";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("checkMaintenanceMode() - ".$this->db->error." - ".$query);
                $this->backupGUID = false;
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError("checkMaintenanceMode() - No records found - ".$query);
                $this->backupGUID = false;
                return false;
            }

            $row = $result->fetch_assoc();
            if ($row['MaintenanceMode'] == 'Y') {
                return true;
            }
            return false;
        }

        private function useLastBackup() {
            $query = "SELECT * FROM DB_BackupHistory ORDER BY BackupDateTime DESC LIMIT 1";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("useLastBackup() - ".$this->db->error." - ".$query);
                $this->backupGUID = false;
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError("useLastBackup() - No backup records found - ".$query);
                $this->backupGUID = false;
                return false;
            }

            $row = $result->fetch_assoc();
            $this->backupGUID = $row['GUID'];
            $this->backup = $row;
            return true;
        }

        public function useBackup($backupGUID) {
            $backupGUID = $this->db->real_escape_string($backupGUID);
            $query = "SELECT * FROM DB_BackupHistory WHERE GUID=\"$backupGUID\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("useBackup() - ".$this->db->error." - ".$query);
                $this->backupGUID = false;
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError("useBackup() - No backup record found - ".$query);
                $this->backupGUID = false;
                return false;
            }

            $row = $result->fetch_assoc();
            $this->backupGUID = $row['GUID'];
            $this->backup = $row;
            return true;
        }

        public function getBackups() {
            $query = "SELECT * FROM DB_BackupHistory ORDER BY BackupDateTime DESC";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getBackups() - ".$this->db->error." - ".$query);
                $this->backupGUID = false;
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError("getBackups() - No backup records found - ".$query);
                $this->backupGUID = false;
                return false;
            }

            $backups = array();
            while ($backup = $result->fetch_assoc()) {
                array_push($backups,$backup);
            }
            return $backups;
        }

        public function getCurrentBackup() {
            return $this->backup;
        }

        public function getLastBackup() {
            $query = "SELECT * FROM DB_BackupHistory ORDER BY BackupDateTime DESC LIMIT 1";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("useLastBackup() - ".$this->db->error." - ".$query);
                $this->backupGUID = false;
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError("useLastBackup() - No backup records found - ".$query);
                $this->backupGUID = false;
                return false;
            }

            $backup = $result->fetch_assoc();
            return $backup;
        }

        /* Players */
        public function getPlayer($accountId = false) {
            $accountId = $this->db->real_escape_string($accountId);
            if ($this->backupGUID === false) {
                $this->logError("getPlayer() - Backup not selected");
                return false;
            }

            if ($accountId === false) {
                $this->logError("getPlayer() - No accountId provided");
                return false;
            }

            $query = "SELECT * FROM Characters WHERE AccountID=$accountId AND DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getPlayer() - ".$this->db->error." - ".$query);
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError("getPlayer() - No Results - ".$query);
                return false;
            }

            if ($result->num_rows > 1) {
                $this->logError("getPlayer() - Multiple Results - ".$query);
                return false;
            }

            $player = $result->fetch_assoc();
            $player['Food'] = round(($player['Food'] / $this->maxFood)*100);
            $player['Water'] = round(($player['Water'] / $this->maxWater)*100);
            $player['Temperature'] =  round($player['Temperature'],1);
            $player['Health'] =  round($player['Health'],1);
            $player['Radiation'] =  round($player['Radiation'],1);

            unset($player['DBBackupGUID']);
            unset($player['PosZ']);
            unset($player['RotZ']);
            unset($player['SelectedSlot']);
            unset($player['MapName']);
            unset($player['Data']);
            unset($player['Gender']);
            unset($player['GameServerID']);
            unset($player['CharacterID']);
            unset($player['DBCharacterID']);
            unset($player['CreationDate']);
            return $player;
        }

        public function getPlayerCreationDate($accountId = false) {
            $accountId = $this->db->real_escape_string($accountId);
            if ($this->backupGUID === false) {
                $this->logError("getPlayerCreationDate() - Backup not selected");
                return false;
            }

            if ($accountId === false) {
                $this->logError("getPlayerCreationDate() - No accountId provided");
                return false;
            }

            $query = "SELECT CreationDate FROM Characters WHERE AccountID=$accountId ORDER BY DBCharacterID ASC LIMIT 1";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getPlayerCreationDate() - ".$this->db->error." - ".$query);
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError("getPlayerCreationDate() - No Results - ".$query);
                return false;
            }

            if ($result->num_rows > 1) {
                $this->logError("getPlayerCreationDate() - Multiple Results - ".$query);
                return false;
            }

            $player = $result->fetch_assoc();
            return $player['CreationDate'];
        }

        public function getAllPlayers() {
            if ($this->backupGUID === false) {
                $this->logError("getAllPlayers() - Backup not selected");
                return false;
            }

            $query = "SELECT * FROM Characters WHERE DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getAllPlayers() - ".$this->db->error." - ".$query);
                return false;
            }

            $players = array();
            while ($player = $result->fetch_assoc()) {
                $player['Data'] = json_decode($player['Data'],true);
                $player['Food'] = round(($player['Food'] / $this->maxFood)*100);
                $player['Water'] = round(($player['Water'] / $this->maxWater)*100);
                $player['Temperature'] =  round($player['Temperature'],1);
                $player['Health'] =  round($player['Health'],1);
                $player['Radiation'] =  round($player['Radiation'],1);

                array_push($players,$player);
            }
            return $players;
        }

        public function getRecentPlayers() {
            if ($this->backupGUID === false) {
                $this->logError("getRecentPlayers() - Backup not selected");
                return false;
            }

            $query = "SELECT * FROM Characters C, DB_SteamNames S WHERE C.DBBackupGUID=\"".$this->backupGUID."\" AND S.AccountID=C.AccountID AND (TO_DAYS(SYSDATE()) - TO_DAYS(LastSeen)) <= 30 ORDER BY S.LastSeen ASC";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getRecentPlayers() - ".$this->db->error." - ".$query);
                return false;
            }

            $players = array();
            while ($player = $result->fetch_assoc()) {
                $player['Data'] = json_decode($player['Data'],true);
                $player['Food'] = round(($player['Food'] / $this->maxFood)*100);
                $player['Water'] = round(($player['Water'] / $this->maxWater)*100);
                $player['Temperature'] =  round($player['Temperature'],1);
                $player['Health'] =  round($player['Health'],1);
                $player['Radiation'] =  round($player['Radiation'],1);

                array_push($players,$player);
            }
            return $players;
        }

        public function getPlayerItems($accountId = false) {
            $accountId = $this->db->real_escape_string($accountId);
            if ($this->backupGUID === false) {
                $this->logError("getPlayerItems() - Backup not selected");
                return false;
            }

            if ($accountId === false) {
                $this->logError("getPlayerItems() - No accountId provided");
                return false;
            }

            //Get CharacterGUID.
            $player = $this->getPlayer($accountId);
            $characterGUID = $player['CharacterGUID'];
            $characterGUID = $this->db->real_escape_string($characterGUID);

            $query = "SELECT * FROM Items WHERE OwnerGUID=\"$characterGUID\" AND DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getPlayerItems() - ".$this->db->error." - ".$query);
                return false;
            }

            $playerItems = array();
            while ($playerItem = $result->fetch_assoc()) {
                $playerItem['Data'] = json_decode($player['Data'],true);
                array_push($playerItems,$playerItem);
            }
            return $playerItems;
        }

        public function getPlayerSteamName($accountId = false) {
            $accountId = $this->db->real_escape_string($accountId);
            if ($this->backupGUID === false) {
                $this->logError("getPlayerSteamName() - Backup not selected");
                return false;
            }

            if ($accountId === false) {
                $this->logError("getPlayerSteamName() - No accountId provided");
                return false;
            }

            $query = "SELECT Name FROM DB_SteamNames WHERE AccountID=$accountId";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getPlayerSteamName() - ".$this->db->error." - ".$query);
                return false;
            }

            if ($result->num_rows == 0) {
                $name_created = $this->setSteamName($accountId);
                if (!$name_created) {
                    $this->logError("getPlayerSteamName() - Unable to fetch Steam name.");
                    return false;
                }
                else {
                    return $this->getPlayerSteamName($accountId);
                }
            }

            if ($result->num_rows > 1) {
                $this->logError("getPlayerSteamName() - Multiple Results - ".$query);
                return false;
            }

            $name = $result->fetch_assoc();
            return $name['Name'];
        }

        public function getPlayerLastSeen($accountId = false) {
            $accountId = $this->db->real_escape_string($accountId);
            if ($this->backupGUID === false) {
                $this->logError("getPlayerLastSeen() - Backup not selected");
                return false;
            }

            if ($accountId === false) {
                $this->logError("getPlayerLastSeen() - No accountId provided");
                return false;
            }

            $query = "SELECT LastSeen FROM DB_SteamNames WHERE AccountID=$accountId";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getPlayerLastSeen() - ".$this->db->error." - ".$query);
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError("getPlayerSteamName() - No Results - ".$query);
                return false;
            }

            if ($result->num_rows > 1) {
                $this->logError("getPlayerSteamName() - Multiple Results - ".$query);
                return false;
            }

            $name = $result->fetch_assoc();

            if ($name['LastSeen'] == null) {
                $name['LastSeen'] = "--";
            }

            return $name['LastSeen'];
        }

        public function getPlayerServerAdminLevel($accountId) {
            $id = $this->db->real_escape_string($accountId);

            $query = "SELECT ServerAdminLevel FROM DB_SteamNames WHERE AccountID=$accountId";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getPlayerServerAdminLevel() - ".$this->db->error." - ".$query);
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError("getPlayerServerAdminLevel() - No Results - ".$query);
                return false;
            }

            if ($result->num_rows > 1) {
                $this->logError("getPlayerServerAdminLevel() - Multiple Results - ".$query);
                return false;
            }

            $row = $result->fetch_assoc();
            return $row['ServerAdminLevel'];
        }

        private function setSteamName($accountId = false) {
            $accountId = $this->db->real_escape_string($accountId);
            if ($this->backupGUID === false) {
                $this->logError("setSteamName() - Backup not selected");
                return false;
            }

            if ($accountId === false) {
                $this->logError("setSteamName() - No accountId provided");
                return false;
            }

            $data = json_decode(file_get_contents("https://twinkiedreamland.com/php/steamconvert.php?id=[U:1:$accountId]"),true);
            if (isset($data['response']['players'][0]['personaname']) && isset($data['response']['players'][0]['steamid'])) {
                $name = $data['response']['players'][0]['personaname'];
                $steamId = $data['response']['players'][0]['steamid'];
                $query = "INSERT INTO DB_SteamNames SET SteamID=$steamId, SteamID3=\"[U:1:$accountId]\", AccountID=$accountId, Name=\"$name\", LastUpdate=SYSDATE(), ServerAdminLevel=0";
                $result = $this->db->query($query);
                if (!$result) {
                    $this->logError("setSteamName() - ".$this->db->error." - ".$query);
                    return false;
                }
                return true;
            }
            else {
                $this->logError("setSteamName() - Data response malformed");
                return false;
            }
        }

        /* Structures */
        public function getPlayerPlotSign($accountId = false) {
            $accountId = $this->db->real_escape_string($accountId);
            if ($this->backupGUID === false) {
                $this->logError("getPlayerPlotSign() - Backup not selected");
                return false;
            }

            if ($accountId === false) {
                $this->logError("getPlayerPlotSign() - No accountId provided");
                return false;
            }

            $query = "SELECT * FROM Structures WHERE AccountID=$accountId AND ClassName='PlotSign' AND DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getPlayerPlotSign() - ".$this->db->error." - ".$query);
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError("getPlayerPlotSign() - No Results - ".$query);
                return false;
            }

            if ($result->num_rows > 1) {
                $this->logError("getPlayerPlotSign() - Multiple Results - ".$query);
                return false;
            }

            $structure = $result->fetch_assoc();
            $structure['Data'] = json_decode($structure['Data'],true);
            return $structure;
        }

        public function getAllPlayerPlotSigns() {
            if ($this->backupGUID === false) {
                $this->logError("getAllPlayerPlotSigns() - Backup not selected");
                return false;
            }

            $query = "SELECT * FROM Structures WHERE ClassName='PlotSign' AND DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getAllPlayerPlotSigns() - ".$this->db->error." - ".$query);
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError("getAllPlayerPlotSigns() - No Results - ".$query);
                return false;
            }

            $structures = array();
            while ($structure = $result->fetch_assoc()) {
                $structure['Data'] = json_decode($structure['Data'],true);
                array_push($structures,$structure);
            }
            return $structures;
        }

        public function getStructures($accountId = false) {
            $accountId = $this->db->real_escape_string($accountId);
            if ($this->backupGUID === false) {
                $this->logError("getStructures() - Backup not selected");
                return false;
            }

            if ($accountId === false) {
                $this->logError("getStructures() - No accountId provided");
                return false;
            }

            $query = "SELECT * FROM Structures WHERE AccountID=$accountId AND DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getStructures() - ".$this->db->error." - ".$query);
                return false;
            }

            $structures = array();
            while ($structure = $result->fetch_assoc()) {
                $structure['Data'] = json_decode($structure['Data'],true);
                array_push($structures, $structure);
            }
            return $structures;
        }

        public function getAllStructures() {
            if ($this->backupGUID === false) {
                $this->logError("getAllStructures() - Backup not selected");
                return false;
            }

            $query = "SELECT * FROM Structures WHERE DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getAllStructures() - ".$this->db->error." - ".$query);
                return false;
            }

            $structures = array();
            while ($structure = $result->fetch_assoc()) {
                $structure['Data'] = json_decode($structure['Data'],true);
                array_push($structures, $structure);
            }
            return $structures;
        }

        public function getStructureItems($structureGUID = false) {
            $structureGUID = $this->db->real_escape_string($structureGUID);
            if ($this->backupGUID === false) {
                $this->logError("getStructureItems() - Backup not selected");
                return false;
            }

            if ($structureGUID === false) {
                $this->logError("getStructureItems() - No StructureGUID provided");
                return false;
            }

            $query = "SELECT * FROM Items WHERE OwnerGUID=\"$structureGUID\" AND DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getStructureItems() - ".$this->db->error." - ".$query);
                return false;
            }

            $structureItems = array();
            while ($structureItem = $result->fetch_assoc()) {
                $structureItem['Data'] = json_decode($structureItem['Data'],true);
                array_push($structureItems, $structureItem);
            }
            return $structureItems;
        }

        public function getStructureParts($structureGUID = false) {
            $structureGUID = $this->db->real_escape_string($structureGUID);
            if ($this->backupGUID === false) {
                $this->logError("getStructureParts() - Backup not selected");
                return false;
            }

            if ($structureGUID === false) {
                $this->logError("getStructureParts() - No StructureGUID provided");
                return false;
            }

            $query = "SELECT S.*,D.ClassName,D.MaxHealth FROM StructureParts S LEFT JOIN DB_StructurePartTypes D ON D.PartTypeID=S.PartTypeID WHERE StructureGUID=\"$structureGUID\" AND DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getStructureParts() - ".$this->db->error." - ".$query);
                return false;
            }

            $structureParts = array();
            while ($structurePart = $result->fetch_assoc()) {
                $structurePart['Data'] = json_decode($structurePart['Data'],true);
                array_push($structureParts, $structurePart);
            }
            return $structureParts;
        }

        public function getStructurePartItems($structurePartGUID = false) {
            $structurePartGUID = $this->db->real_escape_string($structurePartGUID);
            if ($this->backupGUID === false) {
                $this->logError("getStructurePartItems() - Backup not selected");
                return false;
            }

            if ($structurePartGUID === false) {
                $this->logError("getStructurePartItems() - No StructurePartGUID provided");
                return false;
            }

            $query = "SELECT * FROM Items WHERE OwnerGUID=\"$structurePartGUID\" AND DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getStructurePartItems() - ".$this->db->error." - ".$query);
                return false;
            }

            $structurePartItems = array();
            while ($structurePartItem = $result->fetch_assoc()) {
                $structurePartItem['Data'] = json_decode($structurePartItem['Data'],true);
                array_push($structurePartItems, $structurePartItem);
            }
            return $structurePartItems;
        }

        /* Clans */
        public function getClan($clanId = false) {
            $clanId = $this->db->real_escape_string($clanId);
            if ($this->backupGUID === false) {
                $this->logError("getClan() - Backup not selected");
                return false;
            }

            if ($clanId === false) {
                $this->logError("getClan() - No clanId provided");
                return false;
            }

            $query = "SELECT * FROM Clans WHERE ClanID=$clanId AND DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getClan() - ".$this->db->error." - ".$query);
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError("getClan() - No Results - ".$query);
                return false;
            }

            $clan = $result->fetch_assoc();
            return $clan;
        }
        public function getClans() {
            $accountId = $this->db->real_escape_string($accountId);
            if ($this->backupGUID === false) {
                $this->logError("getClans() - Backup not selected");
                return false;
            }

            if ($accountId === false) {
                $this->logError("getClans() - No accountId provided");
                return false;
            }

            $query = "SELECT * FROM Clans WHERE DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getClans() - ".$this->db->error." - ".$query);
                return false;
            }

            $clans = array();
            while ($clan = $result->fetch_assoc()) {
                array_push($clans, $clan);
            }
            return $clans;
        }
        public function getPlayerClan($accountId = false) {
            $accountId = $this->db->real_escape_string($accountId);
            if ($this->backupGUID === false) {
                $this->logError("getPlayerClan() - Backup not selected");
                return false;
            }

            if ($accountId === false) {
                $this->logError("getPlayerClan() - No accountId provided");
                return false;
            }

            $query = "SELECT ClanID FROM ClanMembers WHERE AccountID=$accountId AND DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getPlayerClan() - ".$this->db->error." - ".$query);
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError("getPlayerClan() - No Results - ".$query);
                return false;
            }

            $clan = $result->fetch_row();
            $clanID = $clan[0];
            $clan = $this->getClan($clanID);
            return $clan;
        }

        public function getClanMembers($clanId = false) {
            $clanId = $this->db->real_escape_string($clanId);
            if ($this->backupGUID === false) {
                $this->logError("getClanMembers() - Backup not selected");
                return false;
            }

            if ($clanId === false) {
                $this->logError("getClanMembers() - No clanId provided");
                return false;
            }

            $query = "SELECT * FROM ClanMembers WHERE ClanID=$clanId AND DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getClanMembers() - ".$this->db->error." - ".$query);
                return false;
            }

            $clanMembers = array();
            while ($clanMember = $result->fetch_assoc()) {
                array_push($clanMembers, $clanMember);
            }
            return $clanMembers;
        }
        public function getAllClanMembers() {
            if ($this->backupGUID === false) {
                $this->logError("getAllClanMembers() - Backup not selected");
                return false;
            }

            $query = "SELECT * FROM ClanMembers WHERE DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getAllClanMembers() - ".$this->db->error." - ".$query);
                return false;
            }

            $clanMembers = array();
            while ($clanMember = $result->fetch_assoc()) {
                array_push($clanMembers, $clanMember);
            }
            return $clanMembers;
        }

        /* Vehicles */
        public function getVehicle($vehicleGUID = false) {
            $vehicleGUID = $this->db->real_escape_string($vehicleGUID);
            if ($this->backupGUID === false) {
                $this->logError("getVehicle() - Backup not selected");
                return false;
            }

            if ($vehicleGUID === false) {
                $this->logError("getVehicle() - No VehicleGUID provided");
                return false;
            }

            $query = "SELECT * FROM Vehicles WHERE VehicleGUID=\"$vehicleGUID\" AND DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getVehicle() - ".$this->db->error." - ".$query);
                return false;
            }

            $vehicle = $result->fetch_assoc();
            $vehicle['Data'] = json_decode($vehicle['Data'],true);
            return $vehicle;
        }
        public function getAllVehicles() {
            if ($this->backupGUID === false) {
                $this->logError("getAllVehicles() - Backup not selected");
                return false;
            }

            $query = "SELECT * FROM Vehicles WHERE DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getAllVehicles() - ".$this->db->error." - ".$query);
                return false;
            }

            $vehicles = array();
            while ($vehicle = $result->fetch_assoc()) {
                $vehicle['Data'] = json_decode($vehicle['Data'],true);
                array_push($vehicles, $vehicle);
            }
            return $vehicles;
        }

        public function getVehiclesNear($x = false, $y = false, $radius = false) {
            $x = $this->db->real_escape_string($x);
            $y = $this->db->real_escape_string($y);
            $radius = $this->db->real_escape_string($radius);

            if ($this->backupGUID === false) {
                $this->logError("getVehiclesNear() - Backup not selected");
                return false;
            }

            if ($x === false) {
                $this->logError("getVehiclesNear() - No X Coordinate provided");
                return false;
            }

            if ($y === false) {
                $this->logError("getVehiclesNear() - No Y Coordinate provided");
                return false;
            }

            if ($radius === false) {
                $this->logError("getVehiclesNear() - No radius provided");
                return false;
            }

            $query = "SELECT * FROM Vehicles WHERE DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getVehiclesNear() - ".$this->db->error." - ".$query);
                return false;
            }

            $vehicles = array();
            while ($vehicle = $result->fetch_assoc()) {
                $distance = $this->getDistance($x,$y,$vehicle['PosX'],$vehicle['PosY']);
                if ($distance <= $radius) {
                    $vehicle['Distance'] = $distance;
                    $vehicle['Data'] = json_decode($vehicle['Data'],true);
                    array_push($vehicles, $vehicle);
                }
            }
            return $vehicles;
        }

        public function getVehicleItems($vehicleGUID = false) {
            $vehicleGUID = $this->db->real_escape_string($vehicleGUID);
            if ($this->backupGUID === false) {
                $this->logError("getVehicleItems() - Backup not selected");
                return false;
            }

            if ($vehicleGUID === false) {
                $this->logError("getVehicleItems() - No VehicleGUID provided");
                return false;
            }

            $query = "SELECT * FROM Items WHERE OwnerGUID=\"$vehicleGUID\" AND DBBackupGUID=\"".$this->backupGUID."\"";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getVehicleItems() - ".$this->db->error." - ".$query);
                return false;
            }

            $vehicleItems = array();
            while ($vehicleItem = $result->fetch_assoc()) {
                $vehicleItem['Data'] = json_decode($vehicleItem['Data'],true);
                array_push($vehicleItems, $vehicleItem);
            }
            return $vehicleItems;
        }

        /* Stats */
        public function getDailyMaxPlayerCount($date = false) {
            if ($date === false) {
                $this->logError("getDailyMaxPlayerCount() - No date provided");
                return false;
            }

            $sDate = new DateTime(mysql_escape($date));
            if (!$sDate) {
                $this->logError("getDailyMaxPlayerCount() - Invalid date");
            }

            $dateFormatted = $sDate->format('Y-m-d');

            $query = "SELECT IFNULL(MAX(PlayerCount),0) AS PlayerCount FROM DB_PlayerSnapshot WHERE TO_DAYS(UpdateDateTime) = TO_DAYS('$dateFormatted')";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getDailyMaxPlayerCount() - ".$this->db->error." - ".$query);
                return false;
            }

            $row = $result->fetch_assoc();
            return $row['PlayerCount'];
        }

        public function getDailyPlayerCountBreakdown($date = false) {
            if ($date === false) {
                $this->logError("getDailyPlayerCountBreakdown() - No date provided");
                return false;
            }

            $sDate = new DateTime(mysql_escape($date));
            if (!$sDate) {
                $this->logError("getDailyPlayerCountBreakdown() - Invalid date");
            }

            $dateFormatted = $sDate->format('Y-m-d');

            $query = "SELECT DATE_FORMAT(UpdateDateTime, '%Y-%m-%d %H'), IFNULL(MAX(PlayerCount),0) AS PlayerCount FROM DB_PlayerSnapshot WHERE TO_DAYS(UpdateDateTime) = TO_DAYS($dateFormatted) GROUP BY DATE_FORMAT(UpdateDateTime,'%Y-%m-%d %H')";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getDailyPlayerCountBreakdown() - ".$this->db->error." - ".$query);
                return false;
            }

            $row = $result->fetch_assoc();
            return $row['PlayerCount'];
        }

        public function getWeeklyPlayerCounts($date = false) {
            if ($date === false) {
                $this->logError("getWeeklyPlayerCounts() - No date provided");
                return false;
            }

            $sDate = new DateTime(mysql_escape($date));
            if (!$sDate) {
                $this->logError("getWeeklyPlayerCounts() - Invalid date");
            }

            $dateFormatted = $sDate->format('Y-m-d');

            $query = "SELECT DATE_FORMAT(UpdateDateTime, '%Y-%m-%d %H'), IFNULL(MAX(PlayerCount),0) AS PlayerCount FROM DB_PlayerSnapshot WHERE TO_DAYS(UpdateDateTime) = TO_DAYS($dateFormatted) GROUP BY DATE_FORMAT(UpdateDateTime,'%Y-%m-%d %H')";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getWeeklyPlayerCounts() - ".$this->db->error." - ".$query);
                return false;
            }

            $row = $result->fetch_assoc();
            return $row['PlayerCount'];
        }

        public function getRecentPlayerCountBreakdown() {
            $query = "SELECT DATE_FORMAT(UpdateDateTime, '%Y-%m-%d %H:00:00') AS Hour, IFNULL(MAX(PlayerCount),0) AS PlayerCount FROM DB_PlayerSnapshot WHERE UNIX_TIMESTAMP(UpdateDateTime) >= UNIX_TIMESTAMP(SYSDATE())-86400 GROUP BY DATE_FORMAT(UpdateDateTime,'%Y-%m-%d %H:00:00')";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getDailyPlayerCountBreakdown() - ".$this->db->error." - ".$query);
                return false;
            }

            $hours = array();
            while ($hour = $result->fetch_assoc()) {
                array_push($hours, $hour);
            }
            return $hours;
        }

        public function getWeeklyPlayerCountBreakdown() {
            $query = "SELECT DATE_FORMAT(UpdateDateTime, '%Y-%m-%d') AS Date, IFNULL(MAX(PlayerCount),0) AS PlayerCount FROM DB_PlayerSnapshot WHERE TO_DAYS(UpdateDateTime) >= TO_DAYS(SYSDATE())-7 GROUP BY DATE_FORMAT(UpdateDateTime,'%Y-%m-%d')";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getWeeklyPlayerCountBreakdown() - ".$this->db->error." - ".$query);
                return false;
            }

            $dates = array();
            while ($date = $result->fetch_assoc()) {
                array_push($dates, $date);
            }
            return $dates;
        }

        public function getBiWeeklyPlayerCountBreakdown() {
            $query = "SELECT DATE_FORMAT(UpdateDateTime, '%Y-%m-%d') AS Date, IFNULL(MAX(PlayerCount),0) AS PlayerCount FROM DB_PlayerSnapshot WHERE TO_DAYS(UpdateDateTime) >= TO_DAYS(SYSDATE())-14 GROUP BY DATE_FORMAT(UpdateDateTime,'%Y-%m-%d')";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getBiWeeklyPlayerCountBreakdown() - ".$this->db->error." - ".$query);
                return false;
            }

            $dates = array();
            while ($date = $result->fetch_assoc()) {
                array_push($dates, $date);
            }
            return $dates;
        }

        public function getActivePlayerStats() {
            $query = "SELECT COUNT(*) AS Count FROM DB_SteamNames WHERE TO_DAYS(LastSeen) >= TO_DAYS(SYSDATE())-7";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getActivePlayerStats() - ".$this->db->error." - ".$query);
                return false;
            }
            $count = $result->fetch_assoc();

            $counts = array();
            $counts['Active'] = $count['Count'];


            $query = "SELECT COUNT(*) AS Count FROM DB_SteamNames WHERE TO_DAYS(LastSeen) < TO_DAYS(SYSDATE())-7 OR LastSeen IS NULL";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getActivePlayerStats() - ".$this->db->error." - ".$query);
                return false;
            }
            $count = $result->fetch_assoc();
            $counts['Inactive'] = $count['Count'];

            $query = "SELECT COUNT(*) AS Count FROM DB_SteamNames";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError("getActivePlayerStats() - ".$this->db->error." - ".$query);
                return false;
            }
            $count = $result->fetch_assoc();
            $counts['Total'] = $count['Count'];

            return $counts;
        }

        /* Other */
        private function getDistance($x1, $y1, $x2, $y2) {
            $x = abs($x1 - $x2);
            $y = abs($y1 - $y2);

            return sqrt(($x * $x) + ($y * $y));
        }

    }
?>
