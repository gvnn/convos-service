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


## oAuth

The api supports oAuth2, Resource Owner Password Credentials Grant ([4.3](https://tools.ietf.org/html/rfc6749#section-4.3)).
It's possible to generate a new token executing a post to the oauth/access_token endpoint, specifying the following parameters

- username: the_username
- password: the_password
- grant_type: password
- client_id: the_client_id
- client_secret: the_client_secret

for example:

    $ curl http://localhost:8000/oauth/access_token -d 'grant_type=password&username=foo@domain.comrd=test&client_id=client1id&client_secret=client1secret'
    