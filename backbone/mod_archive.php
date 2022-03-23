<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net"); }

function arch_posts($year,$month = 0,$page = 1) {
	if(((int)$year < 2000 or (int)$year > date("Y")+1 or (int)$month < 1 or (int)$month > 12) and (int)$month !== 0) {
		echo("<h3>Posts in selected period</h3>\n<ul>\n<li>There are no posts available with the given parameters.</li>\n</ul>\n");
		return "";
	}
	if((int)$page < 1) {
		$page = 1;
	}
	$offset = ((int)$page-1)*(int)$GLOBALS['blog']['recents'];
	$query = "SELECT COUNT(*) as `count` FROM `vblog_posts` WHERE `post_status`='public' AND FROM_UNIXTIME(`vblog_posts`.`post_date`,'%Y')=".(int)$year;
	if($month != 0) {
		if((int)$month < 10) {
			$month = "0".$month;
		}
		$query .= " AND FROM_UNIXTIME(`post_date`,'%m')=".$month;
	}
	$result = query($query);
	$brn = @mysql_result($result,0,'count');
	if($offset > $brn) {
		$offset = $brn-(int)$GLOBALS['blog']['recents'];
	}
	
	$query = "SELECT `vblog_posts`.*,COUNT(`vblog_comments`.`post_id`) AS `com_num`,FROM_UNIXTIME(`vblog_posts`.`post_date`,";
	if($month != 0) {
		$query .= "'%M, %Y')";
	} else {
		$query .= "'%Y')";
	}
	$query .= " AS `selected` FROM `vblog_posts` LEFT JOIN `vblog_comments` ON `vblog_comments`.`post_id`=`vblog_posts`.`post_id` WHERE `post_status`='public' AND FROM_UNIXTIME(`vblog_posts`.`post_date`,'%Y')=".(int)$year;
	if($month != 0) {
		$query .= " AND FROM_UNIXTIME(`post_date`,'%m')=".$month;
	}
	$query .= " GROUP BY `vblog_posts`.`post_date` ORDER BY `vblog_posts`.`post_date` ASC LIMIT ".$offset.",".(int)$GLOBALS['blog']['recents'];
#echo($query);
	$result = query($query);
	$rn = @mysql_num_rows($result);
	if($rn == 0) {
		echo("<h3>Posts in selected period</h3>\n<ul>\n<li>There are no posts in this period.</li>\n</ul>\n");
		return "";
	} else {
		$divider = (int)$GLOBALS['blog']['recents'];
		$pnum = (int)ceil($brn/$divider);
		$pm = ($month != 0) ? $month."/" : "";
		$pager = pager($pnum,$page,$GLOBALS['blog']['url']."archive/".$year."/".$pm);
		for($i = 0; $i < $rn; $i++) {
			$ra = @mysql_fetch_array($result);
			if($i == 0) {
				echo("    <h3>Posts in ".$ra['selected']."</h3>\n");
				echo($pager);
			}
			display_post($ra);
		}
		echo($pager);
	}
}
function arch_list() {
	$out = "<h3>Archive months in ".$GLOBALS['blog']['title']."</h3>\n";
	$result = query("SELECT DISTINCT FROM_UNIXTIME(`post_date`,'%Y') AS `archive_year`, FROM_UNIXTIME(`post_date`,'%Y%m') AS `archive_code`, FROM_UNIXTIME(`post_date`, '%M') AS `archive_month`, FROM_UNIXTIME(`post_date`,'%m') AS `archive_month_code`, COUNT(`post_id`) AS `post_num` FROM `vblog_posts` GROUP BY `archive_code` ORDER BY `post_date` DESC");
	$rn = @mysql_num_rows($result);
	if($rn == 0) {
		$out .= "     There are no posts in this blog!\n";
	} else {
		for($i = 0; $i < $rn; $i++) {
			if($i == 0) {
				$out .= "     <h4>".mysql_result($result,$i,'archive_year')."</h4>\n     <ul>\n";
			} elseif(mysql_result($result,$i,'archive_year') != mysql_result($result,$i-1,'archive_year')) {
				$out .= "     </ul>\n     <h4>".mysql_result($result,$i,'archive_year')."</h4>\n     <ul>\n";
			}
			$out .= "      <li class=\"archlist\"><a href=\"".$GLOBALS['blog']['url']."archive/".mysql_result($result,$i,'archive_year')."/".mysql_result($result,$i,'archive_month_code')."/\">".mysql_result($result,$i,'archive_month')."</a> (".mysql_result($result,$i,'post_num')." posts)</li>\n";
		}
	}
	if(!strpos(substr($out,-10,10),"</ul>")) $out .= "     </ul>\n";
	echo $out;
}
?>