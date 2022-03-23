<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function enbase($string) {
	$string = base64_encode("golbv".$string."vblog");
	$string = substr($string,0,-1);
	return $string;
}
function debase($string) {
	$string .= "=";
	$string = base64_decode($string);
	$string = substr($string,5,-4);
	return $string;
}
/*function comment_cookie($mode = false) {
	if(isset($_COOKIE['vblog_form'])) {
		$cookie = debase($_COOKIE['vblog_form']);
		$cookie = unserialize($cookie);
		$username = debase($cookie[enbase('username')]);
		$usermail = debase($cookie[enbase('usermail')]);
		$usersite = debase($cookie[enbase('usersite')]);
	} else {
		return false;
	}
	switch($mode):
		case 1:return $username; break;
		case 2:return $usermail; break;
		case 3:return $usersite; break;
		default:if(is_array($cookie)) return true;
	endswitch;
	return false;
}*/
function remote_ip() {
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) and $_SERVER['HTTP_X_FORWARDED_FOR'] != ""){
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}
function loggedin($mode = 0) {
	query('delete from `vblog_users` where `user_active`=0 and `user_regdate`<now()-interval 5 day');
	if(empty($_COOKIE['vblog_user']) and empty($_SESSION['vblog_user'])) return false;
	$raw = (empty($_COOKIE['vblog_user'])) ? $_SESSION['vblog_user'] : $_COOKIE['vblog_user'];
	if(!empty($raw)) {
		$data = debase($raw);
		$data = @unserialize($data);
		$userid = format(debase($data[enbase('userid')]),'db');
		$usermd = format(debase($data[enbase('usermd')]),'db');
		
		$query = "SELECT `user_id` FROM `vblog_users` WHERE `user_id`=".(int)$userid." AND `user_pass`='".$usermd."' AND `user_active`=1";
		$query .= ($mode != 0) ? " AND `user_group`='admin'" : "";
		
		$result = query($query);
		$rownum = @mysql_num_rows($result);
		if($rownum === 1) {
			return true;
		}
	}
	return false;
}
function get_data($mode) {
	$error = "You are not logged in.";
	if(!loggedin()) return $error;
	
	$raw = (empty($_COOKIE['vblog_user'])) ? $_SESSION['vblog_user'] : $_COOKIE['vblog_user'];
	if(!empty($raw)) {
		$data = debase($raw);
		$data = @unserialize($data);
		$userid = format(debase($data[enbase('userid')]),'db');
		$usermd = format(debase($data[enbase('usermd')]),'db');
		
		$result = query("SELECT * FROM `vblog_users` WHERE `user_id`=".(int)$userid." AND `user_pass`='".$usermd."'");
		
		$rownum = @mysql_num_rows($result);
		if($rownum !== 1) {
			return $error;
		} else {
			switch($mode) {
				case "id":#case (int)0:
					return @mysql_result($result,0,'user_id');
					break;
				case "name":#case (int)1:
					return @mysql_result($result,0,'user_disp');
					break;
			}
		}
	} else {
		return $error;
	}
	return false;			
}
function login($uname,$upass,$mode = 0,$admin = 0) {
	$uname = format($uname,'db');
	$upass = md5($upass);
	
	$query = "SELECT `user_id` FROM `vblog_users` WHERE `user_name`='".$uname."' AND `user_pass`='".$upass."'";
	$query .= ($admin != 0) ? " AND `user_group`='admin'" : "";
	
	$result = query($query);
	
	if(@mysql_num_rows($result) === 1) {
		$uid = @mysql_result($result,0,'user_id');
		if(empty($uid)) return false;
		$data = enbase(serialize(array(enbase('userid') => enbase($uid),enbase('usermd') => enbase($upass))));
		if($mode != 0) {
			$c = @setcookie('vblog_user',$data,time()+365*24*3600,'/','blog.valerauko.net',0);
			if(!$c) { return false; }
			else { header("Location: ".$GLOBALS['blog']['url']); return true; }
		} else {
			$_SESSION['vblog_user'] = $data;
			return true;
		}
	}
	return false;
}
function logout($url = "") {
	if(empty($url)) { $url = $GLOBALS['blog']['url']; }
	$_SESSION['vblog_user'] = "";
	$c = @setcookie("vblog_user","",time()-60*60,"/",'blog.valerauko.net',0);
	if(!empty($_SESSION['vblog_user']) or !$c) {
		return false;
	} else {
		header("Location: ".$url."logout/done/");
	}
}
function nouser($str,$mode = '') {
	$mode = strtolower($mode);
	switch($mode):
		case 'site':
		case 'mail':
		case 'name':
			$result = query("select `user_id` from `vblog_users` where `user_".$mode."`='".format($str,'db')."'");
			if(mysql_num_rows($result) > 0) return false;
			else return true;
			break;
		default: return false;
	endswitch;
}
?>