# Sync tools 

## Description
Synchronisation & Backup von Mysql-Datenbanken & Dateien. 

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

    docker-compose up -d

### Database dump

    docker-compose exec php ./bin/lst database:dump

### Database import
    
    docker-compose exec php ./bin/lst database:import

### Database synchronisation

    docker-compose exec php ./bin/lst database:sync

### File synchronisation

    docker-compose exec php ./bin/lst file:sync