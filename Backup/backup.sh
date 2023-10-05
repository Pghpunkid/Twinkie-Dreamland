#!/bin/sh
cd /home/user/MiscreatedBackups
$(date +"%Y-%m-%d %T" > backup.log)
php perform_backup.php
php updatedb.php
