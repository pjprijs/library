
CREATE TABLE `user_schoolyear` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user` bigint(20) UNSIGNED NOT NULL,
  `schoolyear` tinyint(3) UNSIGNED NOT NULL,
  `group` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
