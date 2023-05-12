# Database Abstractions and Where They Leak

This collection of scripts was used in preparation for the talk and demonstrates the leaks.

## Usage

1. Make sure you have all the needed PHP extensions installed (see [composer.json](composer.json) for more details).
2. Install [Docker](https://www.docker.com/) and [Docker Compose](https://docs.docker.com/compose/),
   if you haven't already.
3. Run `composer install` to install the dependencies.
4. Run `docker-compose up` to start the databases services.
5. Run the scripts in the `bin/` directory to see how the different abstractions leak.

## References

1. [Database Abstractions and Where They Leak](https://joind.in/talk/f0e30) on [Joind.in](https://joind.in/).
