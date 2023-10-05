<?php

    $files = scandir('.');

    for ($f=0; $f<sizeof($files); $f++) {
        if ($files[$f] != "." && $files[$f] != "..") {
            if (isValidImage($files[$f])) {
                echo getFileNameSansExtension($files[$f])."<br/>";
            }
        }
    }

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
