DROP TABLE IF EXISTS `co_security_nodes`;
CREATE TABLE `co_security_nodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `login_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `access_class` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `last_access` datetime NOT NULL,
  `read_security_id` bigint(20) DEFAULT NULL,
  `write_security_id` bigint(20) DEFAULT NULL,
  `object_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `access_class_context` mediumtext,
  `ids` varchar(4095) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `co_security_nodes` (`id`, `login_id`, `access_class`, `last_access`, `read_security_id`, `write_security_id`, `object_name`, `access_class_context`, `ids`) VALUES
(1, NULL, 'CO_Security_Node', '1970-01-01 00:00:00', -1, -1, NULL, NULL, NULL);

ALTER TABLE `co_security_nodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login_id` (`login_id`),
  ADD KEY `access_class` (`access_class`),
  ADD KEY `last_access` (`last_access`),
  ADD KEY `read_security_id` (`read_security_id`),
  ADD KEY `write_security_id` (`write_security_id`),
  ADD KEY `object_name` (`object_name`);

ALTER TABLE `co_security_nodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;