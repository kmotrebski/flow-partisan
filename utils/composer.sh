#!/usr/bin/env bash

# Usage:
#   composer.sh <subcommand> OR composer.sh composer <subcommand>
#   composer.sh status OR composer.sh composer status
#   composer.sh update OR composer.sh composer update

#made SSH not to ask about fingerprint
ssh-keyscan -H github.com >> ~/.ssh/known_hosts

docker run --rm --interactive --tty \
        --volume $PWD/tmp/composer:/tmp \
        --volume $PWD:/app \
        --volume $SSH_AUTH_SOCK:/ssh-auth.sock \
        --volume ~/.ssh/known_hosts:/root/.ssh/known_hosts \
        --env SSH_AUTH_SOCK=/ssh-auth.sock \
        --env COMPOSER_PROCESS_TIMEOUT=6000 \
        --name composer \
        composer:1.8.5 $*
