<?php
error_reporting(E_ALL);								# set error reporting to E_ALL so i'll know all errors
define('vblog',rand(10^3,10^4-1));					# define protection string for included files
define('errorstring','Some errors occurred while processing your request. Please try again later.');
set_time_limit(5);									# set time limit so that infinite loops won't freeze the code

$mysql__i = 0;										# reset mysql query counter

header("Content-encoding: UTF-8");					# send utf-8 content encoding header

if(!session_id()) session_start();					# start session, if no session yet

require_once('backbone/core_database.php');			# include database connection file
query("SET NAMES utf8");							# set the mysql encoding to utf-8

require_once('backbone/core_date.php');
require_once('backbone/core_statistics.php');		# include stat class
$stat =& new stats();								# create and initialize stats

if(isset($_GET['resp'])) { $stat->redir_resp($_GET['resp']); }			# if this is an outgoing respect link, raise the link's stats and redirect
if(isset($_GET['link'])) { $stat->redir_link(/*$_GET['link']*/); }			# if this is an outgoing link, redirect

if(isset($_GET['mode']) and $_GET['mode'] == "file") {
	require("backbone/_attach.php");
}

ob_start('ob_gzhandler');							# start the ob that'll return all content gzipped
ob_start();

# echo("<!--".$_SERVER['REQUEST_URI']."-->");

require_once('backbone/core_log.php');				# include the log file (nothing before this point is logged)
require_once('backbone/core_antispam.php');

require_once('backbone/core_loadvars.php');
$blog = init_vars();								# initialize data and settings required later

require_once('backbone/core_correct.php');
require_once('backbone/core_auth.php');

# if(remote_ip() !== "89.133.175.223") { die("Maintenance. Will be back soon."/*"Sorry, this blog is still in a closed state. You'll see when it's open."*/); }

if(!empty($_GET['setlayout'])) { require_once("backbone/mod_layout.php"); setlayout($_GET['setlayout']); }
if(isset($_GET['logout']) and $_GET['logout'] != "done") { $logout = logout(); }

require_once('backbone/core_module.php');

if(!empty($_POST)) { require_once("backbone/_post.php"); }

if(isset($_GET['mode']) and $_GET['mode'] == "wap") {
	require("backbone/_wap.php");
} elseif(isset($_GET['mode']) and $_GET['mode'] == "rss") {
	require("backbone/_rss.php");
} elseif(isset($_GET['mode']) and $_GET['mode'] == "admin") {
	require_once("backbone/admin_navig.php");
	require("backbone/_admin.php");
} else {
	require_once("backbone/mod_panel.php");
	require_once("backbone/mod_layout.php");
#	if(remote_ip() == "89.133.175.223") { setlayout(2); }
	require("backbone/_web.php");
}

$contents = ob_get_clean();							# get everything printed as of yet and erase the ob contents (we have everything in $contents)

$stat->stop();										# stop the load time counter
print(str_replace("<<loadtime>>",$stat->loadtime,$contents));					# replace <<loadtime>> with the actual load time value

ob_end_flush();										# print the results
?>
