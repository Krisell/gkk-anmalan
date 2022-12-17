#!/bin/bash
php artisan route:clear

# Kill any already running server on specified port
PORT=65456
ALREADY_RUNNING_SERVERS=$(lsof -t -i:$PORT)
echo $ALREADY_RUNNING_SERVERS
if [ ! -z "$ALREADY_RUNNING_SERVERS" ]
then
    echo "Killing server"
    kill "$ALREADY_RUNNING_SERVERS"
    echo ' '
fi
