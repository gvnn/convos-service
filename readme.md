convos-service
===

[![Build Status](https://travis-ci.org/gvnn/convos-service.svg?branch=master)](https://travis-ci.org/gvnn/convos-service)

convos-service is site messaging micro service.

### Install

...

    $ composer install
    $ composer dumpautoload
    $ php artisan migrate:install
    

### Database

    CREATE USER 'convos'@'%' IDENTIFIED BY  'convos';

    GRANT USAGE ON * . * TO  'convos'@'%' IDENTIFIED BY  'convos' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

    CREATE DATABASE IF NOT EXISTS  `convos` ;
    
    $ php artisan migrate    
    $ php artisan db:seed
    
### Run

    $ php artisan serve

## Requirements

convos-service is built with [Lumen](http://lumen.laravel.com/). For details on how to install Lumen, please refer to
the project documentation [http://lumen.laravel.com/docs/installation](http://lumen.laravel.com/docs/installation).