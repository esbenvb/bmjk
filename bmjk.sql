CREATE TABLE `card` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sha1` varchar(40) DEFAULT NULL,
  `message` text,
  `recipient_email` varchar(255) DEFAULT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `sender_email` varchar(255) DEFAULT NULL,
  `sender_ip` varchar(15) DEFAULT NULL,
  `created` bigint(20) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `opened` bigint(20) unsigned DEFAULT NULL,
  `opened_ip` varchar(15) DEFAULT NULL,
  `mail_sent` tinyint(1) DEFAULT '0',
  `notification_sent` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sha1` (`sha1`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;