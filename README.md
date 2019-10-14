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
docker-compose up -d

docker-compose exec phpfpm composer install
docker-compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction

docker run -v ${PWD}:/app itkdev/yarn:latest install
docker run -v ${PWD}:/app itkdev/yarn:latest encore dev
```

Open the site in your default browser:

```bash
open http://$(docker-compose port nginx 80)
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
- Yarn 1.17.3 or above.

Distribute the app to a place where the NGINX can serve it from. 

Create a .env.local file where you set the following variables:
```ini
APP_ENV=prod
APP_ENV=some-very-secret-string-which-is-not-the-same-as-in-.env

DATABASE_URL=mysql://user:pass@url:port/database
DATABASE_SERVER_VERSION='mariadb-10.3.17'

MAILER_URL=smtp://url:port
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

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/itkdev/datatidy/tags). 

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
