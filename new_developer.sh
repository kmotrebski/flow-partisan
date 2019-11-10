#!/usr/bin/env bash
set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m'

utils/composer.sh install

docker pull php:7.1-fpm

if [ ! -f settings.php ]; then
    printf "${RED}Create file \"settings.php\" and fill it in with credentials. Use \"settings.php.example\" template.${NC}\n"
    exit 2
fi

printf "${GREEN}Done! You can start the app!${NC}\n"
