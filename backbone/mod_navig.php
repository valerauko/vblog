<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function navigate() {
	if(isset($_GET['post']) and $_GET['post'] != "") {
		get_one_post((int)$_GET['post']);
	} elseif(isset($_GET['categ'])) {
		if($_GET['categ'] != "") {
			cat_posts((int)$_GET['categ'],(int)$_GET['page']);
		} else {
			cat_list();
		}
	} elseif(isset($_GET['archive'])) {
		if($_GET['archive'] != "") {
			arch_posts((int)$_GET['archive'],(int)$_GET['month'],(int)$_GET['page']);
		} else {
			arch_list();
		}
	} elseif(isset($_GET['page'])) {
		if($_GET['page'] == 'register') {
			require_once("backbone/mod_user.php");
			register();
		}
	} elseif(isset($_GET['profile'])) {
#		if(isset($_GET['edit'])) {
#			editprofile($_GET['profile']);
#		} else {
			showprofile($_GET['profile']);
#		}
	} elseif(isset($_GET['lang'])) {
		listlang($_GET['lang']);
	} elseif(isset($_GET['activate'])) {
		$str = (activate($_GET['activate'])) ? "Your account is now activated." : "Activation failed.";
		echo $str;
	} else {
		get_recents($GLOBALS['blog']['recents']);
	}
}

?>