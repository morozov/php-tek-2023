DROP DATABASE IF EXISTS `php-tek-2023`;
CREATE DATABASE `php-tek-2023`;

USE `php-tek-2023`;

CREATE TABLE `messages` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `subject` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO messages (user_id, subject) VALUES (1, 'Hello, php[tek] 2023!');
