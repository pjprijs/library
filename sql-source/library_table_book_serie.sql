
CREATE TABLE `book_serie` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `book` bigint(20) UNSIGNED NOT NULL,
  `serie` bigint(20) UNSIGNED NOT NULL,
  `serie_nr` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
