-- phpMyAdmin SQL Dump
-- version 4.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Dec 16, 2015 at 01:00 PM
-- Server version: 5.6.24
-- PHP Version: 5.6.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `themagescms`
--

CREATE TABLE IF NOT EXISTS `themagescms` (
  `setting` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_admins`
--

CREATE TABLE IF NOT EXISTS `tm_admins` (
  `id` int(10) unsigned NOT NULL,
  `login` varchar(255) NOT NULL,
  `email` varchar(99) DEFAULT NULL,
  `password` varchar(99) NOT NULL,
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `custom_access` text,
  `last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `login_count` int(10) unsigned NOT NULL DEFAULT '0',
  `last_ip` varchar(40) NOT NULL DEFAULT '',
  `language` char(2) NOT NULL DEFAULT 'en',
  `editRedirect` tinyint(1) unsigned NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_api_request`
--

CREATE TABLE IF NOT EXISTS `tm_api_request` (
  `id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip` varchar(20) NOT NULL,
  `request_data` text,
  `response_data` longtext,
  `call_time` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_countrys`
--

CREATE TABLE IF NOT EXISTS `tm_countrys` (
  `id` int(10) unsigned NOT NULL,
  `value` varchar(99) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=194 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_languages`
--

CREATE TABLE IF NOT EXISTS `tm_languages` (
  `id` smallint(5) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `flag` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_links`
--

CREATE TABLE IF NOT EXISTS `tm_links` (
  `id` int(10) unsigned NOT NULL,
  `link` varchar(255) NOT NULL DEFAULT '',
  `value` varchar(255) NOT NULL,
  `main_link` int(10) unsigned NOT NULL DEFAULT '0',
  `position` tinyint(3) NOT NULL DEFAULT '0',
  `able` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `block` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `logged_in` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_logs`
--

CREATE TABLE IF NOT EXISTS `tm_logs` (
  `id` int(10) unsigned NOT NULL,
  `module` varchar(99) DEFAULT NULL,
  `type` varchar(99) DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date` datetime DEFAULT NULL,
  `ip` varchar(30) DEFAULT NULL,
  `info` varchar(500) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_modules`
--

CREATE TABLE IF NOT EXISTS `tm_modules` (
  `name` varchar(255) NOT NULL,
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `added_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_pages`
--

CREATE TABLE IF NOT EXISTS `tm_pages` (
  `id` int(10) unsigned NOT NULL,
  `link` varchar(99) NOT NULL,
  `value` varchar(99) NOT NULL,
  `logged_in` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `text_russian` text,
  `text_english` text
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_settings`
--

CREATE TABLE IF NOT EXISTS `tm_settings` (
  `setting` varchar(255) NOT NULL,
  `value` varchar(1000) NOT NULL DEFAULT '',
  `field` varchar(99) DEFAULT NULL,
  `type` enum('text','checkbox','level') NOT NULL DEFAULT 'text',
  `position` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_strings`
--

CREATE TABLE IF NOT EXISTS `tm_strings` (
  `key` varchar(255) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `russian` text,
  `english` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_user_auth`
--

CREATE TABLE IF NOT EXISTS `tm_user_auth` (
  `user_id` int(10) unsigned NOT NULL,
  `token` varchar(50) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tm_user_auth_attempts`
--

CREATE TABLE IF NOT EXISTS `tm_user_auth_attempts` (
  `ip` varchar(25) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attempts` smallint(3) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `themagescms`
--
ALTER TABLE `themagescms`
  ADD UNIQUE KEY `setting` (`setting`);

--
-- Indexes for table `tm_admins`
--
ALTER TABLE `tm_admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tm_api_request`
--
ALTER TABLE `tm_api_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tm_countrys`
--
ALTER TABLE `tm_countrys`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `tm_logs`
--
ALTER TABLE `tm_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tm_modules`
--
ALTER TABLE `tm_modules`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `tm_pages`
--
ALTER TABLE `tm_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tm_settings`
--
ALTER TABLE `tm_settings`
  ADD UNIQUE KEY `setting` (`setting`);

--
-- Indexes for table `tm_strings`
--
ALTER TABLE `tm_strings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `tm_user_auth`
--
ALTER TABLE `tm_user_auth`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `tm_user_auth_attempts`
--
ALTER TABLE `tm_user_auth_attempts`
  ADD UNIQUE KEY `ip` (`ip`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tm_admins`
--
ALTER TABLE `tm_admins`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `tm_api_request`
--
ALTER TABLE `tm_api_request`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `tm_countrys`
--
ALTER TABLE `tm_countrys`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=194;
--
-- AUTO_INCREMENT for table `tm_languages`
--
ALTER TABLE `tm_languages`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `tm_links`
--
ALTER TABLE `tm_links`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `tm_logs`
--
ALTER TABLE `tm_logs`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `tm_pages`
--
ALTER TABLE `tm_pages`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;

--
-- Dumping data for table `themagescms`
--

INSERT INTO `themagescms` (`setting`, `value`) VALUES
('max_level', '4'),
('version', '3.15');

--
-- Dumping data for table `tm_countrys`
--

INSERT INTO `tm_countrys` (`id`, `value`) VALUES
(1, 'Afghanistan'),
(2, 'Albania'),
(3, 'Algeria'),
(4, 'Andorra'),
(5, 'Angola'),
(6, 'Antigua and Barbuda'),
(7, 'Argentina'),
(8, 'Armenia'),
(9, 'Australia'),
(10, 'Austria'),
(11, 'Azerbaijan'),
(12, 'Bahamas'),
(13, 'Bahrain'),
(14, 'Bangladesh'),
(15, 'Barbados'),
(16, 'Belarus'),
(17, 'Belgium'),
(18, 'Belize'),
(19, 'Benin'),
(20, 'Bhutan'),
(21, 'Bolivia'),
(22, 'Bosnia and Herzegovina'),
(23, 'Botswana'),
(24, 'Brazil'),
(25, 'Brunei'),
(26, 'Bulgaria'),
(27, 'Burkina Faso'),
(28, 'Burundi'),
(29, 'Cambodia'),
(30, 'Cameroon'),
(31, 'Canada'),
(32, 'Cape Verde'),
(33, 'Central African Republic'),
(34, 'Chad'),
(35, 'Chile'),
(36, 'China'),
(37, 'Colombi'),
(38, 'Comoros'),
(39, 'Congo (Brazzaville)'),
(40, 'Congo'),
(41, 'Costa Rica'),
(42, 'Cote d''Ivoire'),
(43, 'Croatia'),
(44, 'Cuba'),
(45, 'Cyprus'),
(46, 'Czech Republic'),
(47, 'Denmark'),
(48, 'Djibouti'),
(49, 'Dominica'),
(50, 'Dominican Republic'),
(51, 'East Timor (Timor Timur)'),
(52, 'Ecuador'),
(53, 'Egypt'),
(54, 'El Salvador'),
(55, 'Equatorial Guinea'),
(56, 'Eritrea'),
(57, 'Estonia'),
(58, 'Ethiopia'),
(59, 'Fiji'),
(60, 'Finland'),
(61, 'France'),
(62, 'Gabon'),
(63, 'Gambia, The'),
(64, 'Georgia'),
(65, 'Germany'),
(66, 'Ghana'),
(67, 'Greece'),
(68, 'Grenada'),
(69, 'Guatemala'),
(70, 'Guinea'),
(71, 'Guinea-Bissau'),
(72, 'Guyana'),
(73, 'Haiti'),
(74, 'Honduras'),
(75, 'Hungary'),
(76, 'Iceland'),
(77, 'India'),
(78, 'Indonesia'),
(79, 'Iran'),
(80, 'Iraq'),
(81, 'Ireland'),
(82, 'Israel'),
(83, 'Italy'),
(84, 'Jamaica'),
(85, 'Japan'),
(86, 'Jordan'),
(87, 'Kazakhstan'),
(88, 'Kenya'),
(89, 'Kiribati'),
(90, 'Korea, North'),
(91, 'Korea, South'),
(92, 'Kuwait'),
(93, 'Kyrgyzstan'),
(94, 'Laos'),
(95, 'Latvia'),
(96, 'Lebanon'),
(97, 'Lesotho'),
(98, 'Liberia'),
(99, 'Libya'),
(100, 'Liechtenstein'),
(101, 'Lithuania'),
(102, 'Luxembourg'),
(103, 'Macedonia'),
(104, 'Madagascar'),
(105, 'Malawi'),
(106, 'Malaysia'),
(107, 'Maldives'),
(108, 'Mali'),
(109, 'Malta'),
(110, 'Marshall Islands'),
(111, 'Mauritania'),
(112, 'Mauritius'),
(113, 'Mexico'),
(114, 'Micronesia'),
(115, 'Moldova'),
(116, 'Monaco'),
(117, 'Mongolia'),
(118, 'Morocco'),
(119, 'Mozambique'),
(120, 'Myanmar'),
(121, 'Namibia'),
(122, 'Nauru'),
(123, 'Nepa'),
(124, 'Netherlands'),
(125, 'New Zealand'),
(126, 'Nicaragua'),
(127, 'Niger'),
(128, 'Nigeria'),
(129, 'Norway'),
(130, 'Oman'),
(131, 'Pakistan'),
(132, 'Palau'),
(133, 'Panama'),
(134, 'Papua New Guinea'),
(135, 'Paraguay'),
(136, 'Peru'),
(137, 'Philippines'),
(138, 'Poland'),
(139, 'Portugal'),
(140, 'Qatar'),
(141, 'Romania'),
(142, 'Russia'),
(143, 'Rwanda'),
(144, 'Saint Kitts and Nevis'),
(145, 'Saint Lucia'),
(146, 'Saint Vincent'),
(147, 'Samoa'),
(148, 'San Marino'),
(149, 'Sao Tome and Principe'),
(150, 'Saudi Arabia'),
(151, 'Senegal'),
(152, 'Serbia and Montenegro'),
(153, 'Seychelles'),
(154, 'Sierra Leone'),
(155, 'Singapore'),
(156, 'Slovakia'),
(157, 'Slovenia'),
(158, 'Solomon Islands'),
(159, 'Somalia'),
(160, 'South Africa'),
(161, 'Spain'),
(162, 'Sri Lanka'),
(163, 'Sudan'),
(164, 'Suriname'),
(165, 'Swaziland'),
(166, 'Sweden'),
(167, 'Switzerland'),
(168, 'Syria'),
(169, 'Taiwan'),
(170, 'Tajikistan'),
(171, 'Tanzania'),
(172, 'Thailand'),
(173, 'Togo'),
(174, 'Tonga'),
(175, 'Trinidad and Tobago'),
(176, 'Tunisia'),
(177, 'Turkey'),
(178, 'Turkmenistan'),
(179, 'Tuvalu'),
(180, 'Uganda'),
(181, 'Ukraine'),
(182, 'United Arab Emirates'),
(183, 'United Kingdom'),
(184, 'United States'),
(185, 'Uruguay'),
(186, 'Uzbekistan'),
(187, 'Vanuatu'),
(188, 'Vatican City'),
(189, 'Venezuela'),
(190, 'Vietnam'),
(191, 'Yemen'),
(192, 'Zambia'),
(193, 'Zimbabwe');

--
-- Dumping data for table `tm_languages`
--

INSERT INTO `tm_languages` (`id`, `title`, `flag`) VALUES
(1, 'russian', 'ru'),
(2, 'english', 'en');

--
-- Dumping data for table `tm_settings`
--

INSERT INTO `tm_settings` (`setting`, `value`, `field`, `type`, `position`) VALUES
('accounts', '4', NULL, 'level', 0),
('dashboard', '1', NULL, 'level', 0),
('https', '0', 'HTTPS always', 'checkbox', 9),
('languages', '4', NULL, 'level', 0),
('links', '1', NULL, 'level', 0),
('logs', '1', NULL, 'level', 0),
('maintenance', '0', 'Maintenance mode', 'checkbox', 9),
('pages', '1', NULL, 'level', 0),
('settings', '1', NULL, 'level', 0),
('site_description', 'TheMages CMS', NULL, 'text', 0),
('site_keywords', 'cms, themages', NULL, 'text', 0),
('site_name', 'TheMages', NULL, 'text', 0),
('strings', '1', NULL, 'level', 0);

--
-- Dumping data for table `tm_strings`
--

INSERT INTO `tm_strings` (`key`, `status`, `russian`, `english`) VALUES
('about_us', 0, 'О насgfdgf', 'About us'),
('about_you', 0, 'О вас', 'About you'),
('web-link-111', 1, 'mfxhgm', 'gtcu,'),
('web-link-121212', 1, '121212', ''),
('web-link-222', 1, 'sub', 'sub'),
('web-link-444', 1, '444', '444'),
('web-link-555', 1, '', ''),
('web-link-666', 1, '', ''),
('web-link-777', 1, '', ''),
('web-link-888', 1, '888', '888'),
('web-link-999', 1, '999', '999'),
('web-page-fdf', 1, '', ''),
('web-page-Monkeys', 1, 'alert(''xss'');', 'Potatoes');