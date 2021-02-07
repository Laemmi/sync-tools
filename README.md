[![pipeline status](https://gitlab.com/laemmi/sync-tools/badges/master/pipeline.svg)](https://gitlab.com/laemmi/sync-tools/-/commits/master)
[![coverage report](https://gitlab.com/laemmi/sync-tools/badges/master/coverage.svg)](https://gitlab.com/laemmi/sync-tools/-/commits/master)

# Sync tools 

## Description
Synchronisation, Backup von Datenbanken, Dateien.

## Installation

    composer require laemmi/sync-tools

## Configuration
Create `config/lst-config.yml`. You can use template from `vendor/laemmi/sync-tools/lst-config.yml.dist`.
Add your credetials.

## Usage
Dump database from remote to local destination.

    ./vendor/lst database:dump

Import Databasedump to local database.

    ./vendor/lst database:import

Sync remote database to local database.

    ./vendor/lst database:sync

Sync files from remote to local destination.

    ./vendor/lst file:sync

## Test locally with docker

### Database dump

    docker run -it --rm \
        -v ~/.ssh:/root/.ssh:cached \
        -v ${PWD}:/var/www/html:cached \
        registry.gitlab.com/laemmi-dockerimages/php-fpm:7.4 php ./vendor/bin/lst database:dump

### Database import
    
    docker run -it --rm \
        -v ~/.ssh:/root/.ssh:cached \
        -v ${PWD}:/var/www/html:cached \
        registry.gitlab.com/laemmi-dockerimages/php-fpm:7.4 php ./vendor/bin/lst database:import

### Database synchronisation

    docker run -it --rm \
        -v ~/.ssh:/root/.ssh:cached \
        -v ${PWD}:/var/www/html:cached \
        registry.gitlab.com/laemmi-dockerimages/php-fpm:7.4 php ./vendor/bin/lst database:sync

### File synchronisation

    docker run -it --rm \
        -v ~/.ssh:/root/.ssh:cached \
        -v ${PWD}:/var/www/html:cached \
        registry.gitlab.com/laemmi-dockerimages/php-fpm:7.4 php ./vendor/bin/lst file:sync