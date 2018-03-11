-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 11, 2018 at 06:22 PM
-- Server version: 5.6.35
-- PHP Version: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `co_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `co_data_nodes`
--

DROP TABLE IF EXISTS `co_data_nodes`;
CREATE TABLE `co_data_nodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `access_class` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `last_access` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `read_security_id` bigint(20) UNSIGNED DEFAULT NULL,
  `write_security_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ttl` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
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

--
-- Dumping data for table `co_data_nodes`
--

INSERT INTO `co_data_nodes` (`id`, `access_class`, `last_access`, `read_security_id`, `write_security_id`, `ttl`, `name`, `access_class_context`, `owner`, `longitude`, `latitude`, `tag0`, `tag1`, `tag2`, `tag3`, `tag4`, `tag5`, `tag6`, `tag7`, `tag8`, `tag9`, `payload`) VALUES
(1, 'CO_LL_Location', NULL, NULL, NULL, NULL, 'Las Vegas', NULL, NULL, 36.1699, -115.1398, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'CO_LL_Location', NULL, 2, 2, NULL, 'Area 51', NULL, NULL, 37.2343, -115.8067, 'alien', 'spooks', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'CO_LL_Location', '2018-03-11 18:22:16', 3, 3, NULL, 'NA World Services', NULL, NULL, 34.23592, -118.563659, 'NA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `co_data_nodes`
--
ALTER TABLE `co_data_nodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `access_class` (`access_class`),
  ADD KEY `last_access` (`last_access`),
  ADD KEY `write_security_id` (`write_security_id`),
  ADD KEY `read_security_id` (`read_security_id`),
  ADD KEY `ttl` (`ttl`),
  ADD KEY `name` (`name`),
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

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `co_data_nodes`
--
ALTER TABLE `co_data_nodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;