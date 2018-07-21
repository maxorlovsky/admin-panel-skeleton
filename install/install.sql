-- phpMyAdmin SQL Dump
-- version 4.6.0
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Nov 15, 2017 at 08:07 AM
-- Server version: 5.6.37
-- PHP Version: 7.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `cms`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`max`@`localhost` PROCEDURE `cleanupCmsData` ()  MODIFIES SQL DATA
    SQL SECURITY INVOKER
BEGIN
    DECLARE v_delete_limit INT DEFAULT 1000;
    DECLARE v_row_count INT DEFAULT 0;
    
    SELECT 'Cleaning CMS User Auth...';
    REPEAT
        -- SELECT '.';
        START TRANSACTION;
        DELETE FROM tm_user_auth
            WHERE `timestamp` < DATE_SUB( NOW(), INTERVAL 1 MONTH )
            ORDER BY `id`
            LIMIT v_delete_limit;
        SET v_row_count = ROW_COUNT();
        COMMIT;
        
        SELECT CONCAT( '... deleted ', v_row_count );
    UNTIL v_row_count < v_delete_limit
    END REPEAT;
END$$

DELIMITER ;

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
('menu', '[{"key":"dashboard","name":"Home","iconClasses":"fa fa-book","strict":true,"level":1,"subCategories":[]},{"key":"users","name":"Users","iconClasses":"fa fa-users","strict":false,"level":"3","subCategories":[]},{"key":"permissions","name":"Permissions","iconClasses":"fa fa-universal-access","strict":false,"level":"3","subCategories":[]},{"key":"logs","name":"Logs","iconClasses":"fa fa-list","strict":false,"level":"3","subCategories":[]},{"key":"labels","name":"Labels","iconClasses":"fa fa-list-alt","strict":false,"level":1,"subCategories":[]},{"key":"pages","name":"Pages","iconClasses":"fa fa-file-text","strict":false,"level":"2","subCategories":[]}]');

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

--
-- Dumping data for table `mo_admins`
--

INSERT INTO `mo_admins` (`id`, `login`, `email`, `password`, `level`, `custom_access`, `last_login`, `last_ip`, `deleted`) VALUES
(1, 'admin', '', '$2y$10$KEav.eSLlb2dcxDdeNLda.udY4.atgLuQBUgL8RPMrNxJRVG2/0O2', 5, '["dashboard"]', '2017-11-15 09:06:39', '127.0.0.1', 0);

-- --------------------------------------------------------

--
-- Table structure for table `mo_labels`
--

CREATE TABLE `mo_labels` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `output` text,
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
-- Indexes for table `mo_labels`
--
ALTER TABLE `mo_labels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mo_admins`
--
ALTER TABLE `mo_admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `mo_labels`
--
ALTER TABLE `mo_labels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mo_logs`
--
ALTER TABLE `mo_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mo_multisite`
--
ALTER TABLE `mo_multisite`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mo_pages`
--
ALTER TABLE `mo_pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;