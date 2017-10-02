-- phpMyAdmin SQL Dump
-- version 4.6.0
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Oct 02, 2017 at 05:54 AM
-- Server version: 5.6.37
-- PHP Version: 7.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `floweradminv2`
--

-- --------------------------------------------------------

--
-- Table structure for table `mocms`
--

CREATE TABLE `mocms` (
  `setting` varchar(255) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mocms`
--

INSERT INTO `mocms` (`setting`, `value`) VALUES
('max_level', '3'),
('menu', '[{"key":"dashboard","name":"Home","icon_classes":"fa fa-book","strict":true,"level":1},{"key":"users","name":"Users","icon_classes":"fa fa-users","strict":false,"level":"3"},{"key":"permissions","name":"Permissions","icon_classes":"fa fa-universal-access","strict":false,"level":"3"},{"key":"logs","name":"Logs","icon_classes":"fa fa-list","strict":false,"level":"3"},{"key":"pages","name":"Pages","icon_classes":"fa fa-file-text","strict":false,"level":"2"}]'),
('version', '4');

-- --------------------------------------------------------

--
-- Table structure for table `mo_admins`
--

CREATE TABLE `mo_admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `login` varchar(100) NOT NULL,
  `email` varchar(99) DEFAULT NULL,
  `password` varchar(99) NOT NULL,
  `level` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `custom_access` text,
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_ip` varchar(40) DEFAULT '',
  `deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mo_logs`
--

CREATE TABLE `mo_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `module` varchar(99) DEFAULT NULL,
  `type` varchar(99) DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `date` datetime DEFAULT NULL,
  `ip` varchar(30) DEFAULT NULL,
  `info` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mo_multisite`
--

CREATE TABLE `mo_multisite` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mo_pages`
--

CREATE TABLE `mo_pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(160) DEFAULT NULL,
  `link` varchar(300) NOT NULL,
  `logged_in` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `text` text,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mo_users_auth`
--

CREATE TABLE `mo_users_auth` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(50) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mo_users_auth_attempts`
--

CREATE TABLE `mo_users_auth_attempts` (
  `ip` varchar(25) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attempts` smallint(3) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_languages`
--

CREATE TABLE `tm_languages` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `flag` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_links`
--

CREATE TABLE `tm_links` (
  `id` int(10) UNSIGNED NOT NULL,
  `link` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL,
  `main_link` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `position` tinyint(3) NOT NULL DEFAULT '0',
  `able` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `block` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `logged_in` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_strings`
--

CREATE TABLE `tm_strings` (
  `key` varchar(255) NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `russian` text,
  `english` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mocms`
--
ALTER TABLE `mocms`
  ADD UNIQUE KEY `setting` (`setting`);

--
-- Indexes for table `mo_admins`
--
ALTER TABLE `mo_admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mo_logs`
--
ALTER TABLE `mo_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mo_multisite`
--
ALTER TABLE `mo_multisite`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mo_pages`
--
ALTER TABLE `mo_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mo_users_auth`
--
ALTER TABLE `mo_users_auth`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `mo_users_auth_attempts`
--
ALTER TABLE `mo_users_auth_attempts`
  ADD UNIQUE KEY `ip` (`ip`);

--
-- Indexes for table `tm_languages`
--
ALTER TABLE `tm_languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tm_links`
--
ALTER TABLE `tm_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tm_strings`
--
ALTER TABLE `tm_strings`
  ADD PRIMARY KEY (`key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mo_admins`
--
ALTER TABLE `mo_admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `mo_logs`
--
ALTER TABLE `mo_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=577;
--
-- AUTO_INCREMENT for table `mo_multisite`
--
ALTER TABLE `mo_multisite`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `mo_pages`
--
ALTER TABLE `mo_pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `tm_languages`
--
ALTER TABLE `tm_languages`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `tm_links`
--
ALTER TABLE `tm_links`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;