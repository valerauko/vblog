<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

if(isset($_GET['archive'])) {
	require_once("backbone/mod_navig.php");
	require_once("backbone/mod_pagin.php");
	require_once("backbone/mod_get.php");
	require_once("backbone/mod_archive.php");
} elseif(isset($_GET['post'])) {
	require_once("backbone/mod_navig.php");
	require_once("backbone/mod_get.php");
	require_once("backbone/mod_comments.php");
} elseif(isset($_GET['lang'])) {
	require_once("backbone/mod_navig.php");
	require_once("backbone/mod_pagin.php");
	require_once("backbone/mod_lang.php");
	if($_GET['lang'] != "") {
		require_once("backbone/mod_get.php");
	}
} elseif(isset($_GET['categ'])) {
	require_once("backbone/mod_navig.php");
	require_once("backbone/mod_pagin.php");
	require_once("backbone/mod_categ.php");
	if($_GET['categ'] != "") {
		require_once("backbone/mod_get.php");
	}
} elseif(isset($_GET['profile']) or isset($_GET['activate'])) {
	require_once("backbone/mod_navig.php");
	require_once("backbone/mod_user.php");
} elseif(isset($_GET['load']) and $_GET['load'] == "admin") {

	require_once("backbone/admin_add.php");
	require_once("backbone/admin_categ.php");
	require_once("backbone/admin_comments.php");
	require_once("backbone/admin_edit.php");
	require_once("backbone/admin_span.php");

} else {
	require_once("backbone/mod_navig.php");
	require_once("backbone/mod_get.php");
}
?>