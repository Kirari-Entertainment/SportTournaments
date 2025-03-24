#!/bin/bash

if ! [ "$(id -u)" -eq "$(id -u 2>/dev/null)" ] || [ "$(id -u)" -ne 0 ]; then
    echo "Please run as root (using sudo)"
    exit 1
fi

read -p "Enter the command name: " COMMAND_NAME
CONTAINER_NAME="kirari-develop-php-main-backend-1"

echo "Creating /usr/bin/${COMMAND_NAME} file"
cat <<EOL > /usr/bin/${COMMAND_NAME}
#!/bin/bash

if ! docker info &> /dev/null; then
    echo "You do not have permission to run Docker. Please ensure you have the necessary permissions."
    exit 1
fi

docker exec ${CONTAINER_NAME} app "\$@"
EOL

chmod +x /usr/bin/${COMMAND_NAME}