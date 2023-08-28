CREATE DATABASE user_service_db;
CREATE DATABASE posts_service_db;
CREATE DATABASE likes_service_db;
CREATE DATABASE communication_service_db;

CREATE USER 'user_service_user'@'%' IDENTIFIED BY 'user_service_password';
GRANT ALL PRIVILEGES ON user_service_db.* TO 'user_service_user'@'%';

CREATE USER 'posts_service_user'@'%' IDENTIFIED BY 'posts_service_password';
GRANT ALL PRIVILEGES ON posts_service_db.* TO 'posts_service_user'@'%';

CREATE USER 'likes_service_user'@'%' IDENTIFIED BY 'likes_service_password';
GRANT ALL PRIVILEGES ON likes_service_db.* TO 'likes_service_user'@'%';

CREATE USER 'communication_service_user'@'%' IDENTIFIED BY 'communication_password';
GRANT ALL PRIVILEGES ON communication_service_db.* TO 'communication_service_user'@'%';
