DROP TABLE IF EXISTS `pm`;
CREATE TABLE IF NOT EXISTS `pm` (
  `ID` bigint(20) unsigned NOT NULL auto_increment,
  `type` enum('page','blog') NOT NULL default 'page',
  `author` varchar(100) NOT NULL default 'admin',
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `content` longtext NOT NULL,
  `title` text NOT NULL,
  `status` enum('draft','private','public') NOT NULL default 'draft',
  `parent` bigint(20) unsigned NOT NULL default '0',
  `order` int(11) NOT NULL default '0',
  `hidden` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;
