#!/bin/bash
set -e

clear

KO_DATE=$(date +%H%M%S)

docker run \
    --volume $(pwd):/var/ko_flow \
    --name ko_flow_${KO_DATE} \
    --rm \
    --entrypoint="" \
    php:7.1-fpm \
    php /var/ko_flow/start.php