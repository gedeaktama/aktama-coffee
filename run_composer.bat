@echo off
set "PATH=%PATH:C:\Program Files\Git\usr\bin;=%"
set "PATH=%PATH:C:\Program Files\Git\usr\bin=%"
echo Running composer install without Git MSYS unzip...
composer install --no-interaction
