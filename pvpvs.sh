#!/bin/sh
cp /path/to/minecraft/bukkit/logs/server.log ./
php /path/to/pvpvs.php
rm server.log
php /path/to/shotAndSlain.php
php /path/to/PointsEtTitre.php