-- --------------------------------------------------------

--
-- Table structure for table `co_security_nodes`
--

CREATE TABLE `co_security_nodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `access_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `read_security_id` bigint(20) NOT NULL,
  `write_security_id` bigint(20) NOT NULL,
  `last_access` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `access_class` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `access_class_context` blob,
  `ttl` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `co_security_nodes`
--
ALTER TABLE `co_security_nodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `access_key` (`access_key`),
  ADD KEY `read_security_id` (`read_security_id`),
  ADD KEY `write_security_id` (`write_security_id`),
  ADD KEY `last_access` (`last_access`),
  ADD KEY `access_class` (`access_class`),
  ADD KEY `ttl` (`ttl`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `co_security_nodes`
--
ALTER TABLE `co_security_nodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
  
-- --------------------------------------------------------

--
-- Table structure for table `co_security_collection`
--

CREATE TABLE `co_security_collection` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `access_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `read_security_id` bigint(20) NOT NULL,
  `write_security_id` bigint(20) NOT NULL,
  `last_access` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `access_class` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `access_class_context` blob,
  `ttl` time DEFAULT NULL,
  `ids` varchar(4095) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `co_security_collection`
--
ALTER TABLE `co_security_collection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `access_key` (`access_key`),
  ADD KEY `read_security_id` (`read_security_id`),
  ADD KEY `write_security_id` (`write_security_id`),
  ADD KEY `last_access` (`last_access`),
  ADD KEY `access_class` (`access_class`),
  ADD KEY `ttl` (`ttl`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `co_security_collection`
--
ALTER TABLE `co_security_collection`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;