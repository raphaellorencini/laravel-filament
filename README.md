<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Laravel 11 Base with Docker

Base of Laravel 11 running in Docker with Nginx, Mysql and Redis

---
Just execute:
```bash
docker compose up -d
```

---
### Changing .env config
Change configs in docker/app/.app-env, here can be add more configs like **{{.Env.DB_HOST}}**

```
APP_NAME=Laravel  
APP_ENV=local  
APP_KEY=base64:YOUR_LARAVEL_KEY_HERE
APP_DEBUG=true  
APP_TIMEZONE=UTC  
APP_URL=http://localhost  
  
APP_LOCALE=en  
APP_FALLBACK_LOCALE=en  
APP_FAKER_LOCALE=en_US  
  
APP_MAINTENANCE_DRIVER=file  
# APP_MAINTENANCE_STORE=database  
  
BCRYPT_ROUNDS=12  
  
LOG_CHANNEL=stack  
LOG_STACK=single  
LOG_DEPRECATIONS_CHANNEL=null  
LOG_LEVEL=debug  
  
DB_CONNECTION=mysql  
DB_HOST={{.Env.DB_HOST}}  
DB_PORT={{.Env.DB_PORT}}  
DB_DATABASE={{.Env.DB_DATABASE}}  
DB_USERNAME={{.Env.DB_USERNAME}}  
DB_PASSWORD={{.Env.DB_PASSWORD}}  
  
SESSION_DRIVER=database  
SESSION_LIFETIME=120  
SESSION_ENCRYPT=false  
SESSION_PATH=/  
SESSION_DOMAIN=null  
  
BROADCAST_CONNECTION=log  
FILESYSTEM_DISK=local  
QUEUE_CONNECTION=database  
  
CACHE_STORE=database  
CACHE_PREFIX=  
  
MEMCACHED_HOST=127.0.0.1  
  
REDIS_CLIENT=phpredis  
REDIS_HOST=127.0.0.1  
REDIS_PASSWORD=null  
REDIS_PORT=6379  
  
MAIL_MAILER=log  
MAIL_HOST=127.0.0.1  
MAIL_PORT=2525  
MAIL_USERNAME=null  
MAIL_PASSWORD=null  
MAIL_ENCRYPTION=null  
MAIL_FROM_ADDRESS="hello@example.com"  
MAIL_FROM_NAME="${APP_NAME}"  
  
AWS_ACCESS_KEY_ID=  
AWS_SECRET_ACCESS_KEY=  
AWS_DEFAULT_REGION=us-east-1  
AWS_BUCKET=  
AWS_USE_PATH_STYLE_ENDPOINT=false  
  
VITE_APP_NAME="${APP_NAME}"
```

---
### docker-compose.yaml
This file can be modified for your local use.

```yaml
networks:  
  laravel-network:  
    driver: bridge  
  
services:  
  laravel-app:  
    build: ./docker/app  
    container_name: laravel-app  
    entrypoint: dockerize -template ./docker/app/.app-env:/var/www/.env -wait tcp://laravel-db:3306 -timeout 5s ./docker/entrypoint.sh  
    environment:  # Env variables to change in .env file
      - DB_HOST=laravel-db  
      - DB_PORT=3306  
      - DB_DATABASE=laravel_base  
      - DB_USERNAME=root  
      - DB_PASSWORD=root  
    volumes:  
      - .:/var/www  # Local volume shared
    networks:  
      - laravel-network  
    depends_on:  
      - laravel-db  
      - laravel-redis  
  
  laravel-nginx:  
    build: ./docker/nginx  
    container_name: laravel-nginx  
    working_dir: /var/www  
    entrypoint: dockerize -template ./docker/nginx/nginx.conf:/etc/nginx/conf.d/nginx.conf -timeout 5s ./docker/nginx/entrypoint.sh  
    environment:  # Env variables to change in nginx.conf
      - NGINX_HOST=laravel-app  
      - NGINX_PORT=9000  
    restart: always  
    tty: true  
    ports:  
      - "8000:80"
      - "8001:443"  
    volumes:  
      - .:/var/www  
    networks:  
      - laravel-network  
    depends_on:  
      - laravel-app  
  
  laravel-db:  
    image: mysql:8.0  
    command: --innodb-use-native-aio=0  
    container_name: laravel-db  
    restart: always  
    tty: true  
    ports:  
      - "3306:3306"  
    volumes:  
      - ./docker/mysql:/var/lib/mysql  # Local directory shared to storage MySQL data
    environment:  
      - MYSQL_DATABASE=laravel_base  
      - MYSQL_ROOT_PASSWORD=root  
    networks:  
      - laravel-network  
  
  laravel-redis:  
    image: redis:alpine  
    container_name: laravel-redis  
    expose:  
      - 6379  
    networks:  
      - laravel-network
```
