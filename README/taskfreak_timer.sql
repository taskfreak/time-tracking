-- phpMyAdmin SQL Dump
-- version 3.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 21, 2010 at 02:12 AM
-- Server version: 5.0.67
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `taskfreak_timer`
--

-- --------------------------------------------------------

--
-- Table structure for table `acl`
--

DROP TABLE IF EXISTS `acl`;
CREATE TABLE `acl` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(127) NOT NULL,
  `section` varchar(63) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `acl`
--

INSERT INTO `acl` VALUES(1, 'task_see_all', 'general');
INSERT INTO `acl` VALUES(2, 'admin_user', 'general');

-- --------------------------------------------------------

--
-- Table structure for table `acl_user`
--

DROP TABLE IF EXISTS `acl_user`;
CREATE TABLE `acl_user` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `acl_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`acl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `acl_user`
--

INSERT INTO `acl_user` VALUES(1, 1);
INSERT INTO `acl_user` VALUES(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `nickname` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(40) NOT NULL,
  `salt` varchar(40) NOT NULL,
  `auto_login` tinyint(1) unsigned NOT NULL,
  `time_zone` varchar(63) NOT NULL,
  `date_format_us` tinyint(1) NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  `expiration_date` date NOT NULL,
  `last_login_date` datetime NOT NULL,
  `last_login_address` varchar(60) NOT NULL,
  `last_change_date` datetime NOT NULL,
  `visits` int(10) unsigned NOT NULL,
  `bad_access` smallint(5) unsigned NOT NULL,
  `activation` varchar(16) NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `member`
--

INSERT INTO `member` VALUES(1, 'Administrator', '', 'admin', '', '12345678', 0, 'Asia/Bangkok', 0, '2010-05-21 01:43:21', '0000-00-00', '0000-00-00 00:00:00', '127.0.0.1', '0000-00-00 00:00:00', 0, 0, '', 1);
INSERT INTO `member` VALUES(2, 'Emilie', '', 'emilie', '', '12345678', 0, 'Europe/Paris', 0, '2010-05-21 01:44:17', '0000-00-00', '0000-00-00 00:00:00', '127.0.0.1', '0000-00-00 00:00:00', 0, 0, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `note` text NOT NULL,
  `priority` tinyint(3) unsigned NOT NULL default '5',
  `begin` date NOT NULL,
  `deadline` date NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `archived` tinyint(3) unsigned NOT NULL,
  `member_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `task`
--

INSERT INTO `task` VALUES(1, 'taskfreak : README first about this beta version', 'Please keep in mind that this is an early beta version. There are quite a few known bugs, a handful of unknown bugs.\r\n\r\nHere''s what you can do so far :\r\n- create, edit and archive tasks\r\n- start and stop task timers\r\n- report time spent on a task\r\n- edit your own profile\r\n- see other''s tasks (task manager only)\r\n- create and edit users (user admin only)', 5, '9999-00-00', '2010-05-22', 0, 0, 1);
INSERT INTO `task` VALUES(2, 'taskfreak : another quick notes about v0.2', 'There''s still some work to be done before TaskFreak Time Tracking goes stable.\r\n\r\nThis includes :\r\n- fix found bugs (obviously)\r\n- finish table prefix testing\r\n- add multi language support\r\n- Internet Explorer support (only tested on FireFox, Safari & Chrome)', 4, '9999-00-00', '9999-00-00', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `timer`
--

DROP TABLE IF EXISTS `timer`;
CREATE TABLE `timer` (
  `task_id` int(10) unsigned NOT NULL,
  `start` datetime NOT NULL,
  `stop` datetime NOT NULL,
  `spent` int(10) unsigned NOT NULL,
  `manual` tinyint(3) unsigned NOT NULL,
  KEY `task_id` (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `timer`
--
