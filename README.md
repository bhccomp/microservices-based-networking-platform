# Microservices-Based Networking Platform

This project is created for a Dev Test during the Interview Process. Wish me luck. :) It is about Microservices-Based Networking Platform using Docker.

## Prerequisites

- Docker (Make sure it's installed on your system)

## Setup

1. Clone the repository:

\```bash
git clone https://github.com/bhccomp/microservices-based-networking-platform
\```

2. Navigate to the project directory:

\```bash
cd microservices-based-networking-platform
\```

3. Start the containers:

\```bash
docker-compose up --build
\```

## Services & Ports

After the succussful build, these will be the available services:

- **User Service**: `http://localhost:8001` 
- **Posts Service**: `http://localhost:8002`
- **Likes Service**: `http://localhost:8003`
- **Communication Service**: `http://localhost:8004`
- **Email Service**: `http://localhost:8005`
- **RabbitMQ Management Console**: `http://localhost:15672`
- **MailHog Web UI**: `http://localhost:8025`
- **Minio (Cloud Storage simulation)**: `http://localhost:9000`

## Api Documentation
- **User Service**: `http://localhost:8001/api/docs` 
- **Posts Service**: `http://localhost:8002/api/docs`
- **Likes Service**: `http://localhost:8003/api/docs`
- **Communication Service**: `http://localhost:8004/api/docs`

## Features

- **RabbitMQ**: Used as a messaging broker for the system. By default, two queues are started automatically by the Dockerfiles:
  - `messages` - used to queue messages for the communication controller 
  - `emails` - used to send emails when new user is created
  
  You can log in to `http://localhost:15672` with following username and password:
  - `user`
  - `password`

- **MailHog**: Email testing tool where you can see all the messages sent. In this particular case, emails will be sent when user is registered. You can log in at `http://localhost:8025` - no need to login.

- **Minio**: Object storage server. My idea is that Minio simulates Amazon S3 Bucket functionalities. A bucket named `profile-images` is created automatically during the setup and you can start uploading images at it right after the build.

- **MySQL Database**: Used for persistent storage for the services.

## Notes

- IMPORTANT: After the first build, comment out this line `bin/console doctrine:migrations:migrate --no-interaction` at each at these files:
  - `/communication-service/entrypoint.sh`
  - `/email-service/entrypoint.sh`
  - `/likes-service/entrypoint.sh`
  - `/posts-service/entrypoint.sh`
  - `/user-service/entrypoint.sh`
This is required only at the first startup so Symfony can do it's migrations and create required tables. I know I should have automated this as well but I completely forgot about it. :)

- For testing purposes, I created a new server/droplet at Digital Ocean, cloned and executed `docker-compose up --build` and everything works. You can access all these services by accessing this 164.92.161.30 instead of localhost, just make sure you're accessing services using the ports above.
- I am aware that I am missing logging and tests, this is something I would definitely work on if I had more time. 
