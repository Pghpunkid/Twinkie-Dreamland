<?php

	if (!isset($_GET['mapName']) || !isset($_GET['X']) || !isset($_GET['Y']) || !isset($_GET['Z'])) {
		header('HTTP/1.0 403 Forbidden');
		echo '<h1>FORBIDDEN.</h1>';
	}

	$mapName = $_GET['mapName'];
	$x = $_GET['X'];
	$y = $_GET['Y'];
	$z = $_GET['Z'];

	$wr = (isset($_GET['wr'])?$_GET['wr']:'0');
	$wg = (isset($_GET['wg'])?$_GET['wg']:'0');
	$wb = (isset($_GET['wb'])?$_GET['wb']:'0');

	$fileName="$mapName/$z/$x/$y.png";
	if (file_exists($fileName)) {
		$data = file_get_contents("$mapName/$z/$x/$y.png");
		header('Content-Type: image/png');
		echo $data;
	}
	else if ($x < 0 || $y < 0) {
		$gdi = imagecreatetruecolor(256,256);
		header ('Content-Type: image/png');
		$water = imagecolorallocate($gdi,$wr,$wg,$wb);
		imagefilledrectangle($gdi,0,0,256,256,$water);
		imagepng($gdi);
	}
	else if ($x > (pow(2,$z)-1) || $y > (pow(2,$z)-1)) {
		$gdi = imagecreatetruecolor(256,256);
                header ('Content-Type: image/png');
				$water = imagecolorallocate($gdi,$wr,$wg,$wb);
                imagefilledrectangle($gdi,0,0,256,256,$water);
                imagepng($gdi);
	}
	else {
		header('HTTP/1.0 404 Not Found');
                echo '<h1>NOT HERE</h1>';
	}
?>
