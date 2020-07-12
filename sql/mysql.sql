#
# Table structure for table `discuss_message`
#

CREATE TABLE `discuss_message` (
  `discuss_id` smallint(5) unsigned NOT NULL,
  `message_id` int(11) unsigned NOT NULL auto_increment,
  `uid` mediumint(8) unsigned NOT NULL,
  `uname` varchar(60) NOT NULL default '',
  `message` text NOT NULL,
  `timestamp` int(10) unsigned NOT NULL default '0',
  `color` varchar(8) NOT NULL,
  PRIMARY KEY (`discuss_id`,`message_id`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `discuss`
#

CREATE TABLE `discuss` (
  `discuss_id` smallint(5) unsigned NOT NULL auto_increment,
  `subject` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `open_time` int(10) unsigned NOT NULL,
  `close_time` int(10) unsigned NOT NULL,
  `closed` tinyint(1) NOT NULL,
  `access_key` varchar(10) NOT NULL,
  `key_term` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`discuss_id`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `discuss_attendee`
#

CREATE TABLE `discuss_attendee` (
  `attendee_id` mediumint(5) unsigned NOT NULL auto_increment,
  `discuss_id` smallint(5) unsigned NOT NULL default '0',
  `uid` mediumint(8) unsigned NOT NULL default '0',
  `uname` varchar(60) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  `updated` int(10) unsigned NOT NULL default '0',
  `status` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`attendee_id`)
) TYPE=MyISAM;