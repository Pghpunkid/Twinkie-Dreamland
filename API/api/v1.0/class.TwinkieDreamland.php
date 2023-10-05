<?php

class TwinkieDreamland {
    private $db = false;
    private $SQLStats;
    private $errorLog;
    private $dateTimeFormat = false;

    function __construct() {
        $this->SQLStats = array();
        $this->errorLog = array();

        //Connect to server.
        if (!$this->initializeDB()) {
            $this->logError("init - Unable to intialize database");
            return false;
        }

        return true;
    }

    public function getPostData($page = false) {
        $pageCount = 0;

        //Get Page Count
        $query = "SELECT COUNT(*) FROM DB_WebPosts;";
        $result = $this->db->query($query);
        if (!$result) {
            $this->logError($this->db->error." - ".$query);
            return false;
        }

        $row = $result->fetch_row();
        $count = $row[0];
        $pageCount = ceil($count / 10);

        if (!$page) {
            $page = $pageCount;
        }

        $maxItems = $page * 10; //1x10=10, 2x10=20 etc.
        $minItems = $maxItems - 10; //10-10=10 20-10=10 etc.
        $query = "SELECT P.*,S.Name FROM DB_WebPosts P, DB_SteamNames S WHERE P.Author = S.AccountID ORDER BY PublishedDateTime DESC LIMIT $minItems,$maxItems";
        $result = $this->db->query($query);
        if (!$result) {
            $this->logError($this->db->error." - ".$query);
            return false;
        }
        $posts = array();
        while ($post = $result->fetch_assoc()) {
            array_push($posts,$post);
        }
        $tmp = array();
        $tmp['Query'] = $query;
        $tmp['Posts'] = $posts;
        $tmp['Page'] = $page;
        $tmp['PageCount'] = $pageCount;
        return $tmp;
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
}

?>
