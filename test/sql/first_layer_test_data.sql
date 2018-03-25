DROP TABLE IF EXISTS `co_data_nodes`;
CREATE TABLE `co_data_nodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `access_class` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `last_access` datetime NOT NULL,
  `read_security_id` bigint(20) DEFAULT NULL,
  `write_security_id` bigint(20) DEFAULT NULL,
  `object_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `access_class_context` blob,
  `owner` bigint(20) UNSIGNED DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `latitude` double DEFAULT NULL,
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
  `payload` longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `co_data_nodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `access_class` (`access_class`),
  ADD KEY `last_access` (`last_access`),
  ADD KEY `write_security_id` (`write_security_id`),
  ADD KEY `read_security_id` (`read_security_id`),
  ADD KEY `object_name` (`object_name`),
  ADD KEY `owner` (`owner`),
  ADD KEY `longitude` (`longitude`),
  ADD KEY `latitude` (`latitude`),
  ADD KEY `tag0` (`tag0`),
  ADD KEY `tag1` (`tag1`),
  ADD KEY `tag2` (`tag2`),
  ADD KEY `tag3` (`tag3`),
  ADD KEY `tag4` (`tag4`),
  ADD KEY `tag5` (`tag5`),
  ADD KEY `tag6` (`tag6`),
  ADD KEY `tag7` (`tag7`),
  ADD KEY `tag8` (`tag8`),
  ADD KEY `tag9` (`tag9`);
  
INSERT INTO `co_data_nodes` (`id`, `access_class`, `last_access`, `read_security_id`, `write_security_id`, `object_name`, `access_class_context`, `owner`, `longitude`, `latitude`, `tag0`, `tag1`, `tag2`, `tag3`, `tag4`, `tag5`, `tag6`, `tag7`, `tag8`, `tag9`, `payload`) VALUES
(1, 'CO_Main_DB_Record', '1970-01-01 00:00:00', -1, -1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'CO_LL_Location', '1970-01-01 00:00:00', NULL, NULL, 'Las Vegas', NULL, NULL, 36.1699, -115.1398, 'US', 'NV', 'nevada', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'CO_LL_Location', '1970-01-01 00:00:00', 3, 3, 'Area 51', NULL, NULL, 37.2343, -115.8067, 'alien', 'spooks', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'CO_LL_Location', '1970-01-01 00:00:00', 4, 4, 'NA World Services', NULL, NULL, 34.23592, -118.563659, 'NA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'CO_LL_Location', '1970-01-01 00:00:00', 5, 5, 'Dubai', NULL, NULL, 25.2048, 55.2708, 'Middle East', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'CO_LL_Location', '1970-01-01 00:00:00', 5, 5, 'Anchorage', NULL, NULL, 61.2181, -149.9003, 'US', 'AL', 'alaska', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'CO_LL_Location', '1970-01-01 00:00:00', 6, 6, 'Adelaide', NULL, NULL, -34.9285, 138.6007, 'AU', 'SA', 'south australia', 'australia', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'CO_LL_Location', '1970-01-01 00:00:00', 0, 3, 'San Jose, Costa Rica', NULL, NULL, 9.9281, -84.0907, 'CR', 'Costa Rica', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'CO_LL_Location', '1970-01-01 00:00:00', 0, 0, 'San Jose, California', NULL, NULL, 37.3382, -121.8863, 'US', 'CA', 'california', 'northern california', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'CO_LL_Location', '1970-01-01 00:00:00', 6, 6, 'Gettysburg, Pennsylvania', NULL, NULL, 39.8309, -77.2311, 'US', 'PA', 'pennsylvania', 'Lincoln', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

ALTER TABLE `co_data_nodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
