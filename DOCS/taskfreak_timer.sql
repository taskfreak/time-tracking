CREATE TABLE IF NOT EXISTS `acl` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(127) NOT NULL,
  `section` varchar(63) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `acl` VALUES(1, 'task_see_all', 'general');
INSERT INTO `acl` VALUES(2, 'admin_user', 'general');

CREATE TABLE IF NOT EXISTS `acl_user` (
  `user_id` mediumint(8) unsigned NOT NULL,
  `acl_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`acl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `acl_user` VALUES(1, 1);
INSERT INTO `acl_user` VALUES(1, 2);

CREATE TABLE IF NOT EXISTS `member` (
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

INSERT INTO `member` VALUES(1, 'Administrator', '', 'admin', '', '12345678', 0, 'Asia/Bangkok', 0, '2010-06-11 01:30:00', '0000-00-00', '0000-00-00 00:00:00', '127.0.0.1', '0000-00-00 00:00:00', 0, 0, '', 1);
INSERT INTO `member` VALUES(2, 'Emilie', '', 'emilie', '', '12345678', 0, 'Europe/Paris', 0, '2010-06-11 01:31:00', '0000-00-00', '0000-00-00 00:00:00', '127.0.0.1', '0000-00-00 00:00:00', 0, 0, '', 1);

CREATE TABLE IF NOT EXISTS `task` (
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

INSERT INTO `task` VALUES(1, 'taskfreak : README first about this beta version', 'Please keep in mind that this is a beta version. There are quite a few known bugs, a handful of unknown bugs.\r\n\r\nHere''s what you can do so far :\r\n- create, edit and archive tasks\r\n- start and stop task timers\r\n- report time spent on a task\r\n- edit your own profile\r\n- see other''s tasks (task manager only)\r\n- create and edit users (user admin only)\r\n\r\nMake sure you read the README file under the DOCS/ folder. There''s a few setup tips in there.', 5, '9999-00-00', '2010-06-11', 0, 0, 1);
INSERT INTO `task` VALUES(2, 'taskfreak : quick notes about TT 0.4', 'Here''s the list of known bugs at release time :\r\n- some errors might occur on dates depending on your time zone\r\n- order by start, stop, or spent doesn''t really make sense\r\n- very buggy under IE8, and simply unusable with older versions of IE\r\n\r\nPlease report found bugs here :\r\nhttp://forum.taskfreak.com/index.php?board=7.0', 4, '9999-00-00', '9999-00-00', 0, 0, 1);

CREATE TABLE IF NOT EXISTS `timer` (
  `task_id` int(10) unsigned NOT NULL,
  `start` datetime NOT NULL,
  `stop` datetime NOT NULL,
  `spent` int(10) unsigned NOT NULL,
  `manual` tinyint(3) unsigned NOT NULL,
  KEY `task_id` (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

