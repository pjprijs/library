
CREATE TABLE `group` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `name` tinytext NOT NULL,
  `order` tinyint(3) UNSIGNED NOT NULL,
  `active` tinyint(3) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `group` (`id`, `name`, `order`, `active`) VALUES
(1, '1/2 Rood', 0, 1),
(2, '1/2 Geel', 1, 1),
(3, '1/2 Blauw', 2, 1),
(4, '2/3', 3, 0),
(5, '3/4', 5, 0),
(6, '4/5', 7, 1),
(7, '5/6', 9, 0),
(8, '6/7', 11, 0),
(9, '7/8', 13, 0),
(15, '3', 4, 1),
(16, '4', 6, 1),
(17, '5', 8, 1),
(18, '6', 10, 1),
(19, '7', 12, 1),
(20, '8', 14, 1);
