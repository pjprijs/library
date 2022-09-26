
CREATE TABLE `book_author` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `book` bigint(20) UNSIGNED NOT NULL,
  `author` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
