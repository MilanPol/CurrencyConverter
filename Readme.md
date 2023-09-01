# Currency Converter $€£¥₹฿

## Description

This is project is supplied with a complete stack for running Symfony 6.2 into Docker containers using docker-compose tool.

It is composed by 4 containers:

- `nginx`, acting as the webserver.
- `php`, the PHP-FPM container with the 8.2 version of PHP.
- `db` which is the MySQL database container with a **MySQL 8.0** image.

## Installation

1. 😀 Clone this repo.

2. Create the file `./.docker/.env.nginx.local` using `./.docker/.env.nginx` as template. The value of the variable `NGINX_BACKEND_DOMAIN` is the `server_name` used in NGINX.

3. Run `docker compose up -d` to start containers.

4. You should work inside the `php` container. So if the containers are running you should run the following command
    ```
   docker compose exec php composer install
   ```

5. Inside the `php` container, run `composer install` to install dependencies from `/var/www/symfony` folder.

6. Use the following value for the DATABASE_URL environment variable:

```
DATABASE_URL=mysql://cv_user:letstalk@db:3306/cv_db?serverVersion=8.0.23
```

You could change the name, user and password of the database in the `env` file at the root of the project.

## Setting up the environment

1. Create the database and the necessary entities. Use the following command

```
docker compose exec  php  bin/console doctrine:migrations:migrate
```

2. Create user admin user
```
docker compose exec  php  bin/console create:user user@userdomain.com userpassword ROLE_ADMIN
```

3. Import or update currency exchange rates
```
docker compose exec  php  bin/console import:exchange-rate:currencies
```

4. Go to http://127.0.0.1/dashboard
