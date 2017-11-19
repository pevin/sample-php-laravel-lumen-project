#!/bin/bash

sudo /etc/init.d/nginx stop

sudo /etc/init.d/php7.0-fpm stop

sudo /usr/local/bin/supervisorctl stop es_index:*
