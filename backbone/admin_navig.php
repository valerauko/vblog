<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function admin_navig() {
	if(file_exists("backbone/admin_".format($_GET['page'],'url').".php")) {
		require_once("backbone/admin_".format($_GET['page'],'url').".php");
	} else {
		require_once("backbone/admin_main.php");
	}
}
function print_menu() {
	$menu = array("posts","pages","comments","files","categories","users","links","antispam","quotes","plugins","stats");
?>
    <li<?php if(!isset($_GET['page']) or empty($_GET['page'])) {?> class="selected"<?php }?>><a href="<?=$GLOBALS['blog']['url'];?>admin/">Main</a></li>
<?php
	foreach($menu as $item) {
?>
    <li<?php if(isset($_GET['page']) and $_GET['page'] == $item) {?> class="selected"<?php }?>><a href="<?=$GLOBALS['blog']['url'];?>admin/<?=$item;?>/"><?=ucfirst($item);?></a></li>
<?php
	}
?>
    <li><a href="<?=$GLOBALS['blog']['url'];?>admin/logout/">Log out</a></li>
    <li><a href="<?=$GLOBALS['blog']['url'];?>">Back to blog</a></li>

<?php
}

?>