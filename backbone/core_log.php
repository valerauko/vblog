<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function log_event($type,$data = "") {
	$type_arr = array("auth","inner","");
	query("INSERT INTO `vblog_log` VALUES (".time().",'".format($type,'db')."','".format($data,'db')."')");
}
function abrute_check($ip,$place = 0) {
	$mintime = 30; // sec
	$maxtries = 10; // per hour
	
	
	query("DELETE FROM `vblog_antibrute` WHERE `time`<UNIX_TIMESTAMP()-3600");
	
	$result = query("SELECT * FROM `vblog_antibrute` WHERE `ip`='".format($ip,'db')."' AND `form`=".(($place == 1) ? 1 : 0)." ORDER BY `time` DESC");
	$rownum = @mysql_num_rows($result);
	if($rownum > 0) {
		if($rownum > $maxtries-1) {
			return 1; // too many tries this hour
		}
		if(@mysql_result($result,0,'time') > time()-30) {
			return 2; // too soon
		}
	}
	return true;
}
function abrute_add($ip,$place = 0) {
#place : 0: login, 1: admin
	query("INSERT INTO `vblog_antibrute` VALUES(".time().",'".format($ip,'db')."',".(($place == 1) ? 1 : 0).")");
}

?>