<?php
	include("class.SFTP.php");
	include("servercfg.php");

	$backupHome = "/home/user/MiscreatedBackups";

	//Create Server Folder
	if (!is_dir("$backupHome/$serverIP/backups")) {
		mkdir("$backupHome/$serverIP/backups");
	}
	chdir("$backupHome/$serverIP/backups");

	//Create Date Folder
	$date = new DateTime();
	$dateFormat = $date->format('Y-m-d');
	if (!is_dir($dateFormat)) {
		mkdir($dateFormat);
	}
	chdir($dateFormat);

	//Go Get It
	$ftp = new SFTP($ftpServerIP, $ftpServerUser, $ftpServerPassword, $ftpServerPort);
	if (!$ftp->connect()) {
		echo "Failed to connect to FTP Server.";
	}

	//Ping Perfect formatting is /IP_Port/<server root> for FTP.
	$ftp->get($serverIP."_".$serverPort."/miscreated.db","miscreated.db",FTP_BINARY);
	$ftp->get($serverIP."_".$serverPort."/hosting.cfg","hosting.cfg",FTP_BINARY);
	//sync(".");
	$ftp->close();

	chdir("$backupHome/$serverIP/backups");
	$dateFormatZip = $date->format('Y-m-d_Hi');
	$tar = 'tar -cvf "'.$dateFormatZip.'.tar" '.$dateFormat;
	shell_exec($tar);

	if (!is_dir("$backupHome/$serverIP/backups/current/")) {
		mkdir("$backupHome/$serverIP/backups/current/");
	}

	shell_exec("cp $dateFormat/miscreated.db $backupHome/$serverIP/backups/current/");
	shell_exec("rm -fr $dateFormat/");

	function sync($path) {
		global $ftp;

		$ftp->cd($path);
		chdir($path);

		$list = $ftp->ls(".");
		$pwd = $ftp->pwd();

		for ($l=0; $l<sizeof($list); $l++) {
			$file = $list[$l];

			if ($file != "." && $file != "..") {
				if (!file_exists($file)) {
					$isDir = $ftp->isdir("$pwd/$file");
					if ($isDir) {
						mkdir($file);
						echo "$pwd/$file\n";
						sync($file);
					}
					else {
						echo "$pwd/$file\n";
						$ftp->get($file,$file,FTP_BINARY);
					}
				}
			}
		}
		chdir("..");
		$ftp->cd("..");
	}
?>
