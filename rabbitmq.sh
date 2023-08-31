#!/bin/sh

# Wait for RabbitMQ container to be ready
sleep 10

# Declare the queue
rabbitmqadmin -H rabbitmq -u user -p password declare queue name=messages durable=true

# You can also set up exchanges and bindings as necessary
