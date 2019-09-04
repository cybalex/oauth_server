cybalex/oauth-server
====
About
----
cybalex/oauth-server is a stand-alone lightweight oauth2 server, based on 
[symfony 4.3 skeleton](https://github.com/symfony/skeleton/releases) and 
[fos oauth server bundle](https://github.com/FriendsOfSymfony/FOSOAuthServerBundle).

[![Build Status](https://travis-ci.org/cybalex/oauth_server.svg?branch=master)](https://travis-ci.org/cybalex/oauth_server)

Installation
----
- Clone the repository:
```shell script
git clone git@github.com:cybalex/oauth_server.git
```
- setup php-fpm, nginx, and mysql server. 
Here below is the example docker-compose file I used:

```yaml
//docker-compose.yml

version: "3.6"

services:
  oauth2_nginx:
    image: nginx:1.17
    volumes:
      - oauth_www_local:/var/www
      - ${PWD}:/var/www/oauth2
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
    ports:
      - 8081:80
    links:
      - oauth2_fpm
    networks:
      - oauth_local

  oauth2_fpm:
    image: cybalex/tutor-php-dev-local
    volumes:
      - oauth_www_local:/var/www
      - ${PWD}:/var/www/oauth2
      - ${PWD}/.composer:/var/www/.composer/
    environment:
      - COMPOSER_CACHE_DIR=/var/www/.composer/
    networks:
      - oauth_local

  oauth2_mysql:
    image: cybalex/mysql:latest
    networks:
      - oauth_local
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_USER: test
      MYSQL_PASSWORD: test
      MYSQL_DATABASE: oauth
    ports:
      - 3333:3306

networks:
  oauth_local:

volumes:
  oauth_www_local:
```

```
// nginx.conf
upstream oauth_fastcgi {
    server oauth2_fpm:9000;
}

server {
    #better make it 443 and add sertificates
    listen 80;

    server_name oauth.local www.oauth.local;

    root /var/www/oauth2/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass oauth_fastcgi;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
    }

    # return 404 for all other php files not matching the front controller
    # this prevents access to other php files you don't want to be accessible.
    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/oauth_error.log;
    access_log /var/log/nginx/oauth_access.log;
}
```

- Make sure to create .env.local file in the root directory of the project by copying .env.local.dist file. Modify it
according to your needs

- Run the containers
```shell script
docker-compose up -d && docker-compose ps
```
- Run php-fpm container with command `docker-compose run --rm oauth2_fpm bash` and inside the container execute
 the following commands:
```shell script
cd oauth2;
composer install
php bin/console doctrine:schema:update --force;
php bin/console fos:oauth-server:create-client --grant-type=password;
bin/console oauth-server:user:create --username=test --email=test@domain.com --password=test;
```

Grant type `password` requires client password and username to generate a token. Read more about possible values 
at [oauth2 official page](https://oauth.net/2/).

!!!!ATTENTION do not run doctrine:schema:update in production env if the project is using production database :D. Use
doctrine migrations instead!

Usage example
----
Use Postman or curl or whatever analog utility you like to generate an access token for user **test**:

URL example: http://localhost:8081/oauth/v2/token?client_id=4_6184oy4vhtcswgc4gco0okwcwok0okg0888g0c0wo808c4wow4&client_secret=2o5cvobsdaasc88840084kw4koco0o40ockso4ksgs84gwws44&grant_type=password&username=test&password=test&scopes=user%20admin%20profile_edit

URL explained: http://localhost:8082/oauth/v2/token?client_id=[primary-key-from-mysql-client-table]_[random-id-from-mysql-client-table]&client_secret=[secret_from_client_table]&username=[username_from_user_table]&password=[user-password-from-mysql-user-table]&grant_type=password&scopes=[space-separated-list-of-scopes]

The sample output should be:
```json
{
  "access_token":"MTkzNmRlNjU2MmJhZWZjMTE3MTc5ZTg2YjU3MjE2ZGY3NGMwN2Q0OTNhNzFiMDE1ZmY3Mjg0ZTQ1YzI5ZGY0Nw",
  "expires_in":"3600",
  "token_type":"Bearer",
  "scope":"user admin profile_edit",
  "refresh_token":"MmNhNDQxNTBhYjU1YmZiNTYwOTc5NWZhMDMzMWRkNzVkNDMxYjkwODk2YTQwOGIzOTIyZmFkMWRkOTQzNjE4Zg"
}
```

On production environment oauth server should share user, access_token, auth_code, client and refresh_token tables with
other parts of application, where access token is used.

ToDo
----
- ~~implement user create command~~;
- ~~implement user authentication, which would check, if all requested scopes are allowed for the user.~~
- ~~move entities and user provider into separate repository~~
- ~~get rid of hardcoded encryption algorithm~~ - make it possible to configure it from env file - decide to use bcryt by default
- create separate repository with deploy procedures for oauth_server project
- use post instead of get for access token endpoint
- decouple User scope authentication logic from oauth controller
- cover with phpunit tests;

Additional notes
----
1. Scopes parameter is required to get access token (if compared to default behaviour of fos oauth server, where
no scope provided results in all available scopes granted)
2. App does not require Symfony twig and forms components. Also, there is not fos user bundle dependency.
I suggest the least dependencies is the best.
