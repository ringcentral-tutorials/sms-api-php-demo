# SMS Application Walk-through

Welcome to the SMS Application Walk-through and tour of a fully functional SMS application powered by RingCentral. In this walk through you will learn:

- How to send an SMS message
- How to send an MMS message
- How to track delivery status of messages
- How to modify the message's read status.
- How to delete a message.
- How to receive and reply to SMS messages

### Clone - Setup - Run the project
```
$ git clone https://github.com/ringcentral-tutorials/sms-api-php-demo
$ cd sms-api-php-demo
$ cp .env.sample .env
$ curl -sS https://getcomposer.org/installer | php
$ php composer.phar install
```
Specify your app client id and client secret as well as account login credentials to the .env file.

#### How to send SMS
```
$ php send-sms.php
```
#### How to send MMS
```
$ php send-mms.php
```
#### How to retrieve and modify message status
```
$ php retrieve-modify.php
```
#### How to delete a message
```
$ php retrieve-delete.php
```
#### How to receive and reply to SMS messages
```
$ php receive-reply.php
```

## RingCentral PHP SDK
The SDK is available at https://github.com/ringcentral/ringcentral-php
