
CREATE TABLE `book` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `avi` tinyint(3) UNSIGNED DEFAULT 1,
  `amount` tinyint(3) UNSIGNED NOT NULL,
  `published_date` varchar(16) NOT NULL,
  `pagecount` smallint(5) UNSIGNED NOT NULL,
  `printtype` varchar(16) NOT NULL DEFAULT '',
  `language` varchar(16) NOT NULL,
  `description` text NOT NULL,
  `md5hash` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
