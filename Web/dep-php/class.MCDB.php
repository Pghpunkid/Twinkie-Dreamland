<?php
    class MCDB {
        private $db;
        private $errorLog;
        private $backupGUID;
        private $SQLStats;

        function __construct($host, $user, $pass, $db) {
            $this->errorLog = array();
            return $this->connect($host, $user, $pass, $db);
        }

        private function connect($host, $user, $pass, $db) {
            $this->db = new MySQLi($host, $user, $pass, $db);
            if (!$this->db) {
                return false;
            }
            $this->SQLStats = array();
            $this->backupGUID = $this->selectBackup();
            return true;
        }

        private function logSqlTiming($query, $time) {
            $tmp = array();
            $tmp['Time'] = $time;
            $tmp['Query'] = $query;
            array_push($this->SQLStats, $tmp);
            return true;
        }

        public function getSQLStats() {
            return $this->SQLStats;
        }

        private function logError($error) {
            array_push($this->errorLog,$error);
            return true;
        }

        public function getErrorLog() {
            return $this->errorLog;
        }

        private function selectBackup() {
            $sql_start = microtime();
            $query = "SELECT GUID FROM DB_BackupHistory ORDER BY BackupDateTime DESC LIMIT 1;";
            $result = $this->db->query($query);
            $sql_end = microtime();
            $this->logSqlTiming($query, $sql_end-$sql_start);
            if (!$result) {
                $this->logError($this->db->error);
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError('No Backups Found.');
                return false;
            }

            $row = $result->fetch_assoc();
            return $row['GUID'];
        }

        public function getBackups() {
            $sql_start = microtime();
            $query = "SELECT * FROM DB_BackupHistory ORDER BY BackupDateTime DESC";
            $result = $this->db->query($query);
            $sql_end = microtime();
            $this->logSqlTiming($query, $sql_end-$sql_start);
            if (!$result) {
                $this->logError($this->db->error);
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError('No Backups Found.');
                return false;
            }

            $backups = array();
            while ($row = $result->fetch_assoc()) {
                array_push($backups,$row);
            }
            return $backups;
        }

        public function forceUseBackup($guid = null) {
            if ($guid == null) {
                return false;
            }
            $this->backupGUID = $guid;

            return true;
        }

        public function getBackupTimestamp() {
            $sql_start = microtime();
            $query = "SELECT BackupDateTime FROM DB_BackupHistory WHERE GUID='".$this->backupGUID."';";
            $result = $this->db->query($query);
            $sql_end = microtime();
            $this->logSqlTiming($query, $sql_end-$sql_start);
            if (!$result) {
                $this->logError($this->db->error);
                return false;
            }

            if ($result->num_rows == 0) {
                $this->logError('No Backups Found.');
                return false;
            }

            $row = $result->fetch_assoc();
            $buDT = new DateTime($row['BackupDateTime']);
            $buDTFormat = 'M jS Y g:iA';
            return $buDT->format($buDTFormat);
        }

        public function checkMaintenanceMode() {
            $sql_start = microtime();
            $query = "SELECT * FROM DB_SystemVars";
            $sql_end = microtime();
            $this->logSqlTiming($query, $sql_end-$sql_start);
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError('Query failed:'.$this->db->error.' '.$query);
                return false;
            }
            $row = $result->fetch_assoc();

            if ($row['MaintenanceMode'] == 'Y') {
                return true;
            }
            return false;
        }

        public function getUserServerAdminLevel($accountId) {
            if ($this->checkMaintenanceMode()) {
                return false;
            }

            $id = $this->db->real_escape_string($accountId);
            $query = "SELECT ServerAdminLevel FROM DB_SteamNames WHERE AccountID=$id";
            $sql_start = microtime();
            $result = $this->db->query($query);
            $sql_end = microtime();
            $this->logSqlTiming($query, $sql_end-$sql_start);
            if (!$result) {
                $this->logError('Query failed:'.$this->db->error.' '.$query);
                return false;
            }
            if ($result->num_rows != 1) {
                $this->logError('Query results unexpected:'.sizeof($returnedData));
                return false;
            }

            $row = $result->fetch_assoc();
            return $row['ServerAdminLevel'];
        }

        public function getUserInformation($accountId) {
            if ($this->checkMaintenanceMode()) {
                return false;
            }

            $id = $this->db->real_escape_string($accountId);
            $query = "SELECT S.Name, C.Radiation, C.Temperature, C.Water, C.Food, C.Health, C.PosX, C.PosY, C.Gender, C.AccountID, C.Data ".
                      "FROM ".
                      "Characters C ".
                      "LEFT JOIN DB_SteamNames S ON S.AccountID = C.AccountID ".
                      "WHERE C.AccountID=$id AND C.DBBackupGUID='".$this->backupGUID."'";
            $sql_start = microtime();
            $result = $this->db->query($query);
            $sql_end = microtime();
            $this->logSqlTiming($query, $sql_end-$sql_start);
            if (!$result) {
                $this->logError('Query failed:'.$this->db->error.' '.$query);
                return false;
            }

            if ($result->num_rows != 1) {
                $this->logError('Query results unexpected:'.sizeof($returnedData));
                return false;
            }

            $row = $result->fetch_assoc();
            $row['Data'] = json_decode($row['Data'],true);
            $row['GenderEnglish'] = ($row['Gender']==0?"Male":"Female");
            return $row;
        }

        public function getUserClanInformation($accountId) {
            if ($this->checkMaintenanceMode()) {
                return false;
            }

            $id = $this->db->real_escape_string($accountId);
            /*$query = "SELECT ".
                     "IF((SELECT COUNT(*) FROM ClanMembers WHERE AccountID = C.AccountID AND DBBackupGUID=C.DBBackupGUID)=0,\"N\",\"Y\") AS InClan, ".
                     "(SELECT IF(COUNT(*)=1,D.ClanName,NULL) FROM ClanMembers M, Clans D WHERE AccountID = C.AccountID AND D.ClanID=M.ClanID AND D.DBBackupGUID=C.DBBackupGUID AND M.DBBackupGUID=C.DBBackupGUID GROUP BY D.ClanName) AS ClanName, ".
                     "(SELECT IF(COUNT(*)=1,D.ClanID,NULL) FROM ClanMembers M, Clans D WHERE AccountID = C.AccountID AND D.ClanID=M.ClanID AND D.DBBackupGUID=C.DBBackupGUID AND M.DBBackupGUID=C.DBBackupGUID GROUP BY D.ClanID) AS ClanID, ".
                     "(SELECT D.OwnerAccountID FROM Clans D WHERE D.ClanID=(SELECT ClanID FROM ClanMembers WHERE AccountID=C.AccountID AND DBBackupGUID=C.DBBackupGUID) AND DBBackupGUID=C.DBBackupGUID) AS ClanOwner, ".
                     "(SELECT CONCAT('[',GROUP_CONCAT(CONCAT('{\"AccountID\":',M.AccountID,',\"Name\":\"',M.MemberName,'\"}')),']') FROM ClanMembers M WHERE M.ClanID=(SELECT ClanID FROM ClanMembers WHERE AccountID=C.AccountID AND DBBackupGUID=C.DBBackupGUID) AND M.AccountID != C.AccountID AND DBBackupGUID=C.DBBackupGUID) AS ClanMembers ".
                     "FROM ".
                     "Characters C ".
                     "LEFT JOIN DB_SteamNames S ON S.AccountID = C.AccountID ".
                     "WHERE C.AccountID=$id AND C.DBBackupGUID='".$this->backupGUID."'";

            $sql_start = microtime();
            $result = $this->db->query($query);
            $sql_end = microtime();
            $this->logSqlTiming($query, $sql_end-$sql_start);
            if (!$result) {
                $this->logError('Query failed:'.$this->db->error.' '.$query);
                return false;
            }

            if ($result->num_rows != 1) {
                $this->logError('Query results unexpected:'.sizeof($returnedData));
                return false;
            }

            $row = $result->fetch_assoc();
            $row['ClanMembers'] = json_decode($row['ClanMembers'],true);

            for ($c=0; $c<sizeof($row['ClanMembers']); $c++) {
                $clanPlayer = $this->getUserInformation($row['ClanMembers'][$c]['AccountID']);
                if ($clanPlayer !== false) {
                    $row['ClanMembers'][$c] = $clanPlayer;
                    $row['ClanMembers'][$c]['Structures'] = $this->getUserStructureInformation($row['ClanMembers'][$c]['AccountID']);

                    if ($row['ClanMembers'][$c]['Name'] == 'Survivor') {
                        $sql = "SELECT * FROM DB_SteamNames WHERE AccountID = ".$row['ClanMembers'][$c]['AccountID'];
                        $result2 = $this->db->query($sql);
                        if ($result2) {
                            if ($result2->num_rows == 1) {
                                $row2 = $result2->fetch_assoc();
                                $row['ClanMembers'][$c]['Name'] = $row2['Name'];
                            }
                        }
                    }
                }
            }*/

            $query = "SELECT ClanID FROM ClanMembers WHERE AccountID = $id AND DBBackupGUID='".$this->backupGUID."'";

            $clan = array();
            $clan['ClanMembers'] = array();
            $clan['InClan'] = 'N';
            $clan['ClanName'] = false;
            $clan['ClanOwner'] = false;
            $clan['ClanID'] = false;

            $result = $this->db->query($query);
            if (!$result) {
                $this->logError('Query failed:'.$this->db->error.' '.$query);
                return false;
            }

            if ($result->num_rows != 1) {
                $this->logError('No/Multiple Results:'.$this->db->error.' '.$query);
                return $row;
            }

            $row = $result->fetch_assoc();
            $clan['ClanID'] = $row['ClanID'];

            $query = "SELECT ClanName,OwnerAccountID FROM Clans WHERE ClanID=".$row['ClanID']." AND DBBackupGUID='".$this->backupGUID."'";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError('Query failed:'.$this->db->error.' '.$query);
                return false;
            }

            if ($result->num_rows != 1) {
                $this->logError('No/Multiple Results:'.$this->db->error.' '.$query);
                return $row;
            }

            $row = $result->fetch_assoc();
            $clan['ClanName'] = $row['ClanName'];
            $clan['OwnerAccountID'] = $row['OwnerAccountID'];

            $query = "SELECT AccountID,MemberName AS Name, IF(AccountID=".$clan['OwnerAccountID'].",\"Y\",\"N\") AS ClanOwner FROM ClanMembers WHERE AccountID != $id AND ClanID = ".$clan['ClanID']." AND DBBackupGUID='".$this->backupGUID."'";
            $result = $this->db->query($query);
            if (!$result) {
                $this->logError('Query failed:'.$this->db->error.' '.$query);
                return false;
            }

            while ($row = $result->fetch_assoc()) {
                array_push($clan['ClanMembers'],$row);
            }

            for ($c=sizeof($clan['ClanMembers'])-1; $c>=0; $c--) {
                $query = "SELECT * FROM Characters WHERE AccountID = ".$clan['ClanMembers'][$c]['AccountID']." AND DBBackupGUID='".$this->backupGUID."'";
                $result = $this->db->query($query);
                if (!$result || $result->num_rows != 1) {
                    unset($clan['ClanMembers'][$c]);
                }
                else {
                    $row = $result->fetch_assoc();
                    $clan['ClanMembers'][$c]['PosX'] = $row['PosX'];
                    $clan['ClanMembers'][$c]['PosY'] = $row['PosY'];
                    $clan['ClanMembers'][$c]['Radiation'] = $row['Radiation'];
                    $clan['ClanMembers'][$c]['Temperature'] = $row['Temperature'];
                    $clan['ClanMembers'][$c]['Water'] = $row['Water'];
                    $clan['ClanMembers'][$c]['Food'] = $row['Food'];
                    $clan['ClanMembers'][$c]['Health'] = $row['Health'];

                    if ($clan['ClanMembers'][$c]['Name'] == 'Survivor') {
                        $query = "SELECT * FROM DB_SteamNames WHERE AccountID = ".$clan['ClanMembers'][$c]['AccountID'];
                        $result = $this->db->query($query);
                        if ($result) {
                            $row = $result->fetch_assoc();
                            $clan['ClanMembers'][$c]['Name'] = $row['Name'];
                        }
                    }
                }
            }
            $clan['ClanMembers'] = array_values($clan['ClanMembers']);

            for ($c=0; $c<sizeof($clan['ClanMembers']); $c++) {
                $clan['ClanMembers'][$c]['Structures'] = $this->getUserStructureInformation($clan['ClanMembers'][$c]['AccountID']);
            }

            return $clan;
        }

        public function getUserStructureInformation($accountId) {
            if ($this->checkMaintenanceMode()) {
                return false;
            }

            $overallObjects = 0;
            $overallHealth = 0;
            $overallMaxHealth = 0;
            $hurtItems = array();

            $query = "SELECT AccountID, ClassName, PosX, PosY, AbandonTimer, Data, StructureGUID FROM Structures WHERE DBBackupGUID='".$this->backupGUID."' AND AccountID=$accountId";
            $sql_start = microtime();
            $result = $this->db->query($query);
            $sql_end = microtime();
            $this->logSqlTiming($query, $sql_end-$sql_start);

            $structures = array();
            if (!$result) {
                $this->logError('Query failed:'.$this->db->error.' '.$query);
                return false;
            }

            while ($row = $result->fetch_assoc()) {
                $row['Items'] = array();
                $row['Data'] = json_decode($row['Data'],true);
                $row['PartDamage'] = 0;
                if (isset($row['Data']['damage'])) {
                    $row['PartDamage'] = $row['Data']['damage'];
                }

                $overallObjects++;

                $query2 = "SELECT * FROM Items WHERE DBBackupGUID='".$this->backupGUID."' AND OwnerGUID='".$row['StructureGUID']."'";
                $sql_start = microtime();
                $result2 = $this->db->query($query2);
                $sql_end = microtime();
                $this->logSqlTiming($query2, $sql_end-$sql_start);

                if (!$result2) {
                    $this->logError('Query failed:'.$this->db->error.' '.$query2);
                    return false;
                }
                while ($row2 = $result2->fetch_assoc()) {
                    $row2['Data'] = json_decode($row2['Data'],true);
                    array_push($row['Items'],$row2);
                }

                $row['Parts'] = array();

                $query2 = "SELECT D.ClassName,D.MaxHealth,D.Towable,P.Data,P.PosX,P.PosY,P.PartTypeID,P.StructurePartGUID FROM StructureParts P, DB_StructurePartTypes D WHERE P.PartTypeID=D.PartTypeID AND P.StructureGUID=\"".$row['StructureGUID']."\" AND P.DBBackupGUID=\"".$this->backupGUID."\"";
                $sql_start = microtime();
                $result2 = $this->db->query($query2);
                $sql_end = microtime();
                $this->logSqlTiming($query2, $sql_end-$sql_start);

                if (!$result2) {
                    $this->logError('Query failed:'.$this->db->error.' '.$query2);
                    return false;
                }
                while ($row2 = $result2->fetch_assoc()) {
                    $row2['Items'] = array();
                    $row2['Data'] = json_decode($row2['Data'],true);

                    $row2['PartDamage'] = 0;
                    if (isset($row2['Data']['damage'])) {
                        $row2['PartDamage'] = $row2['Data']['damage'];
                    }

                    $overallObjects++;
                    $overallHealth += $row2['MaxHealth'] - $row2['PartDamage'];
                    $overallMaxHealth += $row2['MaxHealth'];

                    if ($row2['PartDamage'] > 0) {
                        array_push($hurtItems, $row2);
                    }

                    $query3 = "SELECT Data,ClassName,Slot FROM Items WHERE DBBackupGUID=\"".$this->backupGUID."\" AND OwnerGUID=\"".$row2['StructurePartGUID']."\"";

                    $sql_start = microtime();
                    $result3 = $this->db->query($query3);
                    $sql_end = microtime();
                    $this->logSqlTiming($query3, $sql_end-$sql_start);

                    if (!$result3) {
                        $this->logError('Query failed:'.$this->db->error.' '.$query3);
                        return false;
                    }
                    while ($row3 = $result3->fetch_assoc()) {
                        $row3['Data'] = json_decode($row3['Data'],true);
                        array_push($row2['Items'],$row3);
                    }
                    array_push($row['Parts'],$row2);
                }
                array_push($structures,$row);
            }
            $tmp = array();
            $tmp['Structures'] = $structures;
            $tmp['OverallObjects'] = $overallObjects;
            $tmp['OverallHealth'] = $overallHealth;
            $tmp['OverallMaxHealth'] = $overallMaxHealth;
            $tmp['DamagedItems'] = $hurtItems;
            return $tmp;
        }

        public function getVehicles() {
            $query = "SELECT PosX, PosY, Category, ClassName, AbandonTimer, Data FROM Vehicles WHERE DBBackupGUID='".$this->backupGUID."'";
            $sql_start = microtime();
            $result = $this->db->query($query);
            $sql_end = microtime();
            $this->logSqlTiming($query, $sql_end-$sql_start);

            $structures = array();
            if (!$result) {
                $this->logError('Query failed:'.$this->db->error.' '.$query);
                return false;
            }

            $vehicles = array();
            while ($row = $result->fetch_assoc()) {
                $row['Data'] = json_decode($row['Data'],true);
                array_push($vehicles, $row);
            }
            return $vehicles;
        }

        public function getDistance($x1, $y1, $x2, $y2) {
            $x = abs($x1 - $x2);
            $y = abs($y1 - $y2);

            return sqrt(($x * $x) + ($y * $y));
        }
    }
?>
