<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net"); }

function loadvars($key) {
	$result = query("SELECT * FROM `vblog_settings` WHERE `set_id`='".format($key,'db')."'");
	if(@mysql_num_rows($result) !== 1) { return false; }
	return mysql_result($result,0,'set_value');
}
function init_vars() {
	$result = query("SELECT * FROM `vblog_settings`");
	$rownum = @mysql_num_rows($result);
	if($rownum < 1) { return false; }
	for($i = 0; $i < $rownum; $i++) {
		$row = mysql_fetch_row($result);
		switch($row[0]):
			case "title":
			case "url":
			case "keywords":
			case "descript":
			case "author":
			case "contact":
			case "button":
			case "recents":
				$array[$row[0]] = $row[1];
				break;
			case "activity":
			case "feat":
			case "user":
			case "quotes":
			case "ancient":
			case "categ":
			case "sign":
			case "layout":
			case "stats":
			case "respect":
			case "lang":
			case "archive":
				$array['panels'][$row[0]] = $row[1];
				break;
		endswitch;
	}
	return $array;
}
function location() {
	if(isset($_GET['archive'])) {
		$return = "[ archives ]";
		if(!empty($_GET['archive'])):
			$return .= "[ ".$_GET['archive']." ]";
			if(!empty($_GET['month'])):
				$months = array(1=>'january',2=>'february',3=>'march',4=>'april',5=>'may',6=>'june',7=>'july',8=>'august',9=>'september',10=>'october',11=>'november',12=>'december');
				$k = !empty($months[(int)$_GET['month']]) ? $months[(int)$_GET['month']] : "a month";
				if(!empty($k)) $return .= "[ ".$k." ]";
			endif;
			if(!empty($_GET['page'])):
				$end = array(1=>'st',2=>'nd',3=>'rd',4=>'th',5=>'th',6=>'th',7=>'th',8=>'th',9=>'th',0=>'th');
				$return .= "[ ".(int)$_GET['page'].$end[(int)substr($_GET['page'],0,-1)]." page ]";
			endif;
		endif;
		return $return;
	} elseif(isset($_GET['post'])) {
		$title = post_title($_GET['post']);
		return "[ ".(empty($title) ? "posts" : $title)." ]";
	} elseif(isset($_GET['lang'])) {
		if($_GET['lang'] != "") {
			$k = mb_strtolower(get_lang($_GET['lang'],1));
			$lang = empty($k) ? "a" : $k;
			return "[ ".$lang." posts ]";
		}
		return "[ listing languages ]";
	} elseif(isset($_GET['categ'])) {
		if($_GET['categ'] != "") {
			$result = query('select categ_name from vblog_categ where categ_id='.(int)$_GET['categ']);
			if(@mysql_num_rows($result) === 1) $k = mb_strtolower(@mysql_result($result,0));
			$categ = empty($k) ? "a" : $k;
			return "[ ".$categ." posts ]";
		}
		return "[ category listing ]";
	} elseif(isset($_GET['profile']) or isset($_GET['activate'])) {
		$return = "[ ";
		if(!empty($_GET['profile'])) {
			$result = query('select user_disp from vblog_users where user_id='.(int)$_GET['profile']);
			if(@mysql_num_rows($result) === 1) {
				$user = @mysql_result($result,0);
				if(!empty($user)) {
					if(substr($user,0,-1) == 's') $user .= "'";
					else $user .= "'s";
					$return .= $user." ";
				}
			}
		}
		$return .= "profile ]";
		return $return;
	}
}
?>