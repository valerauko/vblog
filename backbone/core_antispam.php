<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function check_visitor() {
	$uip = (empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR'];
	$banned = array('94.21.130.156','120.43.11.40','222.76.219.109');
	foreach($banned as $user)
		if($user == $uip)
			die(header("HTTP/1.0 403 Forbidden"));
}

check_visitor();

function is_spam($str) {
/*	$result = query("SELECT `key` FROM `vblog_antispam`");
	for($i = 0; $i < @mysql_num_rows($result); $i++) {
		if(mb_eregi(@mysql_result($result,$i),$str)) return true;
	}*/
	return false;
}


?>