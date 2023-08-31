#!/bin/sh

# Download and set permissions for the rabbitmqadmin tool
curl -O http://rabbitmq:15672/cli/rabbitmqadmin
chmod +x rabbitmqadmin

# Let's give RabbitMQ some time to start before attempting to create queues
sleep 20

# Fetch the list of queues
QUEUES=$('./rabbitmqadmin' list queues name)

# Conditionally declare the queues if they don't already exist
if echo "$QUEUES" | grep -qv "messages"; then
    ./rabbitmqadmin declare queue name=messages durable=true
fi

if echo "$QUEUES" | grep -qv "emails"; then
    ./rabbitmqadmin declare queue name=emails durable=true
fi

echo "Queues set up successfully!"
