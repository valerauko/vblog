CREATE TABLE IF NOT EXISTS `vblog_posts` (
`post_id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`post_date` int(32) NOT NULL,
`post_status` enum('public', 'sketch', 'scheduled') DEFAULT 'active' NOT NULL,
`post_commentable` enum('open', 'regged', 'locked') DEFAULT 'open' NOT NULL,
`post_title` text NOT NULL,
`post_main` text NOT NULL,
`post_long` text NOT NULL,
`post_avatar` varchar(64) default '',
`lang_id` int(8) default 1,
`file_id` int(8) default 0,
UNIQUE KEY `id` (`post_id`),
FULLTEXT KEY `title` (`post_title`),
FULLTEXT KEY `main` (`post_main`),
FULLTEXT KEY `long` (`post_long`)
) DEFAULT CHARSET utf8;

CREATE TABLE IF NOT EXISTS `vblog_c2p` (
`categ_id` int(8) NOT NULL,
`post_id` int(8) NOT NULL
);

CREATE TABLE IF NOT EXISTS `vblog_categ` (
`categ_id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`categ_name` varchar(64) NOT NULL,
`categ_descript` text(1024) NOT NULL,
UNIQUE `id`(`categ_id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `vblog_files` (
`file_id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`file_path` text(2048) NOT NULL,
`file_size` int(10) NOT NULL,
`file_mime` varchar(64) NOT NULL DEFAULT 'audio/mpeg',
`file_downloads` int(8) NOT NULL DEFAULT 0,
UNIQUE KEY `id`(`file_id`)
) DEFAULT CHARSET utf8;

CREATE TABLE IF NOT EXISTS `vblog_comments` (
`com_id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`com_date` int(32) NOT NULL,
`post_id` int(8) NOT NULL,
`com_text` text NOT NULL,
`com_author_id` int(8),
`com_author_name` varchar(32),
`com_author_mail` varchar(64),
`com_author_site` varchar(64),
`com_author_ip` varchar(32) NOT NULL,
UNIQUE KEY `id`(`com_id`),
FULLTEXT KEY `text`(`com_text`)
) DEFAULT CHARSET utf8;

CREATE TABLE IF NOT EXISTS `vblog_users` (
`user_id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`user_regdate` int(32) NOT NULL,
`user_name` varchar(32) NOT NULL,
`user_disp` varchar(32) NOT NULL,
`user_pass` varchar(128) NOT NULL,
`user_sign` text(512) NOT NULL,
`user_mail` varchar(128) NOT NULL,
`user_site` varchar(128) NOT NULL,
`user_group` enum('normal','admin') DEFAULT 'normal' NOT NULL,
`user_actkey` varchar(128) NOT NULL,
`user_active` tinyint(1) NOT NULL,
UNIQUE KEY `id`(`user_id`)
) DEFAULT CHARSET utf8;

CREATE TABLE IF NOT EXISTS `vblog_trackback` (
`tb_id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`tb_received` int(32) NOT NULL,
`tb_title` varchar(64) NOT NULL DEFAULT 'Untitled',
`tb_excerpt` varchar(255) NOT NULL DEFAULT 'This post has no text to display',
`tb_url` varchar(128) NOT NULL,
`tb_blog` varchar(32) NOT NULL DEFAULT 'Noname',
`post_id` int(8) NOT NULL,
UNIQUE KEY `id`(`tb_id`)
) DEFAULT CHARSET utf8;

CREATE TABLE IF NOT EXISTS `vblog_stats` (
`stat_id` varchar(16) NOT NULL PRIMARY KEY,
`stat_num` int(16) NOT NULL,
`stat_misc` text(1024) NOT NULL,
UNIQUE KEY `id`(`stat_id`)
) DEFAULT CHARSET utf8;

CREATE TABLE IF NOT EXISTS `vblog_antispam` (
`id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`key` varchar(255) NOT NULL,
UNIQUE KEY `id`(`af_id`),
FULLTEXT KEY `key`(`af_key`)
) DEFAULT CHARSET utf8;

CREATE TABLE IF NOT EXISTS `vblog_pages` (
`page_id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`page_content` text NOT NULL,
UNIQUE KEY `id`(`page_id`),
FULLTEXT KEY `content`(`page_content`)
) DEFAULT CHARSET utf8;

CREATE TABLE IF NOT EXISTS `vblog_links` (
`link_id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`link_title` varchar(32) NOT NULL,
`link_url` varchar(128) NOT NULL,
`link_hits` int(8) NOT NULL,
UNIQUE KEY `id`(`link_id`)
) DEFAULT CHARSET utf8;

CREATE TABLE IF NOT EXISTS `vblog_respect` (
`resp_id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`resp_name` varchar(32) NOT NULL,
`resp_url` varchar(128) NOT NULL,
`resp_desc` text(512) NOT NULL,
`resp_rel` varchar(32) NOT NULL,
`resp_hits` int(8) NOT NULL,
UNIQUE KEY `id`(`resp_id`)
) DEFAULT CHARSET utf8;

CREATE TABLE IF NOT EXISTS `vblog_quotes` (
`quot_id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`quot_text` varchar(255) NOT NULL,
`quot_src` varchar(32) NOT NULL,
UNIQUE KEY `id`(`quot_id`)
) DEFAULT CHARSET utf8;

CREATE TABLE IF NOT EXISTS `vblog_languages` (
`lang_id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`lang_code` varchar(16) NOT NULL,
`lang_name` varchar(32) NOT NULL,
`lang_encoding` varchar(32) NOT NULL,
UNIQUE `id`(`lang_id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `vblog_settings` (
`set_id` varchar(16) NOT NULL PRIMARY KEY,
`set_value` varchar(255) NOT NULL,
UNIQUE `id`(`set_id`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `vblog_antibrute` (
`time` int(32) NOT NULL,
`ip` varchar(64) NOT NULL,
`form` int(1) NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS `vblog_log` (
`time` int(32) NOT NULL,
`type` varchar(32) NOT NULL,
`value` varchar(128) NOT NULL
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `vblog_pings` (
`ping_id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`ping_url` varchar(255) NOT NULL,
`ping_name` varchar(32) NOT NULL,
`ping_mode` tinyint(1) NOT NULL DEFAULT 0,
`ping_ping` int(16) NOT NULL DEFAULT 1
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `vblog_layouts` (
`layout_id` int(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
`layout_name` varchar(32) NOT NULL,
`layout_dir` varchar(128) NOT NULL,
`layout_used` int(8) NOT NULL DEFAULT 1,
`layout_default` tinyint NOT NULL DEFAULT 0,
UNIQUE KEY `default(`layout_default`)
) DEFAULT CHARACTER SET utf8;

INSERT INTO `vblog_settings` VALUES('title','shadow arts blog');
INSERT INTO `vblog_settings` VALUES('url','http://tiny.webserveronline.com/~vale/blog/');
INSERT INTO `vblog_settings` VALUES('keywords','valerauko, vale, blackbird, studios, blog, webdesign, css, php, html, javascript, flash, network, anime, manga, personal, host, site, unlimited, space, bandwidth');
INSERT INTO `vblog_settings` VALUES('descript','Vale&#039;s private (or rather too public) blog');
INSERT INTO `vblog_settings` VALUES('recents','5');
INSERT INTO `vblog_settings` VALUES('activity','Buzz');
INSERT INTO `vblog_settings` VALUES('user','You');
INSERT INTO `vblog_settings` VALUES('quotes','Better');
INSERT INTO `vblog_settings` VALUES('ancient','Summon');
INSERT INTO `vblog_settings` VALUES('categ','Whatabouts');
INSERT INTO `vblog_settings` VALUES('respect','Blogball');
INSERT INTO `vblog_settings` VALUES('stats','Count');
INSERT INTO `vblog_settings` VALUES('lang','Babel');
INSERT INTO `vblog_settings` VALUES('archive','Dust');
INSERT INTO `vblog_settings` VALUES('layout','Looks');

INSERT INTO `vblog_stats`(`stat_id`,`stat_num`) VALUES(0,1);
INSERT INTO `vblog_stats`(`stat_id`,`stat_num`,`stat_misc`) VALUES(0,1,18);
INSERT INTO `vblog_stats`(`stat_id`,`stat_num`) VALUES(0,1);
INSERT INTO `vblog_stats`(`stat_id`,`stat_num`,`stat_misc`) VALUES(0,1,18);
INSERT INTO `vblog_stats`(`stat_id`,`stat_num`) VALUES(0,1);
INSERT INTO `vblog_stats`(`stat_misc`) VALUES('89.132.70.23||1176906984');

INSERT INTO `vblog_pings` VALUES(0,'http://ping.blogsearch.hu/','Blogsearch',1,1),(0,'http://rpc.pingomatic.com/','Ping-o-matic',1,1);

INSERT INTO `vblog_layouts` VALUES(0,'Blue theme','blue',1,1),(0,'Black theme','black',1,0);