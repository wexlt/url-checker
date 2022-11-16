# url-checker
Symfony url check task

## Installation

Clone repo.

```bash
git clone https://github.com/wexlt/url-checker.git
```

Run docker compose

```bash
docker-compose up -d --build
```

Login to container

```bash
docker-compose exec php /bin/bash
```

Install all packages

```bash
composer install
```

Created database for tests

```bash
php bin/console --env=test doctrine:database:create
```

Run migrations for both DB

```bash
php bin/console doctrine:migrations:migrate
php bin/console --env=test doctrine:migrations:migrate
```

Run Fixtures to create test user(needed for PHPunit tests

```bash
php bin/console --env=test doctrine:fixtures:load
```

Run php unit tests to check if system working fine

```bash
php bin/phpunit
```

## Usage

- Go to [http://localhost:8080](http://localhost:8080) press `Signup` and register a user
- Login
- Go to [http://localhost:8080/link](http://localhost:8080/link) create a links you wanna to check. `IMPORTANT`: keywords should be separated by comma
- To run link checker you can do it manually by running command
```bash
php bin/console app:check-links
```
- Or inside docker container start cron service which will run command every minute 
```bash
cron
```

## Additional info
- Users can see links that was created by other users. 
- Info about request is saved as string.
- Website shows last request log. All logs history saved on database.
