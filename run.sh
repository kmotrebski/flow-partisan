#!/bin/bash
set -e

clear



docker run \
    --volume $(pwd):/var/ko_flow \
    --name ko_flow \
    --rm \
    --entrypoint="" \
    php:7.1-fpm \
    php /var/ko_flow/start.php