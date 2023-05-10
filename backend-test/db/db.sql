create database waizly;
use waizly;

CREATE TABLE `auth` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `auth`
(`username`,
`password`)
VALUES
('ridwan',
'123');


CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_done` char(1) DEFAULT 'N',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

create database waizly_test;
use waizly_test;

CREATE TABLE `auth` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `auth`
(`username`,
`password`)
VALUES
('ridwan',
'123');

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_done` char(1) DEFAULT 'N',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;