-- --------------------------------------------------------

--
-- Table structure for table `co_data_nodes`
--

CREATE TABLE `co_data_nodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `access_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `owner` bigint(20) NOT NULL,
  `read_security_id` bigint(20) NOT NULL,
  `write_security_id` bigint(20) NOT NULL,
  `last_access` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `access_class` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `access_class_context` blob,
  `tag0` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `tag1` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `tag2` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `tag3` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `tag4` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `tag5` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `tag6` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `tag7` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `tag8` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `tag9` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `ttl` time DEFAULT NULL,
  `payload` longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `co_data_nodes`
--
ALTER TABLE `co_data_nodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `access_key` (`access_key`),
  ADD KEY `owner` (`owner`),
  ADD KEY `last_access` (`last_access`),
  ADD KEY `write_security_id` (`write_security_id`),
  ADD KEY `read_security_id` (`read_security_id`),
  ADD KEY `access_class` (`access_class`),
  ADD KEY `tag0` (`tag0`),
  ADD KEY `tag1` (`tag1`),
  ADD KEY `tag2` (`tag2`),
  ADD KEY `tag3` (`tag3`),
  ADD KEY `tag4` (`tag4`),
  ADD KEY `tag5` (`tag5`),
  ADD KEY `tag6` (`tag6`),
  ADD KEY `tag7` (`tag7`),
  ADD KEY `tag8` (`tag8`),
  ADD KEY `tag9` (`tag9`),
  ADD KEY `ttl` (`ttl`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `co_data_nodes`
--
ALTER TABLE `co_data_nodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;