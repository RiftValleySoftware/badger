DROP TABLE IF EXISTS `co_security_nodes`;
CREATE TABLE `co_security_nodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `login_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `access_class` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `last_access` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `read_security_id` bigint(20) DEFAULT NULL,
  `write_security_id` bigint(20) DEFAULT NULL,
  `ttl` bigint(20) UNSIGNED DEFAULT NULL,
  `object_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `access_class_context` mediumtext,
  `ids` varchar(4095) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `co_security_nodes` (`id`, `login_id`, `access_class`, `last_access`, `read_security_id`, `write_security_id`, `ttl`, `object_name`, `access_class_context`, `ids`) VALUES
(1, NULL, 'CO_Security_Node', '1970-01-01 00:00:00', -1, -1, NULL, NULL, NULL, NULL),
(2, 'admin', 'CO_Security_Login', '1970-01-01 00:00:00', 2, 2, NULL, 'Default Admin', 'a:1:{s:15:\"hashed_password\";s:4:\"JUNK\";}', NULL),
(3, 'secondary', 'CO_Security_Login', '1970-01-01 00:00:00', 3, 3, NULL, 'Secondary Login', 'a:1:{s:15:\"hashed_password\";s:13:\"CodYOzPtwxb4A\";}', '5,6'),
(4, 'tertiary', 'CO_Security_Login', '1970-01-01 00:00:00', 4, 4, NULL, 'Tertiary Login', 'a:1:{s:15:\"hashed_password\";s:13:\"CodYOzPtwxb4A\";}', '7'),
(5, 'billybob', 'CO_Security_Login', '1970-01-01 00:00:00', 4, 4, NULL, 'Billy Bob\'s Login', 'a:1:{s:15:\"hashed_password\";s:13:\"CodYOzPtwxb4A\";}', '2,6,7'),
(6, NULL, 'CO_Security_ID', '1970-01-01 00:00:00', -1, -1, NULL, 'Security ID 4', '', NULL),
(7, NULL, 'CO_Security_ID', '1970-01-01 00:00:00', -1, -1, NULL, 'Security ID 5', '', NULL);

ALTER TABLE `co_security_nodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login_id` (`login_id`),
  ADD KEY `access_class` (`access_class`),
  ADD KEY `last_access` (`last_access`),
  ADD KEY `read_security_id` (`read_security_id`),
  ADD KEY `write_security_id` (`write_security_id`),
  ADD KEY `ttl` (`ttl`),
  ADD KEY `object_name` (`object_name`);

ALTER TABLE `co_security_nodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;