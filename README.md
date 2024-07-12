# Instruction

### Clone the repository and navigate to the project directory
- git clone <repository-url>
- cd notification-service

### Create the environment file
cp .env.example .env

### Start Docker containers
docker-compose up

### Configure application
- docker-compose exec app php artisan migrate
- docker-compose exec app php artisan db:seed

### Run tests
- docker-compose exec app php artisan test
