
CREATE TABLE `folders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `color` varchar(10) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `position_index` (`position`) USING BTREE
);

CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sourcesid` int(11) NOT NULL DEFAULT '0',
  `foldersid` int(11) NOT NULL DEFAULT '0',
  `hash` varchar(40) DEFAULT NULL,
  `guid` varchar(255) DEFAULT NULL,
  `linkurl` varchar(255) DEFAULT NULL,
  `imageurl` varchar(255) DEFAULT NULL,
  `enclosureurl` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `status` int(11) DEFAULT '0',
  `starred` int(11) DEFAULT '0',
  `author` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `pubdate` datetime DEFAULT NULL,
  `flags` varchar(255) DEFAULT NULL,
  `raw` text,
  PRIMARY KEY (`id`),
  KEY `pubdate_idx` (`pubdate`),
  KEY `guid_idx` (`guid`),
  KEY `feedid_idx` (`sourcesid`),
  KEY `title_idx` (`title`),
  KEY `unread_idx` (`status`),
  KEY `folderid_idx` (`foldersid`)
);


CREATE TABLE `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(2000) DEFAULT NULL,
  `linkurl` varchar(500) DEFAULT NULL,
  `faviconurl` varchar(500) DEFAULT NULL,
  `description` varchar(2000) DEFAULT NULL,
  `tags` varchar(1000) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `linkurl_UNIQUE` (`linkurl`),
  KEY `url` (`linkurl`)
);

CREATE TABLE `sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `sourceurl` varchar(255) NOT NULL DEFAULT '',
  `siteurl` varchar(255) DEFAULT NULL,
  `iconurl` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `lastupdate` datetime DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `private` int(11) NOT NULL DEFAULT '1',
  `active` int(11) NOT NULL DEFAULT '1',
  `foldersid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`sourceurl`),
  KEY `folder_index` (`foldersid`),
  KEY `position_index` (`position`)
)

