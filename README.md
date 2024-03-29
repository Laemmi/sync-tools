# Sync tools 

## Description
Synchronisation & Backup von Mysql-Datenbanken & Dateien. 

## Installation

    composer require laemmi/sync-tools

## Configuration
Create `config/lst-config.yml`. You can use template from `vendor/laemmi/sync-tools/lst-config.yml.dist`.
Add your credentials.

### ssh_force_transfer (bool) Default: true
If set to false mysql dump would be transfer with rsync. It is better on slow internet connections.

## Usage
Backup local database.

    ./vendor/lst database:backup
    
Dump database from remote to local destination.

    ./vendor/lst database:dump

Import Databasedump to local database. import_dump is optional name of database for import.

    ./vendor/lst database:import import_dump

Sync remote database to local database.

    ./vendor/lst database:sync

Sync files from remote or local to local destination.

    ./vendor/lst file:sync

Backup local files

    ./vendor/lst file:backup

## Test locally with docker

    docker-compose up -d

### Database backup (local)

    docker-compose exec php ./bin/lst database:backup

### Database dump

    docker-compose exec php ./bin/lst database:dump

### Database import
    
    docker-compose exec php ./bin/lst database:import

### Database synchronisation

    docker-compose exec php ./bin/lst database:sync

### File synchronisation

    docker-compose exec php ./bin/lst file:sync

### File backup (local)

    docker-compose exec php ./bin/lst file:backup