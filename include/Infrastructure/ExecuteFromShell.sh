#!/bin/bash

# Define the path to the autoload file
AUTOLOAD="'/var/www/html/vendor/autoload.php'"

# Function to execute PHP command with autoload
execute_php() {
    local command=$1
    php -r "require $AUTOLOAD; argv = $@; $command"
}
