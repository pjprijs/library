
CREATE TABLE `avi` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` tinytext NOT NULL,
  `order` tinyint(3) UNSIGNED NOT NULL,
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `avi` (`id`, `name`, `order`, `active`) VALUES
(1, 'Onbekend', 1, 1),
(2, 'Start', 2, 1),
(3, 'M3', 3, 1),
(4, 'E3', 4, 1),
(5, 'M4', 5, 1),
(6, 'E4', 6, 1),
(7, 'M5', 7, 1),
(8, 'E5', 8, 1),
(9, 'M6', 9, 1),
(10, 'E6', 10, 1),
(11, 'M7', 11, 1),
(12, 'E7', 12, 1),
(13, 'Plus', 13, 1),
(14, 'Prentenboek', 0, 1);
