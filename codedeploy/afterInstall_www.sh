#!/bin/bash

# go to app directory
cd /var/www/views-api

# copy .env file from S3
if [ "$DEPLOYMENT_GROUP_NAME" == "views-api" ]
then
    aws s3 cp s3://codedeploy-us-west-2-views-env-dev/.env .env
fi

# run composer
COMPOSER_HOME=/var/cache/composer composer install

# run migrations
php artisan migrate

# generate Swagger UI
php artisan swagger-lume:generate
