
CREATE TABLE `loan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `book` bigint(20) UNSIGNED NOT NULL,
  `user` bigint(20) UNSIGNED NOT NULL,
  `startdate` date NOT NULL,
  `enddate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
