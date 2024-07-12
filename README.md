# Introduction

This service has the ability to manage channels and providers through a database,
each channel has its own providers through which the channel is able to deliver notifications.
So f.e. Email channel can deliver message via AWS SES and SendGrid providers.
If one of the providers is unavailable, the notification service will use the other until the message is sent.
If all providers will be unavailable so NotificationJob will be failed
and retried with progressive delay (NotificationJob class, backoff method), 
this is achievable because notification service utilizes queue.

The service contains "/api/send" endpoint for delivering message.

Send endpoint has the ability to deliver one message through several different channels;
for this, the code channels must be listed in the incoming request.
Each channel and provider can be enabled/disabled via DB.

This service was tested with Twilio Provider on real account.
AWS SES provider was tested via feature/unit tests (AWS SES requires to have real domain to test via aws console).
Other providers weren't tested since lack of time.

# Installation

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
