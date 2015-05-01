convos-service
===

[![Build Status](https://travis-ci.org/gvnn/convos-service.svg?branch=master)](https://travis-ci.org/gvnn/convos-service)

convos-service is site messaging micro service.

## Requirements

The Laravel framework has a few system requirements:

- PHP >= 5.4
- Mcrypt PHP Extension
- OpenSSL PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension

The application requirements are:

- Composer
- MySql
- SQLite (in memory for testing)

Optional

- Node
- Bower

## Install

1. Update composer dependencies

    $ composer install
    $ composer dump-autoload

2. Create a mysql database (execute in mysql shell)

    CREATE USER 'convos'@'%' IDENTIFIED BY 'convos';
    GRANT USAGE ON *.* TO 'convos'@'%' IDENTIFIED BY 'convos' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
    CREATE DATABASE IF NOT EXISTS `convos`;GRANT ALL PRIVILEGES ON `convos`.* TO 'convos'@'%';

3. Run migrations and seeds

    $ php artisan migrate:install
    $ php artisan migrate
    $ php artisan db:seed

### Optional

Update node and bower dependencies

    $ npm install
    $ bower install
    $ gulp


## Launch the application

    $ php artisan serve

## API

The api comes with already 2 registered users and 2 client applications.

- users: foo@domain.com/test and bar@domain.com/test
- applications: client1id/client1secret and client2id/client2secret

### Authentication

The api supports oAuth2, Resource Owner Password Credentials Grant ([4.3](https://tools.ietf.org/html/rfc6749#section-4.3)).
It's possible to generate a new token executing a post to the oauth/access_token endpoint, specifying the following parameters

- username: the_username
- password: the_password
- grant_type: password
- client_id: the_client_id
- client_secret: the_client_secret

for example:

    $ curl http://localhost:8000/oauth/access_token -d 'grant_type=password&username=foo@domain.com&password=test&client_id=client1id&client_secret=client1secret'
    
### Endpoints
    
####  