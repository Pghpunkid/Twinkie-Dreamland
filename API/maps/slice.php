<?php
    //exit(0);
    set_time_limit(0);
    ini_set('memory_limit','3072M');
    $srcImagePath = 'miscreated-map-large_tdl_lofi.jpg';
    //$srcImagePath = 'miscreated-map-large.jpg';
    //$destLrgPath = '../images/Miramar_6.png';
    $mapName = 'island_lofi';

    $destSize = 256; //Doesn't Change.

    $srcSize = 2048;
    $steps = 2;
    $zoomLevel = 1;

    $sizes = json_decode('
    [
        {"zoomLevel":1,  "srcSize":2048, "steps":2},
        {"zoomLevel":2,  "srcSize":1024, "steps":4},
        {"zoomLevel":3,  "srcSize":512,  "steps":8},
        {"zoomLevel":4,  "srcSize":256,  "steps":16},
        {"zoomLevel":5,  "srcSize":128,  "steps":32},
        {"zoomLevel":6,  "srcSize":64,   "steps":64},
        {"zoomLevel":7,  "srcSize":32,   "steps":128}
    ]
    ',true);

    $src = imagecreatefromjpeg($srcImagePath);
    //$destLrg = imagecreatetruecolor($steps*$destSize,$steps*$destSize);

    $s=0;
    $zoomLevel = $sizes[$s]['zoomLevel'];
    $srcSize = $sizes[$s]['srcSize'];
    $steps = $sizes[$s]['steps'];
    echo "Zoom:".$zoomLevel." srcSize:".$srcSize." steps:".$steps."<br/>";

    for ($x=0; $x<$steps; $x++) {
        if (!file_exists($mapName.'/'.$zoomLevel)) {
            mkdir($mapName.'/'.$zoomLevel);
        }

        if (!file_exists($mapName.'/'.$zoomLevel.'/'.$x)) {
            mkdir($mapName.'/'.$zoomLevel.'/'.$x);
        }

        for ($y=0; $y<$steps; $y++) {
            //imagecopyresized ($destLrg, $src, $x*$destSize, $y*$destSize, $x*$srcSize, $y*$srcSize, $destSize, $destSize, $srcSize, $srcSize);

            $destSmlPath = $mapName.'/'.$zoomLevel.'/'.$x.'/'.$y.'.png';
            $destSml = imagecreatetruecolor($destSize,$destSize);
            imagecopyresized ($destSml, $src, 0, 0, $x*$srcSize, $y*$srcSize, $destSize, $destSize, $srcSize, $srcSize);
            imagepng($destSml,$destSmlPath,0);
            imagedestroy($destSml);
        }
    }

    //imagepng($destLrg,$destLrgPath,0);
    //imagedestroy($destLrg);
    imagedestroy($src);
    echo "Done.";
?>
