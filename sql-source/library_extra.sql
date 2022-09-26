

ALTER TABLE `author`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `avi`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `book`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `md5hash` (`md5hash`),
  ADD KEY `title` (`title`,`subtitle`),
  ADD KEY `avi` (`avi`);

ALTER TABLE `book_author`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `book` (`book`,`author`),
  ADD KEY `author` (`author`);

ALTER TABLE `book_isbn`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `book` (`book`);

ALTER TABLE `book_serie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `book` (`book`,`serie`),
  ADD KEY `serie` (`serie`);

ALTER TABLE `group`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `loan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `book` (`book`,`user`,`enddate`),
  ADD KEY `fk_book` (`book`) USING BTREE,
  ADD KEY `fk_user` (`user`) USING BTREE;

ALTER TABLE `schoolyear`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `serie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `translate_avi`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `translate_bookid`
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `old_book` (`old_book`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user_schoolyear`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user` (`user`),
  ADD KEY `schoolyear` (`schoolyear`),
  ADD KEY `group` (`group`);


ALTER TABLE `author`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `avi`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

ALTER TABLE `book`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `book_author`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `book_isbn`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `book_serie`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `group`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

ALTER TABLE `loan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `schoolyear`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `serie`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `translate_avi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_schoolyear`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `book`
  ADD CONSTRAINT `book_ibfk_1` FOREIGN KEY (`avi`) REFERENCES `avi` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

ALTER TABLE `book_author`
  ADD CONSTRAINT `book_author_ibfk_1` FOREIGN KEY (`book`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `book_author_ibfk_2` FOREIGN KEY (`author`) REFERENCES `author` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `book_isbn`
  ADD CONSTRAINT `book_isbn_ibfk_1` FOREIGN KEY (`book`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `book_serie`
  ADD CONSTRAINT `book_serie_ibfk_2` FOREIGN KEY (`serie`) REFERENCES `serie` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `book_serie_ibfk_3` FOREIGN KEY (`book`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `loan`
  ADD CONSTRAINT `loan_ibfk_1` FOREIGN KEY (`book`) REFERENCES `book` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `loan_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `user_schoolyear`
  ADD CONSTRAINT `user_schoolyear_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_schoolyear_ibfk_2` FOREIGN KEY (`schoolyear`) REFERENCES `schoolyear` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_schoolyear_ibfk_3` FOREIGN KEY (`group`) REFERENCES `group` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;
