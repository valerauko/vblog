<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function listlang($id,$page=1) {
	if((int)$page < 1) {
		$page = 1;
	}
	$offset = ((int)$page-1)*(int)$GLOBALS['blog']['recents'];

	$result = query("SELECT `vblog_posts`.*,COUNT(`vblog_comments`.`post_id`) AS `com_num`, `vblog_languages`.`lang_name` FROM `vblog_posts` LEFT JOIN `vblog_comments` ON `vblog_posts`.`post_id`=`vblog_comments`.`post_id` LEFT JOIN `vblog_languages` ON `vblog_posts`.`lang_id`=`vblog_languages`.`lang_id` WHERE `vblog_posts`.`lang_id`=".(int)$id." GROUP BY `vblog_posts`.`post_id` ORDER BY `vblog_posts`.`post_date` DESC");

	echo "sorry this feature's not available. only in the next version.";
}
?>