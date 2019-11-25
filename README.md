# Datatidy

Datatidy is a data wrangler application based on the Symfony 4 framework. It can take one or more datasources from public APIs, make some transformations and deliver the result to a datastore.

## Getting started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

- [Docker](https://docs.docker.com/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Installing

```bash
docker-compose pull
docker-compose up --detach

docker-compose exec phpfpm composer install
docker-compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction

docker run -v ${PWD}:/app itkdev/yarn:latest install
docker run -v ${PWD}:/app itkdev/yarn:latest encore dev
```

Create a user:

```bash
docker-compose exec phpfpm bin/console fos:user:create

# Super admin user
docker-compose exec phpfpm bin/console fos:user:create --super-admin
```

Open the site in your default browser:

```bash
open http://$(docker-compose port nginx 80)
```

#### Jobs

Start the queue consumer:

```bash
docker-compose exec phpfpm bin/console messenger:consume async
```

Produce some jobs:

```bash
docker-compose exec phpfpm bin/console datatidy:data-flow:produce-jobs
```

## Running the tests

```bash
docker-compose exec phpfpm bin/phpunit
```

## Deployment

You will need an environment where the following is present:

- PHP 7.3
- Composer 1.9 or above.
- MariaDB 10.3.17.
- NGINX ([Config example](.docker/vhost.conf))
- Redis 5 or above.
- Yarn 1.17.3 or above.

Distribute the app to a place where NGINX can serve it from.

Create a .env.local file where you set the following variables:
```ini
APP_ENV=prod
APP_SECRET=some-very-secret-string-which-is-not-the-same-as-in-.env

SITE_URL=some-url.com
SITE_NAME=Name

DEFAULT_LOCALE=da

DATABASE_URL=mysql://user:pass@url:port/database
DATABASE_SERVER_VERSION='mariadb-10.3.17'

MAILER_URL=smtp://url:port
MAILER_FROM_EMAIL=info@example.com
MAILER_FROM_NAME=Info

MESSENGER_TRANSPORT_DSN=redis://url:port/messages
```

Install the dependencies and build the assets:

```bash
# Install the dependencies
composer install --no-dev
yarn install --production

# Build the assets
yarn build

# Create the database and run the migrations
php bin/console doctrine:database:create --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction
```

Want more? See the [official Symfony 4.3 documentation](https://symfony.com/doc/4.3/deployment.html) section about deployment.

### Jobs

#### Consumer

In order to have jobs processed the queue consumer has to be running. You probably want something to watch that the process is running all the time, and take an action if it doesn't. You could use [Supervisor](http://supervisor.org) as this something with the following settings added:

```ini
[datatidy:consumer]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/env php path/to/datatidy/bin/console consume async
autostart=true
autorestart=true
numprocs=1
redirect_stderr=true
stdout_logfile=path/to/output.file
```

#### Producer

You'll need to run the producer every minute to create jobs the consumer can process. You could for example use cron with the following settings to run the producer every minute:

```crontab
* * * * * /usr/bin/env php path/to/datatidy/bin/console datatidy:data-flow:produce-jobs > path/to/output.file
```

#### Handling long running jobs

Sometimes and for different reasons a job may run for a long time. And because jobs only can be created if there is no other active jobs for a DataFlow, you need to set those jobs in a non-active state.
To help you accomplish this a command is available:
```crontab
*/30 * * * * /usr/bin/env php /path/to/datatidy/bin/console datatidy:data-flow:timeout-jobs --timeout-threshold=30 > path/to/output.file
```

## Documentation

Documentation is kept in the [doc](doc) folder.

## Contributing

Before opening a Pull Request, make sure that our coding standards are followed:

```bash
# PHP
# Check to see if any violations is found:
docker-compose exec phpfpm composer check-coding-standards
docker-compose exec phpfpm vendor/bin/phan --allow-polyfill-parser

# You can see if the tools can fix them for you:
docker-compose exec phpfpm composer apply-coding-standards

# Twig
# Only checks for violations.
docker-compose exec phpfpm composer check-coding-standards/twigcs

# CSS, SCSS and JS
docker run -v ${PWD}:/app itkdev/yarn:latest check-coding-standards
docker run -v ${PWD}:/app itkdev/yarn:latest apply-coding-standards
```

### Pull Request Process

1. Update the README.md with details of changes that are relevant.
2. You may merge the Pull Request in once you have the sign-off of one other developer, or if you
   do not have permission to do that, you may request the reviewer to merge it for you.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/itk-dev/datatidy/tags).

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Testing

### Loading fixtures

```sh
docker-compose exec phpfpm bin/console hautelook:fixtures:load --purge-with-truncate --no-interaction
```

### Running a flow

The `datatidy:data-flow:run` console command can run a data flow by name or id:

```sh
docker-compose exec phpfpm bin/console datatidy:data-flow:run --help
```
