<?php
if(isset($_POST['search_string'])) {
	error_reporting(E_ALL);
	header("Content-encoding: UTF-8");
	header("Content-type: text/html; charset=utf-8");
	if(strlen($_POST['search_string']) < 4) {
		die("\n<li>Enter ".(4-strlen($_POST['search_string']))." more characters to start searching.</li>\n");
	}
	
	
	define("vblog",rand(10^3,10^4-1));
	$mysql__i = 0;
	define('errorstring',"\n<li>Some errors occurred while processing your request. Please try again later.</li>\n");
	
	require_once("../backbone/core_database.php");
	query("SET NAMES utf8");
	
	require_once("../backbone/core_loadvars.php");
	$blog = init_vars();
	require_once("../backbone/core_correct.php");
	require_once("../backbone/core_date.php");
	
#	$str = mb_convert_encoding($_POST['search_string'],'utf-8');
	$str = $_POST['search_string'];
#	var_dump($str);
	$str = format($str,'db');

	$result = query("SELECT `post_id`,`post_title`,`post_date` FROM `vblog_posts` WHERE `post_title` LIKE '%".$str."%' OR `post_main` LIKE '%".$str."%' OR `post_long` LIKE '%".$str."%' ORDER BY `post_date` DESC");
	
	$rownum = @mysql_num_rows($result);
	
	if($rownum == 0) {
		die("\n<li>No posts match your search.</li>\n");
	}
	
	$out = "";
	for($i = 0; $i < $rownum; $i++) {
		$ra = mysql_fetch_row($result);
		$out .= "\n<li class=\"title\"><a href=\"".$blog['url']."posts/".(int)$ra[0]."/".format($ra[1],'url')."\">";
#		$out .= preg_replace("'(\&|\&amp;)#([0-9]+);'mis","&#\\2;",htmlentities(format_text_only($ra[1])));
		$out .= format($ra[1]);
		$out .= "</a></li>\n";
		$out .= "<li class=\"date\">".vdate((int)$ra[2])."</li>";
	}
	echo($out);
}
?>