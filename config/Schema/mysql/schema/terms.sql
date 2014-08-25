CREATE TABLE `terms` (
`id` INTEGER(10) NOT NULL AUTO_INCREMENT,
`vocabulary_id` INTEGER(11) NOT NULL,
`lft` INTEGER(11) NOT NULL,
`rght` INTEGER(11) NOT NULL,
`parent_id` INTEGER(11) NOT NULL,
`name` VARCHAR(255) NOT NULL,
`slug` VARCHAR(255) NOT NULL,
`created` DATETIME NOT NULL,
`modified` DATETIME NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB