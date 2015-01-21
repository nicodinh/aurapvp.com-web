#!/bin/sh
cp /path/to/minecraft/bukkit/logs/server.log ./
php /path/to/pvpvs.php
rm server.log
cp /path/to/minecraft/bukkit/logs/server.log ./
php /path/to/pvpvs_cc.php
rm server.log
php /path/to/shotAndSlain.php