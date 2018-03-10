-- --------------------------------------------------------

--
-- Table structure for table `co_security_nodes`
--

CREATE TABLE `co_security_nodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `access_class` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `last_access` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `read_security_id` bigint(20) UNSIGNED DEFAULT NULL,
  `write_security_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ttl` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `login_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `access_class_context` blob,
  `ids` varchar(4095) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `co_security_nodes`
--

INSERT INTO `co_security_nodes` (`id`, `access_class`, `last_access`, `read_security_id`, `write_security_id`, `ttl`, `name`, `login_id`, `access_class_context`, `ids`) VALUES
(1, 'CO_Security_Login', '1970-01-01 00:00:00', NULL, NULL, NULL, 'Default Admin', 'admin', NULL, NULL);

--
-- Indexes for table `co_security_nodes`
--
ALTER TABLE `co_security_nodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `login_id_2` (`login_id`),
  ADD KEY `access_class` (`access_class`),
  ADD KEY `last_access` (`last_access`),
  ADD KEY `read_security_id` (`read_security_id`),
  ADD KEY `write_security_id` (`write_security_id`),
  ADD KEY `ttl` (`ttl`),
  ADD KEY `name` (`name`),
  ADD KEY `login_id` (`login_id`);

--
-- AUTO_INCREMENT for table `co_security_nodes`
--
ALTER TABLE `co_security_nodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;