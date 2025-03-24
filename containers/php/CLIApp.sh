#!/bin/bash

# Check if the correct number of arguments are provided
if [ "$#" -lt 1 ]; then
    echo "Usage: $0 <ModuleName> <Commands>"
    exit 1
fi

# Function to execute PHP command with autoload
php -r "require '/var/www/html/public/CLICommands.php';" -- "$@"
