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
- phpunit

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

### Configuration

The application uses .env library [https://github.com/vlucas/phpdotenv](https://github.com/vlucas/phpdotenv). By default
it connects to a mysql database called *convos*, with username and passowrd *convos*. To change the settings simply
create a .evn file, below an example

    APP_ENV=local
    APP_DEBUG=true
    APP_KEY=B5UwJsg7VpNewUGAfiIz3glPeEqwKAmL

    DB_CONNECTION=mysql
    DB_HOST=localhost
    DB_DATABASE=convos
    DB_USERNAME=convos
    DB_PASSWORD=convos

    CACHE_DRIVER=file
    SESSION_DRIVER=file
    QUEUE_DRIVER=sync

    MAIL_DRIVER=smtp
    MAIL_HOST=mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=null
    MAIL_PASSWORD=null


## Launch the application

    $ php artisan serve
    
### Testing

    $ phpunit

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
    
#### POST oauth/access_token

Generates a new access token

**parameters**

- username: the_username
- password: the_password
- grant_type: password
- client_id: the_client_id
- client_secret: the_client_secret

**curl**

    curl -i -X POST \
       -H "Authorization:Bearer p4t2LV7ZrEWxhCWyCyVSp3VpMDs43Qw5lJXcqK7i" \
       -d "subject=test" \
       -d "recipient=2" \
       -d "body=test" \
     'http://localhost:8000/api/v1/convos'

**response**

    {
        "access_token":"NaQfZQJ9Gkr3NoLMSIYtX9OvHku3XEGlYGxElbpT",
        "token_type":"Bearer",
        "expires_in":3600
    }
    
#### POST /api/v1/convos

Creates a new conversation

**parameters**
    
- recipient: user id
- subject: text, max 140 chars
- body: message body

**curl**

	curl -i -X POST \
	   -H "Authorization:Bearer p4t2LV7ZrEWxhCWyCyVSp3VpMDs43Qw5lJXcqK7i" \
	   -d "subject=test" \
	   -d "recipient=2" \
	   -d "body=test" \
	 'http://localhost:8000/api/v1/convos'
 
**response**

	{
		"subject": "test",
		"created_by": "1",
		"updated_at": "2015-05-01 16:34:34",
		"created_at": "2015-05-01 16:34:34",
		"id": 12,
		"messages": [
		    {
		        "id": "7",
		        "conversation_id": "12",
		        "body": "test",
		        "user_id": "1",
		        "created_at": "2015-05-01 16:34:34",
		        "updated_at": "2015-05-01 16:34:34"
		    }
		],
		"participants": [
		    {
		        "id": "18",
		        "is_creator": true,
		        "is_read": true,
		        "read_at": "2015-05-01 16:34:34",
		        "user_id": "1",
		        "conversation_id": "12",
		        "created_at": "2015-05-01 16:34:34",
		        "updated_at": "2015-05-01 16:34:34"
		    },
		    {
		        "id": "19",
		        "is_creator": false,
		        "is_read": false,
		        "read_at": null,
		        "user_id": "2",
		        "conversation_id": "12",
		        "created_at": "2015-05-01 16:34:34",
		        "updated_at": "2015-05-01 16:34:34"
		    }
		]
	}

#### GET /api/v1/convos/[id]

Returns a conversation details

**curl**

	curl -i -X GET \
	   -H "Authorization:Bearer p4t2LV7ZrEWxhCWyCyVSp3VpMDs43Qw5lJXcqK7i" \
	 'http://localhost:8000/api/v1/convos/12'

**response**	

	{
		"id": "12",
		"subject": "test",
		"created_by": "1",
		"created_at": "2015-05-01 16:34:34",
		"updated_at": "2015-05-01 16:34:34",
		"participants": [
		    {
		        "id": "18",
		        "is_creator": true,
		        "is_read": true,
		        "read_at": "2015-05-01 16:34:34",
		        "user_id": "1",
		        "conversation_id": "12",
		        "created_at": "2015-05-01 16:34:34",
		        "updated_at": "2015-05-01 16:34:34"
		    },
		    {
		        "id": "19",
		        "is_creator": false,
		        "is_read": false,
		        "read_at": null,
		        "user_id": "2",
		        "conversation_id": "12",
		        "created_at": "2015-05-01 16:34:34",
		        "updated_at": "2015-05-01 16:34:34"
		    }
		]
	}

#### GET /api/v1/convos

Get list of conversations

**parameters**

- limit: number of results. default 25, max 100
- page: results page
- until: filter results until specific timestamp. ISO 8601 

**curl**

	curl -i -X GET \
	   -H "Authorization:Bearer p4t2LV7ZrEWxhCWyCyVSp3VpMDs43Qw5lJXcqK7i" \
	 'http://localhost:8000/api/v1/convos?limit=2&page=1'

**response**

	{
		"pagination": {
		    "page": 1,
		    "limit": 2,
		    "count": 7
		},
		"conversations": [
		    {
		        "id": "12",
		        "subject": "test",
		        "updated_at": "2015-05-01 16:34:34",
		        "is_read": "1",
		        "created_by": "1"
		    },
		    {
		        "id": "11",
		        "subject": "test",
		        "updated_at": "2015-05-01 16:33:00",
		        "is_read": "1",
		        "created_by": "1"
		    }
		]
	}

#### PUT /api/v1/convos/[id]

Updates a conversation

**parameters**

- is_read: 0 or 1

**curl**

	curl -i -X PUT \
	   -H "Authorization:Bearer p4t2LV7ZrEWxhCWyCyVSp3VpMDs43Qw5lJXcqK7i" \
	   -H "Content-Type:application/x-www-form-urlencoded" \
	   -d "is_read=0" \
	 'http://localhost:8000/api/v1/convos/12'

**response**

	{
		"id": "12",
		"subject": "test",
		"created_by": "1",
		"created_at": "2015-05-01 16:34:34",
		"updated_at": "2015-05-01 16:34:34"
	}

#### DELETE /api/v1/convos/[id]

Deletes a conversation

**curl**

	curl -i -X DELETE \
	   -H "Authorization:Bearer p4t2LV7ZrEWxhCWyCyVSp3VpMDs43Qw5lJXcqK7i" \
	 'http://localhost:8000/api/v1/convos/12'
	 
**response**

	{
		"id": "12",
		"subject": "test",
		"created_by": "1",
		"created_at": "2015-05-01 16:34:34",
		"updated_at": "2015-05-01 16:34:34"
	}

#### POST /api/v1/convos/[id]/messages

Creates a new message

**parameters**

- body: message body

**curl**

	curl -i -X POST \
	   -H "Authorization:Bearer p4t2LV7ZrEWxhCWyCyVSp3VpMDs43Qw5lJXcqK7i" \
	   -H "Content-Type:application/x-www-form-urlencoded" \
	   -d "body=message body" \
	 'http://localhost:8000/api/v1/convos/11/messages'

**response**

	{
		"user_id": "1",
		"body": "message body",
		"conversation_id": "11",
		"updated_at": "2015-05-01 17:08:01",
		"created_at": "2015-05-01 17:08:01",
		"id": 10
	}

#### GET /api/v1/convos/[id]/messages

Returns a list of messages in a conversation

**parameters**

- limit: number of results. default 25, max 100
- page: results page
- until: filter results until specific timestamp. ISO 8601 

**curl**

	curl -i -X GET \
	   -H "Authorization:QTcnifuX2HFHxMNcmScAo6I1mZbZnvnCpTDT4H8T" \
	   -H "Content-Type:application/x-www-form-urlencoded" \
	 'http://localhost:8000/api/v1/convos/11/messages?page=2&limit=1'

**response**

	{
		"pagination": {
		    "page": 2,
		    "limit": 1,
		    "count": 4
		},
		"messages": [
		    {
		        "id": "9",
		        "body": "message body",
		        "updated_at": "2015-05-01 17:07:45",
		        "user_id": "1"
		    }
		]
	}


#### DELETE /api/v1/convos/[id]/messages/[id]

Deletes a message from the conversation

**curl**

	curl -i -X DELETE \
	   -H "Authorization:QTcnifuX2HFHxMNcmScAo6I1mZbZnvnCpTDT4H8T" \
	 'http://localhost:8000/api/v1/convos/11/messages/9'

**response**

	{
		"id": "9",
		"conversation_id": "11",
		"body": "message body",
		"user_id": "1",
		"created_at": "2015-05-01 17:07:45",
		"updated_at": "2015-05-01 17:07:45"
	}



