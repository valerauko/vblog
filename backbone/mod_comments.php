<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function get_avatar($email,$name = "someone") {
	$md5 = md5($email);
	$def = "http://blog.valerauko.net/avatars/default.gif";
	$siz = "50";
	$url = "http://mkavatar.hu/avatar.php?email=".$md5."&amp;default=".$def."&amp;g=on";
	return "<img src=\"".$url."\" alt=\"Avatar of ".$name."\" class=\"comment_avy\" />";
}
function comment_cookie($mode = "k") {
	if(!empty($_COOKIE['vblog_guest'])) $content = $_COOKIE['vblog_guest'];
	elseif(!empty($_SESSION['vblog_guest'])) $content = $_SESSION['vblog_guest'];
	else return false;
	$content = @urldecode(decode_unicode_url($content));
	$content = @unserialize($content);
	if(!$content) return false;
	if(empty($content['name']) or empty($content['mail'])) return false;
	switch($mode){
		case 1:return $content['name'];break;
		case 2:return $content['mail'];break;
		case 3:return (empty($content['site']) ? "" : $content['site']);break;
		default:return $content;
	}
}
function get_comments($post,$ptitle,$mode = "ASC") {
	$mode = strtoupper($mode);
	if($mode != "DESC") { $mode = "ASC"; }
	$title = format($ptitle,'url');
	$result = query("SELECT `post_commentable` FROM `vblog_posts` WHERE `post_id`=".(int)$post);
	$commentable = @mysql_result($result,0);
	if(!$commentable) return false;
	$result = query("SELECT `vblog_comments`.*, `vblog_users`.`user_disp`, `vblog_users`.`user_mail`, `vblog_users`.`user_site` FROM `vblog_comments` LEFT JOIN `vblog_users` ON `vblog_comments`.`com_author_id`=`vblog_users`.`user_id` LEFT JOIN `vblog_posts` ON `vblog_comments`.`post_id`=`vblog_posts`.`post_id` WHERE `vblog_comments`.`post_id` = ".(int)$post." ORDER BY `vblog_comments`.`com_date` ".$mode);
	$falt = format($ptitle,'text')." comments RSS 2.0 feed";
	echo("\n    <h4 id=\"comments\"><a href=\"".$GLOBALS['blog']['url']."rss/comments/".(int)$post."/".$title."/\" title=\"".$falt."\" class=\"rss_small\"><span>".$falt."</span></a>&nbsp;Comments</h4>\n");

	$rn = @mysql_num_rows($result);
	if($rn == 0) {
		$comments = "";
	} else {
		$comments = "    <div id=\"order\">\n";
		$comments .= ($mode == "ASC") ? "     <a id=\"asc_act\"><span>Order comments ascending by date</span></a>\n" : "     <a href=\"".$GLOBALS['blog']['url']."post/".(int)$post."/".$title."/order/asc\" id=\"asc\"><span>Order comments ascending by date</span></a>\n";
		$comments .= ($mode == "DESC") ? "     <a id=\"desc_act\"><span>Order comments descending by date</span></a>\n" : "     <a href=\"".$GLOBALS['blog']['url']."post/".(int)$post."/".$title."/order/desc\" id=\"desc\"><span>Order comments descending by date</span></a>\n";
		$comments .= "    </div>\n    <div id=\"comment_holder\">\n";
		for($i = 0; $i < $rn; $i++) {
			$ra = @mysql_fetch_assoc($result);
			$username = (empty($ra['com_author_id'])) ? $ra['com_author_name'] : $ra['user_disp'];
			$username = format($username,'text');
			$mail = (empty($ra['com_author_mail']) and !empty($ra['com_author_id'])) ? $ra['user_mail'] : $ra['com_author_mail'];
			if(empty($mail)) continue;
			$web = (!empty($ra['com_author_id'])) ? $ra['user_site'] : $ra['com_author_site'];
			if(!empty($web)) {
				$web = (!strpos($web,"//")) ? "http://".$web : $web;
				$link = "<a href=\"".$GLOBALS['blog']['url']."link/".$web."\" title=\"".$username.((substr($username,-1,1) == 's') ? "'" : "'s")." website\">".$username."</a>";
			} else {
				$link = $username;
			}
			$proflink = (!empty($ra['com_author_id'])) ? " (<a href=\"".$GLOBALS['blog']['url']."profile/".(int)$ra['com_author_id']."/\" title=\"".$username.((substr($username,-1,1) == 's') ? "'" : "'s")." profile\">+</a>)" : "";
			$comments .= "     <div class=\"comment_box\" id=\"comment_".$ra['com_id']."\">\n";
			$comments .= "      <div class=\"comment_data\">\n";
			$comments .= "       <div class=\"comment_avy\">".get_avatar($mail)."</div>\n";
			$comments .= "       <div class=\"comment_num\">#".($i+1)."</div>\n";
			$comments .= "       <div class=\"comment_inf\">\n";
			$comments .= "        Comment by ".$link.$proflink."<br />\n";
			$comments .= "        on ".vdate($ra['com_date'])."<br />\n";
			$comments .= "        <a href=\"".$GLOBALS['blog']['url']."post/".(int)$post."/".$title."/#comment_".$ra['com_id']."\">Link to this comment</a> | <a href=\"".$GLOBALS['blog']['url']."post/".(int)$post."/".$title."/#new\" onclick=\"reply('".$ra['com_id']."','".$username."')\">Reply</a>\n";
			$comments .= "       </div>\n";
			$comments .= "      </div>\n";
			$comments .= "      <div class=\"comment_body\">\n";
			$comments .= "       ".format($ra['com_text'],'full')."\n";
			$comments .= "      </div>\n";
			$comments .= "     </div>\n";
		}
		echo($comments."   </div>\n");
	}
	$form = "    <h4 id=\"new\">Post your comment</h4>\n";
	
	if($commentable == "locked" and !loggedin(1)) {
		$form .= "     <div>This post is locked.</div>\n";
	} elseif($commentable == "regged" and !loggedin()) {
		$form .= "     <div>Only registered users can comment.</div>\n";
	} else {
		$form .= "     <div id=\"comment_form\">\n      <form action=\"".$GLOBALS['blog']['url']."post/".$post."/".$title."/\" method=\"post\" onsubmit=\"return comment_check();\">\n       <fieldset>\n";
		if(!empty($GLOBALS['comment_msg'])) $form .= "        <div>".$GLOBALS['comment_msg']."</div>\n";
		if(loggedin()) {
			$form .= "        <div>You are already logged in as <strong>".get_data('name')."</strong>.</div>\n";
#			$form .= "        <input type=\"hidden\" id=\"comment_user\" name=\"comment_user\" value=\"".get_data('id')."\" />\n";
		} else {
			$c = comment_cookie();
			$form .= $c ? "        <div id=\"comment_saved\">You will leave your comment as <strong>".$c['name']."</strong><br />\n   <a onclick=\"showform();\">Change it if you wish</a>.</div>\n" : "";
			$form .= "        <div id=\"comment_new\"".($c ? " style=\"display:none;\"" : "").">\n";
			$form .= $c ? "         <div><a onclick=\"hideform();\" title=\"Hiding the form will result in resetting the data to the saved version\">Hide this form</a></div>\n" : "";
			$form .= "         <div><label for=\"comment_name\">Enter your name:</label><br /><input type=\"text\" name=\"comment_name\" id=\"comment_name\" class=\"text\" ".($c ? "value=\"".$c['name']."\" " : "")."/></div>\n";
			$form .= "         <div><label for=\"comment_mail\" title=\"Will not be displayed.\">Enter your e-mail address:</label><br /><input type=\"text\" name=\"comment_mail\" id=\"comment_mail\" class=\"text\" ".($c ? "value=\"".$c['mail']."\" " : "")."/></div>\n";
			$form .= "         <div><label for=\"comment_site\" title=\"Not required.\">Enter your website:</label><br /><input type=\"text\" name=\"comment_site\" id=\"comment_site\" class=\"text\" ".($c ? "value=\"".$c['site']."\" " : "")."/></div>\n";
			$form .= "        </div>\n";
		}
		$form .= "        <div><label for=\"comment_main\">Write your comment:</label><br /><textarea name=\"comment_main\" id=\"comment_main\" cols=\"35\" rows=\"10\"></textarea></div>\n";
		$form .= "        <input type=\"hidden\" name=\"comment_post\" id=\"comment_post\" value=\"".(int)$post."\" />\n";
		if(!loggedin()) {
			$form .= "        <script type=\"text/javascript\">antispam();</script>\n";
			$form .= "        <noscript>\n";
			$form .= "         <div><label for=\"spamprotect\"><b>Spam protection</b></label><br />Enter the <span id=\"need\" title=\"white\">opposite of black</span>: <input type=\"text\" name=\"spamprotect\" id=\"spamprotect\" /></div>\n";
			$form .= "        </noscript>\n";
		}
		$form .= "        <div><input type=\"submit\" name=\"comment_submit\" id=\"comment_submit\" /></div>\n";
		$form .= "       </fieldset>\n      </form>\n     </div>\n";
#		$form .= "    </div>\n";
	}
	
	echo($form);
}

function add_comment($mode,$post,$body,$name = "",$mail = "",$site = "") {
	if($mode == 1 and loggedin() and ($id = (int)get_data('id')) != 0) {
		return query("INSERT INTO `vblog_comments` VALUES(0,".time().",".(int)$post.",'".format($body,'db')."',".$id.",NULL,NULL,NULL,'".remote_ip()."')");
	} elseif($mode == 0 and !loggedin() and $name != "" and $mail != "" and
			 !is_spam($body) and !is_spam($name) and !is_spam($mail) and !is_spam($site) and
			 nouser($name,'name') and nouser($mail,'mail') and nouser($site,'site')) {
		$site = trim(format($site,'db'));
		return query("INSERT INTO `vblog_comments` VALUES(0,".time().",".(int)$post.",'".format($body,'db')."',NULL,'".format($name,'db')."','".format($mail,'db')."',".(empty($site) ? 'NULL' : "'".$site."'").",'".remote_ip()."')");
	}
	return false;
}
?>