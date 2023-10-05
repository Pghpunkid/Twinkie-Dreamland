<?php

    header('Content-Type: application/javascript');
    $files = scandir('.');

    echo "var itemList = new Array();\n\n";
    for ($f=0; $f<sizeof($files); $f++) {
        if ($files[$f] != "." && $files[$f] != "..") {
            if (isValidImage($files[$f])) {
                $data = file_get_contents($files[$f]);
                $b64_data = base64_encode($data);
                echo 'itemList.push({"name": "'.strtolower(getFileNameSansExtension($files[$f])).'", "image":"data:image/png;base64,'.$b64_data.'"});'."\n\n";

            }
        }
    }

    echo "var itemListIdx = new Array();\n\n";
    $i=0;
    for ($f=0; $f<sizeof($files); $f++) {
        if ($files[$f] != "." && $files[$f] != "..") {
            if (isValidImage($files[$f])) {
                echo 'itemListIdx["'.strtolower(getFileNameSansExtension($files[$f])).'"] = '.$i.";\n";
                $i++;
            }
        }
    }

    echo "var icons = new Array();\n
    for (var i=0; i<itemList.length; i++) {
        icons[itemList[i].name] = L.icon({
            iconUrl: itemList[i].image,
            iconSize: [48, 48],
            iconAnchor: [25, 43],
            popupAnchor: [0, -26]
        });
    }";

    function getFileNameSansExtension($file) {
        $file = str_replace("-","_",$file);
        if (is_numeric(substr($file, 0, 1))) {
            $file = "a".$file;
        }
        $file = str_replace("_48","",$file);
        $lastDot = strrpos($file,".");

        return substr($file, 0, $lastDot);
    }

    function isValidImage($file) {
        if (mime_content_type($file) == "image/jpeg" || mime_content_type($file) == "image/png") {
            return true;
        }
        return false;
    }

?>
