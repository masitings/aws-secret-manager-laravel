# Laravel AWS Secret Manager
Integrate Laravel Application with Amazon Secret Manager. 

## Installation
You can install the package via composer : 
```bash
composer require masitings/aws-secret-manager
```
Publish Config:
```
php artisan vendor:publish --provider="Masitings\AwsSecretManager\SecretManagerProvider"
```

## Usage
This package will load and fetch the secrets from AWS Secret Manager in any configuration variable that exists in your project. Put your env value with this
```php
AWS_DEFAULT_REGION=ap-southeast-1
AWS_SECRETS_TAG_NAME=stage
AWS_SECRETS_TAG_VALUE=production
```
Change the `AWS_ACCESS_KEY_ID` and `AWS_SECRET_ACCESS_KEY` with your own credential and assign that credential with AWSSecretManager permissions. 

`AWS_SECRETS_TAG_NAME` and `AWS_SECRETS_TAG_VALUE` are used to pull down all the secrets that match the tag key/value.

In order to make it works, you need to match the Key with your dot array configuration. For example, we have `database.php` configuration like this :
```php
<?php

use Illuminate\Support\Str;

return [
    'default' => env('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            ]) : [],
        ],
    ],

];
```
The key in your AWS Secret Manager will be something like this :
```
database.connections.mysql.driver
database.connections.mysql.url
database.connections.mysql.host
database.connections.mysql.port
database.connections.mysql.database
database.connections.mysql.username
database.connections.mysql.password
```
After that you can put the value according to the value in your `.env` file or another.

### AWS Credentials
Since this package utilizes the PHP AWS SDK the following .env values are used or credentials set `~/.aws/credentials`.
```
AWS_ACCESS_KEY_ID
AWS_SECRET_ACCESS_KEY
```
[https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html)

