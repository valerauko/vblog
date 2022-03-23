<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function vtime() {
	return time()+(7-(int)date('I'))*60*60;
}
function vdate($stamp = "none") {
	$format = "F j, Y G:i:s";
	if($stamp != "none") {
		$date = (int)$stamp+(7*60*60);
		$date -= ((int)date('I',$date))*60*60;
		if(date("F j, Y",$date) == date("F j, Y",time()+(7*60*60))) {
			return "today, ".date("G:i:s",$date);
		} elseif(date("F j, Y",$date) == date("F j, Y",time()-(17*60*60))) {
			return "yesterday, ".date("G:i:s",$date);
		} else {
			return date($format,$date);
		}
	} else {
		return vdate(vtime());
	}
}

?>