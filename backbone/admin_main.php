<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

?>
<p>
Stats:
<ul>
<?php
$result = query("SELECT * FROM `vblog_stats`");
for($i = 0; $i < mysql_num_rows($result); $i++) {
	$row = mysql_fetch_row($result);
?>
<li><b><?=$row['0'];?></b> <?=$row[1];?>; <?=substr($row[2],0,100);?></li>
<?php
}
?>
</ul>
</p>
<p>
Visitors:
<ul>
<?php
$result = query("select `name`, `mail` from `vblog_sign`");
for($i = 0; $i < mysql_num_rows($result); $i++) {
	$row = mysql_fetch_row($result);
?>
<li><b><?=$row[0];?></b> (<?=base64_decode($row[1]);?>)</li>
<?php
}
?>
<p>Latest comments:
<ul>
<?php
$result = query('select vblog_posts.post_id, vblog_posts.post_title, vblog_comments.com_date, vblog_comments.com_author_name, vblog_users.user_disp from vblog_comments left join vblog_posts on vblog_comments.post_id=vblog_posts.post_id left join vblog_users on vblog_comments.com_author_id=vblog_users.user_id order by vblog_comments.com_date desc limit 10');
for($i = 0; $i < mysql_num_rows($result); $i++) {
	$row = mysql_fetch_row($result);
?>
<li>@<?=date('r',$row[2]);?> on <b><a href="http://blog.valerauko.net/post/<?=$row[0];?>"><?=$row[1];?></a></b> (<?=(empty($row[3]) ? $row[4] : $row[3]);?>)</li>
<?php
}
?>