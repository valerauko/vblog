<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function cat_list() {
	$out = "<h3>Categories in ".$GLOBALS['blog']['title']."</h3>\n<ul>";
	$result = query("SELECT DISTINCT `vblog_categ`.*,COUNT(`vblog_c2p`.`post_id`) AS `post_num` FROM `vblog_categ` LEFT JOIN `vblog_c2p` ON `vblog_categ`.`categ_id`=`vblog_c2p`.`categ_id` GROUP BY `vblog_categ`.`categ_name` ASC ORDER BY `post_num` DESC");
	$rn = @mysql_num_rows($result);
	if($rn == 0) {
		$out .= "<li>There are no categories in this blog.</li>\n";
	} else {
		for($i = 0; $i < $rn; $i++) {
			$row = mysql_fetch_assoc($result);
			$out .= "<li class=\"catlist\"><a href=\"".$GLOBALS['blog']['url']."categ/".(int)$row['categ_id']."/".format($row['categ_name'],'url')."\">".ucfirst($row['categ_name'])."</a> (".(int)$row['post_num']." posts)<br /><span>".format($row['categ_descript'],'text')."</span></li>\n";
		}
	}
	$out .= "</ul>\n";
	
	echo $out;
}
function cat_posts($categ_id,$page = 1) {
	$result = query("SELECT `categ_name` FROM `vblog_categ` WHERE `categ_id`=".(int)$categ_id." LIMIT 1");
	$rn = @mysql_num_rows($result);
	if($rn == 0) {
		echo("There is no such category.");
		return false;
	} else {
		$categ_name = format(mysql_result($result,0,'categ_name'),'text');
		$cname_url = format($categ_name,'url');
	}
	if((int)$page < 1) {
		$page = 1;
	}
	echo("<h3>Posts in ".ucfirst($categ_name)." category</h3>\n");
	$result = query("SELECT COUNT(*) AS `count` FROM `vblog_c2p` WHERE `categ_id`=".(int)$categ_id);
	$rn = @mysql_result($result,0,'count');
	if($rn == 0) {
		echo("<ul>\n<li>There are no posts in this category.</li>\n</ul>\n");
	} else {
		$div = (int)$GLOBALS['blog']['recents'];
		$pnum = (int)ceil($rn/$div);
		$offset = ((int)$page-1)*$div;
		if($offset > $rn) { $offset = $rn-$div; }
		$pager = pager($pnum,$page,$GLOBALS['blog']['url']."categ/".$categ_id."/".$cname_url."/");
		echo($pager);
		$query = "SELECT `vblog_posts`.*,COUNT(`vblog_comments`.`post_id`) AS `com_num`, `vblog_c2p`.* FROM `vblog_c2p` LEFT JOIN `vblog_posts` ON `vblog_c2p`.`post_id` = `vblog_posts`.`post_id` LEFT JOIN `vblog_comments` ON `vblog_comments`.`post_id`=`vblog_posts`.`post_id` WHERE `vblog_posts`.`post_status` = 'public' AND `vblog_c2p`.`categ_id`=".(int)$categ_id." GROUP BY `vblog_posts`.`post_date` ORDER BY `vblog_posts`.`post_date` DESC LIMIT ".$offset.",".$GLOBALS['blog']['recents'];
		$result = query($query);
		$rn = @mysql_num_rows($result);
		for($i = 0; $i < $rn; $i++) {
			$ra = mysql_fetch_array($result);
			display_post($ra);
		}
		echo($pager);
	}
}

?>