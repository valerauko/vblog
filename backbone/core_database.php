<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

if($_SERVER['HTTP_HOST'] !== 'vale.home') {
	$mysqlusr = "vale_vale";
	$mysqlpas = "3yw3rs6m";
	$mysqlbas = "vale_valenet";
	$mysqlhos = "localhost";
} else {
	$mysqlusr = "root";
	$mysqlpas = "";
	$mysqlbas = "vblog";
	$mysqlhos = "localhost";
}
@mysql_connect($mysqlhos,$mysqlusr,$mysqlpas) or die(errorstring." (M".mysql_errno()."@C".mysql_error().")");
@mysql_select_db($mysqlbas) or die(errorstring." (M".mysql_errno()."@S)");

function query($query = "empty"){
	$GLOBALS['mysql__i']++;
	if($query == "empty") return false;
	$result = @mysql_query($query) or die(errorstring." (".mysql_errno().":".mysql_error().")");
	return $result;
}
?>