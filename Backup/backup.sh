#!/bin/sh
cd /home/phunter/MiscreatedBackups
$(date +"%Y-%m-%d %T" > backup.log)
php perform_backup.php
php updatedb.php
