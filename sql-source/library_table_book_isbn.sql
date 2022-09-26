
CREATE TABLE `book_isbn` (
  `id` bigint(20) NOT NULL,
  `book` bigint(20) UNSIGNED NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `edition` varchar(16) NOT NULL,
  `manual` tinyint(3) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
