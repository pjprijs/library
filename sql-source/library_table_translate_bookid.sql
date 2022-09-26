
CREATE TABLE `translate_bookid` (
  `old_book` int(11) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `avi_level` varchar(20) DEFAULT NULL,
  `book` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
