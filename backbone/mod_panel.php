<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function panel_respect() {
	if($GLOBALS['blog']['panels']['respect'] == false) { echo("\n"); return ""; }
	
	$out = "<h3>".ucfirst($GLOBALS['blog']['panels']['respect'])."</h3>\n    <ul>\n";
	$result = query("select * from `vblog_respect` order by rand() limit 7");
	$rn = @mysql_num_rows($result);
	if($rn == 0) {
		$out .= "     <li>There are no respect links available.</li>\n";
	} else {
		for($i = 0; $i < $rn; $i++) {
			$ra = @mysql_fetch_row($result);
			$temp[] = $ra;
		}
	}
	shuffle($temp);
	foreach($temp as $ra){
		$out .= "     <li><a href=\"".$GLOBALS['blog']['url']."resp/".(int)$ra[0]."/".format($ra[1],'url')."/\" title=\"".dequote(format($ra[3],'text'))."\"".((!empty($ra[4])) ? " rel=\"".format($ra[4],'text')."\"" : "").">".format($ra[1],'text')."</a></li>\n";
	}
	echo($out."    </ul>\n");
}
function panel_quotes() {
	if($GLOBALS['blog']['panels']['quotes'] == false) { echo("\n"); return ""; }
	
	$out = "<h3>".ucfirst($GLOBALS['blog']['panels']['quotes'])."</h3>\n    <ul>\n";
	$result = query("SELECT * FROM `vblog_quotes` ORDER BY RAND() LIMIT 1");
	$rn = @mysql_num_rows($result);
	if($rn !== 1) {
		$out .= "     <li>There are no quotes available.</li>\n";
	} else {
		$ra = @mysql_fetch_row($result);
		if(strlen($ra[2]) < 3) {
			$out .= "     <li id=\"quote\">".format($ra[1],'full')."</li>\n";
		} else {
			$out .= "     <li id=\"quote\">".format($ra[1],'full')."</li><li id=\"from\">".format($ra[2],'text')."</li>\n";
		}
	}
	$out .= "    </ul>\n";
	echo $out;
}
function panel_activity() {
	if($GLOBALS['blog']['panels']['activity'] == false) { echo("\n"); return ""; }
	
	$out = "<h3>".ucfirst($GLOBALS['blog']['panels']['activity'])."</h3>\n    <ul>\n";
	$result = query("SELECT `vblog_posts`.`post_id`,`vblog_posts`.`post_title`,`vblog_comments`.`com_id`,`vblog_comments`.`com_date`,`vblog_comments`.`com_author_id`,`vblog_comments`.`com_author_name`,`vblog_users`.`user_disp` FROM `vblog_comments` INNER JOIN (SELECT MAX(`com_id`) AS `com_id` FROM `vblog_comments` group by `post_id`) `ids` ON `vblog_comments`.`com_id`=`ids`.`com_id` LEFT JOIN `vblog_posts` ON `vblog_comments`.`post_id` = `vblog_posts`.`post_id` LEFT JOIN `vblog_users` ON `vblog_comments`.`com_author_id` = `vblog_users`.`user_id` group by `vblog_comments`.`post_id` ORDER BY `vblog_posts`.`post_date` DESC LIMIT ".$GLOBALS['blog']['recents']);
#	$result = query("SELECT `vblog_comments`.`post_id`, `vblog_posts`.`post_title`, `vblog_comments`.`com_id`, `vblog_comments`.`com_date`, `vblog_comments`.`com_author_id`,`vblog_comments`.`com_author_name`, `vblog_users`.`user_name` FROM `vblog_comments` LEFT JOIN `vblog_posts` ON `vblog_comments`.`post_id` = `vblog_posts`.`post_id` LEFT JOIN `vblog_users` ON `vblog_comments`.`com_author_id` = `vblog_users`.`user_id` GROUP BY `vblog_comments`.`post_id` ORDER BY `vblog_comments`.`com_date` DESC LIMIT ".$GLOBALS['blog']['recents']);
	$rn = @mysql_num_rows($result);
	if($rn == 0) {
		$out .= "     <li>There are no comments yet.</li>\n";
	} else {
		for($i = 0; $i < $rn; $i++) {
			$res_array = @mysql_fetch_row($result);
			if($res_array[6] == "") {
				$user = $res_array[5];
			} else {
				$user = $res_array[6];
			}
			$user = format($user,'text');
			$out .= "    <li class=\"activity\" title=\"Comment on since ".vdate($res_array[3])."\"><a href=\"".$GLOBALS['blog']['url']."post/".(int)$res_array[0]."/".format($res_array[1],'url')."/#comment_".(int)$res_array[2]."\"><span class=\"title\">".short(format($res_array[1],'text'),25)."</span> <span class=\"author\">Comment by ".$user."</span></a></li>\n";
		}
	}
	$out .= "    </ul>\n";
	echo $out;
}
function panel_stats() {
	if($GLOBALS['blog']['panels']['stats'] == false) { echo("\n"); return ""; }
	
	$out = "<h3>".ucfirst($GLOBALS['blog']['panels']['stats'])."</h3>\n    <ul>\n";
	
	global $stat;
	
	$vars = @get_object_vars($stat);
	$out .= "     <li>".(empty($vars['posts']) ? "No" : (int)$vars['posts'])." posts, ".(empty($vars['comments']) ? "no" : (int)$vars['comments'])." comments</li>\n";
	$out .= (empty($vars['dte']) ? '' : "     <li>".$vars['dte']."</li>\n");
	$out .= (!empty($vars['posts']) and !empty($vars['comments'])) ? "     <li>Average ".round($vars['comments']/$vars['posts'],1)." comments per post</li>\n" : "";
#	$result = query("SELECT COUNT(`post_id`) AS `posts`, ROUND((MAX(`post_date`)-MIN(`post_date`))/(3600*24)) AS `days`, ROUND(COUNT(`post_id`)/((MAX(`post_date`)-MIN(`post_date`))/(3600*24)),1) AS `avg` FROM `vblog_posts`");
	$result = query("SELECT ROUND(COUNT(`post_id`)/((MAX(`post_date`)-MIN(`post_date`))/(3600*24)),1) FROM `vblog_posts`");
	$out .= "     <li>Average daily ".@mysql_result($result,0)." posts</li>\n";
	$out .= "    </ul>\n";
	echo $out;
}
function panel_categ() {
	if($GLOBALS['blog']['panels']['categ'] == false) { echo("\n"); return ""; }
	
	$out = "<h3>".ucfirst($GLOBALS['blog']['panels']['categ'])."</h3>\n    <ul>\n";
	$result = query("SELECT DISTINCT `vblog_categ`.*,COUNT(`vblog_c2p`.`post_id`) AS `post_num` FROM `vblog_categ` LEFT JOIN `vblog_c2p` ON `vblog_categ`.`categ_id`=`vblog_c2p`.`categ_id` GROUP BY `vblog_categ`.`categ_name` ORDER BY `vblog_categ`.`categ_name` ASC");
	$rn = @mysql_num_rows($result);
	if($rn == 0) {
		$out .= "     <li>There are no categories yet.</li>\n";
	} else {
		for($i = 0; $i < $rn; $i++) {
			$res_array = @mysql_fetch_assoc($result);
			$cid = (int)$res_array['categ_id'];
			$cname = format($res_array['categ_name'],'text');
			$cdesc = format($res_array['categ_descript'],'text');
			$out .= "     <li><a href=\"".$GLOBALS['blog']['url']."rss/categ/".$cid."/".format($cname,'url')."/\" title=\"".ucfirst($cname)." RSS feed\" class=\"rss_small\"><span>".ucfirst($cname)." RSS feed</span></a> ";
			$out .= "<a href=\"".$GLOBALS['blog']['url']."categ/".$cid."/".format($cname,'url')."/\" title=\"".$cdesc."\">";
			$out .= ucfirst($cname);
			$out .= "</a> (".(int)$res_array['post_num'].") </li>\n";
		}
	}
	$out .= "    </ul>\n";
	
	echo($out);
}
function panel_archive() {
	if($GLOBALS['blog']['panels']['archive'] == false) { echo("\n"); return ""; }

	$out = "<h3>".$GLOBALS['blog']['panels']['archive']."</h3>\n    <ul>\n";
	$result = query("SELECT DISTINCT FROM_UNIXTIME(`post_date`, '%M') AS `archive_month`, FROM_UNIXTIME(`post_date`,'%Y%m') AS `archive_code`, FROM_UNIXTIME(`post_date`,'%Y') AS `archive_year`, FROM_UNIXTIME(`post_date`,'%m') AS `archive_month_code`, COUNT(`post_id`) AS `post_num` FROM `vblog_posts` GROUP BY `archive_code` ORDER BY `post_date` DESC LIMIT 10");
	$rn = @mysql_num_rows($result);
	if($rn == 0) {
		$out .= "     <li>There are no posts yet.</li>\n";
	} else {
		for($i = 0; $i < $rn; $i++) {
			$out .= "     <li><a href=\"".$GLOBALS['blog']['url']."archive/".mysql_result($result,$i,'archive_year')."/".mysql_result($result,$i,'archive_month_code')."/\">".mysql_result($result,$i,'archive_month')."</a> (".mysql_result($result,$i,'post_num')./*" posts*/")</li>\n";
		}
		$out .= "     <li><a href=\"".$GLOBALS['blog']['url']."archive/\">Older posts...</a></li>\n";
	}
	$out .= "    </ul>\n";
	echo $out;
}
function panel_ancient() {
	if($GLOBALS['blog']['panels']['ancient'] == false) { echo("\n"); return ""; }

	$st = "<h3>".ucfirst($GLOBALS['blog']['panels']['ancient'])."</h3>\n    <ul>\n";
/*	$result = query("SELECT `post_id`,`post_title`,`post_date` FROM `vblog_posts` ORDER BY RAND() LIMIT 1");
	if(@mysql_num_rows($result) != 1) {
		$out = "     <li>There are no posts yet.</li>\n";
	} else {
		$row = @mysql_fetch_row($result);
		$out = "     <li><a href=\"".$GLOBALS['blog']['url']."post/".$row[0]."/".format($row[1],'url')."/\">".format($row[1],'text')."</a></li>\n";
		$date = $row[2];
		$beat = round((time()-$date+7*3600)/86.4,1);
		$out .= "     <li><span title=\"".vdate($date)."\">@".$beat." ago</span></li>\n";
	}*/
	$mode = rand(0,4);
#	echo $mode;
	$query = "SELECT `vblog_posts`.`post_id`,`post_title`,`post_date`, count(`com_id`) as `cn` FROM `vblog_posts` LEFT JOIN `vblog_comments` ON `vblog_posts`.`post_id`=`vblog_comments`.`post_id` WHERE `post_date` < UNIX_TIMESTAMP() AND FROM_UNIXTIME(`post_date`,'%Y%m%d') = DATE_FORMAT(NOW()-INTERVAL ";
	switch($mode):
		case 0:
			$q = $query."2 YEAR,'%Y%m%d') group by `vblog_comments`.`post_id` ORDER BY `post_date` DESC";
			$result = query($q);
			$rn = @mysql_num_rows($result);
			if($rn >= 1) {
				$r = @mysql_fetch_assoc($result);
				$cr = ($rn > 1) ? rand(0,$rn-1) : 0;
				$out = "     <li><a title=\"".((int)$r['cn'] < 1 ? "No" : (int)$r['cn'])." comment".((int)$r['cn'] == 1 ? "" : "s")."\" href=\"".$GLOBALS['blog']['url']."posts/".(int)$r['post_id']."/".format($r['post_title'],'url')."/\">".$r['post_title']."</a></li>\n     <li class=\"date\">Two years ago</li>\n";
				break;
			}
		case 1:
			$q = $query."1 YEAR,'%Y%m%d') group by `vblog_comments`.`post_id` ORDER BY `post_date` DESC";
			$result = query($q);
			$rn = @mysql_num_rows($result);
			if($rn >= 1) {
				$r = @mysql_fetch_assoc($result);
				$cr = ($rn > 1) ? rand(0,$rn-1) : 0;
				$out = "     <li><a title=\"".((int)$r['cn'] < 1 ? "No" : (int)$r['cn'])." comment".((int)$r['cn'] == 1 ? "" : "s")."\" href=\"".$GLOBALS['blog']['url']."posts/".(int)$r['post_id']."/".format($r['post_title'],'url')."/\">".$r['post_title']."</a></li>\n     <li class=\"date\">A year ago</li>\n";
				break;
			}
		case 2:
			$q = $query."6 MONTH,'%Y%m%d') group by `vblog_comments`.`post_id` ORDER BY `post_date` DESC";
			$result = query($q); 
			$rn = @mysql_num_rows($result);
			if($rn >= 1) {
				$r = @mysql_fetch_assoc($result);
				$cr = ($rn > 1) ? rand(0,$rn-1) : 0;
				$out = "     <li><a title=\"".((int)$r['cn'] < 1 ? "No" : (int)$r['cn'])." comment".((int)$r['cn'] == 1 ? "" : "s")."\" href=\"".$GLOBALS['blog']['url']."posts/".(int)$r['post_id']."/".format($r['post_title'],'url')."/\">".$r['post_title']."</a></li>\n     <li class=\"date\">Half year ago</li>\n";
				break;
			}
		case 3:
			$q = $query."1 MONTH,'%Y%m%d') group by `vblog_comments`.`post_id` ORDER BY `post_date` DESC";
			$result = query($q); 
			$rn = @mysql_num_rows($result);
			if($rn >= 1) {
				$r = @mysql_fetch_assoc($result);
				$cr = ($rn > 1) ? rand(0,$rn-1) : 0;
				$out = "     <li><a title=\"".((int)$r['cn'] < 1 ? "No" : (int)$r['cn'])." comment".((int)$r['cn'] == 1 ? "" : "s")."\" href=\"".$GLOBALS['blog']['url']."posts/".(int)$r['post_id']."/".format($r['post_title'],'url')."/\">".$r['post_title']."</a></li>\n     <li class=\"date\">A month ago</li>";
				break;
			}
		case 4:
			$q = $query."1 WEEK,'%Y%m%d') group by `vblog_comments`.`post_id` ORDER BY `post_date` DESC";
			$result = query($q); 
			$rn = @mysql_num_rows($result);
			if($rn >= 1) {
				$r = @mysql_fetch_assoc($result);
				$cr = ($rn > 1) ? rand(0,$rn-1) : 0;
				$out = "     <li><a title=\"".((int)$r['cn'] < 1 ? "No" : (int)$r['cn'])." comment".((int)$r['cn'] == 1 ? "" : "s")."\" href=\"".$GLOBALS['blog']['url']."posts/".(int)$r['post_id']."/".format($r['post_title'],'url')."/\">".$r['post_title']."</a></li>\n     <li class=\"date\">A week ago</li>";
				break;
			}
		default:
			$result = query("select * from (SELECT `vblog_posts`.`post_id`,`post_title`,`post_date`, count(`com_id`) as `cn` FROM `vblog_posts` LEFT JOIN `vblog_comments` ON `vblog_posts`.`post_id`=`vblog_comments`.`post_id` group by `vblog_comments`.`post_id` ORDER BY `post_date` DESC LIMIT 10) as `temp` order by rand() limit 1");
			$rn = @mysql_num_rows($result);
			if($rn > 0) {
				$r = rand(0,$rn-1);
				$out = "     <li><a href=\"".$GLOBALS['blog']['url']."posts/".(int)mysql_result($result,$r,'post_id')."/".format(mysql_result($result,$r,'post_title'),'url')."/\">".mysql_result($result,$r,'post_title')."</a></li>\n     <li class=\"date\">Some time ago</li>\n";
			}
	endswitch;
	
	
	$out = $st.$out."\n    </ul>\n";
	
	echo($out);
}
function panel_languages() {
	if($GLOBALS['blog']['panels']['lang'] == false) { echo("\n"); return ""; }

	$out = "<h3>".ucfirst($GLOBALS['blog']['panels']['lang'])."</h3>\n    <ul>\n";
	$result = query("SELECT `vblog_languages`.*, COUNT(`vblog_posts`.`post_id`) FROM `vblog_languages` LEFT JOIN `vblog_posts` ON `vblog_languages`.`lang_id`=`vblog_posts`.`lang_id` GROUP BY `vblog_languages`.`lang_id` ORDER BY `vblog_languages`.`lang_id` ASC");
	$rn = @mysql_num_rows($result);
	if($rn == 0) {
		$out .= "     <li>There are no languages added yet.</li>\n";
	} else {
		for($i = 0; $i < $rn; $i++) {
			$row = @mysql_fetch_row($result);
			$out .= "     <li><a href=\"".$GLOBALS['blog']['url']."rss/lang/".$row[0]."/".format($row[1],'url')."/\" title=\"".format($row[2],'text')." RSS feed\" class=\"rss_small\"><span>".format($row[2],'text')." RSS feed</span></a> <a href=\"".$GLOBALS['blog']['url']."lang/".(int)$row[0]."/".format($row[1],'text')."/\" xml:lang=\"".format($row[1],'text')."\">".format($row[2],'text')."</a> (".(int)$row[4]./*" posts*/")</li>\n";
		}
	}
	$out .= "    </ul>\n";
	
	echo($out);	
}
function panel_feat() {
	if($GLOBALS['blog']['panels']['feat'] == false) { echo("\n"); return ""; }
	
	echo "<h3>".ucfirst($GLOBALS['blog']['panels']['feat'])."</h3>\n    <ul>\n";
	$result = query("SELECT `vblog_posts`.`post_id`,`post_title`, count(`com_id`) as `cn` FROM `vblog_posts` LEFT JOIN `vblog_comments` ON `vblog_posts`.`post_id`=`vblog_comments`.`post_id` left join `vblog_feat` on `vblog_posts`.`post_id`=`vblog_feat`.`post_id` where `vblog_feat`.`feat_date`=curdate() group by `vblog_posts`.`post_id` limit 1");
#echo mysql_error();
#	$result = query("select `post_id` from `vblog_feat` where `feat_date`=curdate() limit 1");
	if(($rn = @mysql_num_rows($result)) < 1) {
		query("insert into `vblog_feat` select `vblog_posts`.`post_id`,curdate() from `vblog_posts` left join `vblog_feat` on `vblog_posts`.`post_id` = `vblog_feat`.`post_id` group by `vblog_posts`.`post_id` having count(`vblog_feat`.`post_id`) = 0 order by rand() limit 1");
#echo mysql_error();
		$result = query("SELECT `vblog_posts`.`post_id`,`post_title`, count(`com_id`) as `cn` FROM `vblog_posts` LEFT JOIN `vblog_comments` ON `vblog_posts`.`post_id`=`vblog_comments`.`post_id` left join `vblog_feat` on `vblog_posts`.`post_id`=`vblog_feat`.`post_id` where `vblog_feat`.`feat_date`=curdate() group by `vblog_posts`.`post_id` limit 1");
#echo mysql_error();
		if(($rn = @mysql_num_rows($result)) < 1) {
			echo "     <li>Error.</li>\n    </ul>\n";
			return "";
		}
	}
	$r = @mysql_fetch_assoc($result);
	if($rn > 61) query('delete from `vblog_feat` limit 61,5');
	if(!$r) return "";
	echo "     <li><a title=\"".((int)$r['cn'] < 1 ? "No" : (int)$r['cn'])." comment".((int)$r['cn'] == 1 ? "" : "s")."\" href=\"".$GLOBALS['blog']['url']."posts/".(int)$r['post_id']."/".format($r['post_title'],'url')."/\">".$r['post_title']."</a></li>\n    </ul>\n";
}
function panel_search() {
	?><h3>Sniff</h3>
    <form action="<?=$GLOBALS['blog']['url'];?>search/" id="search">
    <ul>
     <li>
      <fieldset>
       <label for="search_phrase" class="hide">Search phrase:</label>
       <input id="search_phrase" name="search_phrase"
        size="10" value="search..." class="input_text"
        onkeydown="liveSearch('<?=$GLOBALS['layout']['dir'];?>');" onkeypress="liveSearch('<?=$GLOBALS['layout']['dir'];?>');" onkeyup="liveSearch('<?=$GLOBALS['layout']['dir'];?>');"
        onblur="fieldBlur();" onfocus="fieldFocus();" />
      </fieldset>
     </li>
    </ul>
    </form>
    <ul id="search_result">
     <li></li>
    </ul>
    <ul>
     <li><a href="<?=$GLOBALS['blog']['url'];?>search/" title="Advanced search">Advanced search</a></li>
    </ul>
<?php 
}
function panel_user() {
	if($GLOBALS['blog']['panels']['user'] == false) { echo("\n"); return ""; }
	
	$out = "<h3>".ucfirst($GLOBALS['blog']['panels']['user'])."</h3>\n";
	if(!loggedin()) {
		$out .= "    <form action=\"".$GLOBALS['blog']['url']."\" method=\"post\" onsubmit=\"return check('login');\">\n";
		$out .= "     <fieldset>\n";
		$out .= "      <ul>\n";
		if(isset($GLOBALS['logout'])) {
			$out .= "      <li>You are now logged out.</li>\n";
		}
		if(!empty($GLOBALS['login_msg'])) {
			$out .= "      <li>".$GLOBALS['login_msg']."</li>\n";
		}
		$out .= "       <li><label for=\"username\" class=\"hide\">Username:</label> <input id=\"username\" class=\"input_text\" name=\"username\" alt=\"Enter username\" value=\"username\" type=\"text\" size=\"15\" maxlength=\"64\" onblur=\"if(this.value=='') this.value='username';\" onfocus=\"if(this.value=='username') this.value='';\" /></li>\n";
		$out .= "       <li><label for=\"password\" class=\"hide\">Password:</label> <input id=\"password\" class=\"input_text\" name=\"password\" alt=\"Enter password\" value=\"password\" type=\"password\" size=\"15\" maxlength=\"64\" onblur=\"if(this.value=='') this.value='password';\" onfocus=\"if(this.value=='password') this.value='';\" /></li>\n";
		$out .= "       <li><label for=\"remember\">Remember me</label> <input id=\"remember\" name=\"remember\" alt=\"Remember me\" value=\"remember\" type=\"checkbox\" /></li>\n";
		$out .= "       <li><input id=\"login_submit\" name=\"login_submit\" class=\"submit\" alt=\"Log in!\" value=\"Log in!\" type=\"submit\" /></li>\n";
		$out .= "       <li>Or <a href=\"".$GLOBALS['blog']['url']."register/\">register</a></li>\n";
		$out .= "      </ul>\n";
		$out .= "     </fieldset>\n";
		$out .= "    </form>\n";
	} else {
		$out .= "    <ul>\n";
		$out .= "     <li>Welcome <strong>".get_data("name")."</strong></li>\n";
		if(loggedin(1)) {
			$out .= "     <li><a href=\"".$GLOBALS['blog']['url']."admin/\" title=\"Admin area\">Admin area</a></li>\n";
		}
		$out .= "     <li><a href=\"".$GLOBALS['blog']['url']."profile/self/edit/\" title=\"Edit your profile\">Edit your profile</a></li>\n";
		$out .= "     <li><a href=\"".$GLOBALS['blog']['url']."logout/\" title=\"Log out\">Log out</a></li>\n";
		$out .= "    </ul>\n";
	}
	echo $out;
}
function panel_layout() {
	if($GLOBALS['blog']['panels']['layout'] == false) { echo("\n"); return ""; }
	
	$out = "<h3>".ucfirst($GLOBALS['blog']['panels']['layout'])."</h3>\n    <ul>\n";
	if(!loggedin()) {
		$out .= "    <li>Only registered users can change layouts.</li>\n";
	} else {
		$result = query("SELECT `layout_id`,`layout_name` FROM `vblog_layouts` ORDER BY `layout_used` DESC");
		$rn = @mysql_num_rows($result);
		if($rn < 1) {
			$out .= "    <li>No layouts. Strange.</li>\n";
		} else {
			for($i = 0; $i < $rn; $i++) {
				$row = @mysql_fetch_row($result);
				$out .= "    <li><a href=\"".$GLOBALS['blog']['url']."setlayout/".$row[0]."/".format($row[1],'url')."/\">".$row[1]."</a></li>\n";
			}
		}
	}
	$out .= "    </ul>\n";
	echo $out;
}
function panel_sign() {
	if($GLOBALS['blog']['panels']['sign'] == false) { echo("\n"); return ""; }
	$out = "<h3>".ucfirst($GLOBALS['blog']['panels']['sign'])."</h3>\n    <ul>\n";
	
	$form = <<<EOD
    <script type="text/javascript">print_form();</script>
    <noscript>
     <li>Sorry, but JavaScript is required for this feature.</li>
    </noscript>
EOD;
?><?php
	if(!empty($_POST['sign_name'])) {
		$name = format($_POST['sign_name'],'db');
		if(empty($_POST['sign_mail']) or strlen($_POST['sign_mail']) < 6 or !strpos($_POST['sign_mail'],'@') or !strpos($_POST['sign_mail'],'.')) {
			$mail = "undefined";
		} else {
			$mail = format($_POST['sign_mail'],'db');
			$result = query("select `id` from `vblog_sign` where `mail`='".$mail."'");
			if(@mysql_num_rows($result) > 0) {
				$out .= "    <li>Sorry, this e-mail has already signed.</li>\n".$form;
				$off = true;
			}
		}
		if(empty($off)) {
			query("insert into `vblog_sign` values(0,'".$name."','".base64_encode($mail)."','".remote_ip()."',".vtime().")");
			$out .= "    <li>Thanks for signing!</li>\n";
		}	
	} else {
		$out .= $form;
	}
	$out .= "    </ul>\n";
	echo $out;
}
?>