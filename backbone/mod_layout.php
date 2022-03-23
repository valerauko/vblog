<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function getlayout() {
	if(!empty($_COOKIE['vblog_layout'])) $source = $_COOKIE['vblog_layout'];
	elseif(!empty($_SESSION['vblog_layout'])) $source = $_SESSION['vblog_layout'];
	if(!empty($source)) {
		$result = query("SELECT * FROM `vblog_layouts` WHERE `layout_id`=".(int)$source);
		if(@mysql_num_rows($result) == 1) {
			$row = @mysql_fetch_row($result);
			$array = array("id" => $row[0],"title" => $row[1],"dir" => $row[2]);
			return $array;
		}
	}
	$result = query("SELECT * FROM `vblog_layouts` WHERE `layout_default`=1");
	if(@mysql_num_rows($result) !== 1) die(errorstring);
	$row = @mysql_fetch_row($result);
	$array = array("id" => $row[0],"title" => $row[1],"dir" => $row[2]);
	return $array;
}
function setlayout($id) {
	$result = query("SELECT `layout_id` FROM `vblog_layouts` WHERE `layout_id`=".(int)$id);
	if(@mysql_num_rows($result) !== 1) return false;
	query("UPDATE `vblog_layouts` SET `layout_used`=`layout_used`+1 WHERE `layout_id`=".(int)$id);
	$cookie = setcookie('vblog_layout',$id,time()+365*24*3600,'/',"blog.valerauko.net");
	if(!$cookie) $_SESSION['vblog_layout'] = $id;
	else header("Location: ".$GLOBALS['blog']['url']);
}

?>