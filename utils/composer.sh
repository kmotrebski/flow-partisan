#!/usr/bin/env bash

mkdir -p tmp

docker run --rm --interactive --tty \
        --volume $PWD/tmp/composer:/tmp \
        --volume $PWD:/app \
        --env COMPOSER_PROCESS_TIMEOUT=6000 \
        --name composer \
        composer:1.8.5 $*

