<?php

    $itemXMLPath = '../itemXMLs';

    $db_host = "ca1.pghnetwork.net";
    $db_user = "root";
    $db_pass = "Hunter1234";
    $db_db = "miscreated_online";

    $db = new Mysqli($db_host, $db_user, $db_pass, $db_db);

    $files = scandir($itemXMLPath);

    for ($f=0; $f<sizeof($files); $f++) {
        if ($files[$f] != ".." && $files[$f] != ".") {
            $data = file_get_contents($itemXMLPath."/".$files[$f]);
            $xml = simplexml_load_string($data);
            $json = json_encode($xml);
            $array = json_decode($json,true);

            $good = false;
            if (isset($array['base_part'])) {
                if (isset($array['base_part']['@attributes'])) {
                    if ($array['base_part']['@attributes']['type'] != ">") {
                        //echo "Type: ".$array['base_part']['@attributes']['type']." Name:".$array['base_part']['@attributes']['name']." Max Health:".(isset($array['base_part']['@attributes']['max_health'])?$array['base_part']['@attributes']['max_health']:0)."\n";
                        $good = true;

                        $typeID = $array['base_part']['@attributes']['type'];

                        $sql="SELECT * FROM DB_StructurePartTypes WHERE PartTypeID=$typeID";

                        $result = $db->query($sql);

                        if (!$result) {
                            echo "Well that failed... $sql\n\n";
                        }

                        else if ($result->num_rows == 0) {
                            $iconURL = false;
                            $iconLargeURL = false;
                            $id = $array['base_part']['@attributes']['type'];
                            $name = $array['base_part']['@attributes']['name'];
                            $maxHealth = (isset($array['base_part']['@attributes']['max_health'])?$array['base_part']['@attributes']['max_health']:0);
                            $towable = (isset($array['base_part']['@attributes']['towable'])?'Y':'N');

                            if (isset($array['steam']['param'])) {
                                $params = $array['steam']['param'];
                                for ($p=0; $p<sizeof($params); $p++) {
                                    if ($params[$p]['@attributes']['name'] == 'icon_url') {
                                        $iconURL = $params[$p]['@attributes']['value'];
                                    }
                                    else if ($params[$p]['@attributes']['name'] == 'icon_url_large') {
                                        $iconLargeURL = $params[$p]['@attributes']['value'];
                                    }
                                }
                            }

                            //echo "INSERT INTO DB_StructurePartTypes SET PartTypeID=$id, ClassName='$name', MaxHealth=$maxHealth, Towable='$towable', IconURL='$iconURL', IconURLLarge='$iconLargeURL', EnglishName=NULL, Description= NULL;\n";
                        }
                    }
                }
            }

            if (!$good) {
                echo "WHAT THE HELL IS THIS?\n".$json."\n\n";
            }
        }
    }



?>
