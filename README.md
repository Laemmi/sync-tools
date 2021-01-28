[![pipeline status](https://gitlab.com/laemmi/sync-tools/badges/master/pipeline.svg)](https://gitlab.com/laemmi/sync-tools/-/commits/master)
[![coverage report](https://gitlab.com/laemmi/sync-tools/badges/master/coverage.svg)](https://gitlab.com/laemmi/sync-tools/-/commits/master)

# Sync tools 

## Description
Synchronisation, Backup von Datenbanken, Dateien.

## Installation

## Usage

## Test locally

### Database dump

    docker run -it --rm \
        -v ~/.ssh:/root/.ssh:cached \
        -v ${PWD}:/var/www/html:cached \
        registry.gitlab.com/laemmi-dockerimages/php-fpm:7.4 php ./bin/laemmi-sync-tools database:dump

### Database import
    
    docker run -it --rm \
        -v ~/.ssh:/root/.ssh:cached \
        -v ${PWD}:/var/www/html:cached \
        registry.gitlab.com/laemmi-dockerimages/php-fpm:7.4 php ./bin/laemmi-sync-tools database:import

### File synchronisation

    docker run -it --rm \
        -v ~/.ssh:/root/.ssh:cached \
        -v ${PWD}:/var/www/html:cached \
        registry.gitlab.com/laemmi-dockerimages/php-fpm:7.4 php ./bin/laemmi-sync-tools file:sync