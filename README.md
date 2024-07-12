# Instruction

### Clone the repository and navigate to the project directory
- `git clone git@github.com:DmitryDeveloper/notification-service.git`
- `cd notification-service`

### Create the environment file
`cp .env.example .env`

### Start Docker containers
`docker-compose up`

### Configure application
- `docker-compose exec app php artisan migrate`
- `docker-compose exec app php artisan db:seed` (this command will prepopulate DB with Channels and Providers)

### Run tests
- `docker-compose exec app php artisan test`
