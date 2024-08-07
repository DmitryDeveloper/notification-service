# Introduction

This service has the ability to manage channels and providers through a database,
each channel has its own providers through which the channel is able to deliver notifications.
So f.e. Email channel can deliver message via AWS SES and SendGrid providers.
If one of the providers is unavailable, the notification service will use the other until the message is sent.
If all providers will be unavailable so NotificationJob will be failed
and retried with progressive delay (NotificationJob class, backoff method), 
this is achievable because notification service utilizes queue.

The service contains "/api/send" endpoint for delivering message.
This endpoint uses throttle middleware 300 requests per hour default configuration.
Throttling can be configured via env variables.

Send endpoint has the ability to deliver one message through several different channels;
for this, the code channels must be listed in the incoming request.
Each channel and provider can be enabled/disabled via DB.

During notification processing sender/recipient details are stored in notifications table.

#### Code flow explanation
1. NotificationController (accepts the request and calls the required service)
2. NotificationService (Get Notification aggregate for each passed in request channel, call NotificationJob for each aggregate)
3. NotificationJob (Process Notification)
4. Notification send() (Notification aggregate tries to send message via providers one by one)
5. Provider send() (Sending message via client)

The Notification aggregate manages the channel and its providers, and encapsulate the logic for sending messages.

# Installation

### Clone the repository and navigate to the project directory
- `git clone git@github.com:DmitryDeveloper/notification-service.git`
- `cd notification-service`

### Create the environment file
- `cp .env.example .env` 
- Update environment variables on your creds in `.env` (if you want to test on a real account):
```
TWILIO_SID=your-twilio-sid
TWILIO_AUTH_TOKEN=your-twilio-auth-token
TWILIO_PHONE_NUMBER=your-twilio-phone-number
```
- Update throttling configurations (by default 300 requests per hour):
```
THROTTLE_SEND_NOTIFICATIONS_REQUESTS_LIMIT=300
THROTTLE_SEND_NOTIFICATIONS_WINDOW_IN_MINUTES=60
```
### Start Docker containers
- `docker-compose up`

### Configure application
- `docker-compose exec app php artisan migrate`
- `docker-compose exec app php artisan db:seed` (this command will prepopulate DB with Channels and Providers)
- `php artisan queue:work` run queue worker to process background notification jobs

### Run tests
- `docker-compose exec app php artisan test`

# Usage

POST request http://localhost/api/send

json body example 1 (several channels):
```
{
    "channels": ["sms", "email", "push"],
    "sender_uuid": "123e4567-e89b-12d3-a456-426614174000",
    "recipient_uuid": "987e6543-b21a-32d1-c123-654321098765",
    "recipient_phone": "+48111111111",
    "recipient_email": "test@test.com",
    "recipient_device_token": "XXXXXXXXXXXXXXXXXXXXXX",
    "subject": "Test Subject",
    "message": "This is a test message."
}
```
json body example 2 (only sms channel):
```
{
    "channels": ["sms"],
    "sender_uuid": "123e4567-e89b-12d3-a456-426614174000",
    "recipient_uuid": "987e6543-b21a-32d1-c123-654321098765",
    "recipient_phone": "+48111111111",
    "subject": "Test Subject",
    "message": "This is a test message."
}
```
json body example 3 (only email channel):
```
{
    "channels": ["email"],
    "sender_uuid": "123e4567-e89b-12d3-a456-426614174000",
    "recipient_uuid": "987e6543-b21a-32d1-c123-654321098765",
    "recipient_email": "test@test.com",
    "subject": "Test Subject",
    "message": "This is a test message."
}
```
json body example 4 (only push channel):
```
{
    "channels": ["push"],
    "sender_uuid": "123e4567-e89b-12d3-a456-426614174000",
    "recipient_uuid": "987e6543-b21a-32d1-c123-654321098765",
    "recipient_device_token": "XXXXXXXXXXXXXXXXXXXXXX",
    "subject": "Test Subject",
    "message": "This is a test message."
}
```
The notification service responds with 200 status if the payload is valid
and then tries to send a message with the transmitted channels and configured providers using queue.
The notification service takes care of failover and retries.
