<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function post_title($id) {
	if((int)$id < 1) return false;
	$result = query("SELECT `post_title` FROM `vblog_posts` WHERE `post_id`=".(int)$id);
	if(@mysql_num_rows($result) < 1) return false;
	return mysql_result($result,0);
}
function get_lang($id,$mode = 0) {
	if((int)$id < 1) return false;
	if((int)$mode !== 1) $mode = 0;
	$result = query("SELECT `lang_code`, `lang_name` FROM `vblog_languages` WHERE `lang_id`=".(int)$id);
	if(@mysql_num_rows($result) < 1) return false;
	return mysql_result($result,0,$mode);
}
function get_categ($id) {
	$id = (int)$id;
	$result = query("SELECT `vblog_categ`.* FROM `vblog_categ` RIGHT JOIN `vblog_c2p` ON `vblog_categ`.`categ_id`=`vblog_c2p`.`categ_id` RIGHT JOIN `vblog_posts` ON `vblog_c2p`.`post_id`=`vblog_posts`.`post_id` WHERE `vblog_posts`.`post_id`=".$id." ORDER BY RAND()");
	$rn = @mysql_num_rows($result);
	if($rn < 1) { return false; }
	$return = array();
	for($i = 0; $i < $rn; $i++) {
		$ra = @mysql_fetch_row($result);
		$return[$i+1] = array('id' => $ra[0],'name' => $ra[1],'desc' => $ra[2]);
	}
	return $return;
}
function get_file($id) {
	$id = (int)$id;
	$result = query("SELECT * FROM `vblog_files` WHERE `file_id`=".$id);
	if(@mysql_num_rows($result) !== 1) return false;
	else return mysql_fetch_assoc($result);
}
function is_post($id) {
	$result = query("SELECT `post_id` FROM `vblog_posts` WHERE `post_id`=".(int)$id);
	if(@mysql_num_rows($result) === 1) return true;
	else return false;
}
function prevnext($id) {
	query("set @x = (select post_date from vblog_posts where post_id=".(int)$id.")");
	$result = query("select post_id, post_title from ((select post_id, post_title from vblog_posts where post_date<@x order by post_date desc limit 1) union (select post_id, post_title from vblog_posts where post_date>@x order by post_date asc limit 1)) as temp");
	$output = array();
	for($i = 0; $i < @mysql_num_rows($result); $i++) {
		$output[] = mysql_fetch_assoc($result);
	}
	return $output;
}
function display_post($ra,$mode = 0,$data = 0) {
	global $blog;
	if($mode != 1) $mode = 0;
	if($data != 1) $data = 0;
	
	$id = (int)$ra[0];
	$title = format($ra[4],"text");
	$date = vdate((int)$ra[1],"post");
	$body = $ra[5];
	if($mode == 1) { $body .= "\n".$ra[6]; }
	$body = format($body,"full");
	$com_num = ( ((int)$ra['com_num'] == 0) ? 'No' : (int)$ra['com_num']);
	
	$categories = get_categ($id);
	
	if(!empty($ra[7])) { $avatar = urlencode($ra[7]); } else { $avatar = ""; }
	if(!empty($ra[9])) { $attachment = (int)$ra[9]; } else { $attachment = 0; }
	$lang = (empty($ra[8])) ? "en" : get_lang($ra[8]);
	$prevnext = prevnext($id);
	if(empty($ra[10])) {
		$trav = "";
	} else {
		$t = $ra[10];
		$trav = "\n      Travail ".(($t > 3600) ? ($h = (($t-($t%3600))/3600))." hour".(($h > 1) ? "s" : "").", " : "").( ($t > 60) ? (($t > 3600) ? ($h = ((($t-($t%60))%3600)/60)) : ($h = (($t-($t%60))/60)))." minute".($h > 1 ? "s" : "").", " : "").(($t > 0 and $t%60 > 0) ? ($h = $t%60)." second".($h > 1 ? "s" : "") : "")."<br />\n";
	}
	
	echo("\n");?>
    <div class="post <?=$lang;?>" xml:lang="<?=$lang;?>">
     <h2>
	  <?=(($mode == 1 and $prevnext[0]['post_id'] < $id) ? '<a class="leftlink" rel="prev" href="'.$blog['url'].'post/'.$prevnext[0]['post_id'].'/'.format($prevnext[0]['post_title'],'url').'/" title="Previous post: '.format($prevnext[0]['post_title'],'text').'">&laquo;</a>' : ""); ?>
      <a title="<?=$title;?> by Vale posted on <?=$date;?>" href="<?=$GLOBALS['blog']['url'];?>post/<?=$id;?>/<?=format($title,"url");?>/"><?=$title;?></a> <span class="hide">by Vale</span>
      <?=(($mode == 1 and !empty($prevnext[($n = (empty($prevnext[1]) ? 0 : 1))]['post_id']) and $prevnext[$n]['post_id'] > $id) ? '<a class="rightlink" rel="next" href="'.$GLOBALS['blog']['url'].'post/'.$prevnext[$n]['post_id'].'/'.format($prevnext[$n]['post_title'],'url').'/" title="Next post: '.format($prevnext[$n]['post_title'],'text').'">&raquo;</a>' : ""); ?>
     </h2>
     <div class="text">
      <?=$body;?>
      <?php if($mode == 0 && !empty($ra[6])) { ?><p class="cont_link"><a title="Read the whole post (<?=$title;?>)" href="<?=$GLOBALS['blog']['url'];?>post/<?=$id;?>/<?=format($title,'url');?>/">Read the full post</a></p><?php } ?>

     </div>
     <div class="data">
<?php if($avatar != "") { ?>      <img class="post_avatar" src="<?=$GLOBALS['blog']['url'];?>avatars/admin/<?=$avatar;?>" alt="Avatar used in post #<?=$id;?>" title="Vale no avatar" /><?="\n";?><?php } ?>
      <p>
      On since <?=$date;?><br /><?=$trav."\n";?>
      Related to <?php if($categories === false) { ?>nothing<?php } else { $cn = count($categories); foreach($categories as $key => $cat) { ?><a title="<?=format($cat['desc'],"text");?>" href="<?=$GLOBALS['blog']['url'];?>categ/<?=$cat['id'];?>/<?=format($cat['name'],"url");?>/"><?=format($cat['name'],"text");?></a><?php if($key < $cn) echo ","; ?> <?php } } ?><br />
<?php if($attachment != 0) { ?>      <a href="<?=$GLOBALS['blog']['url'];?>download/<?=$attachment;?>/">Download the attachment</a><br /><?php } ?>
      <?=$com_num;?> comments as of yet.<?php if($ra[3] == "locked") { ?> The post is locked.<?php } else {?> <a href="<?=$GLOBALS['blog']['url'];?>post/<?=$id;?>/<?=format($title,"url");?>/#new">Leave a comment</a><?="\n";?><?php } ?>
      </p>
     </div>
     <div style="clear:both;height:1px;">&nbsp;</div>
    </div>
	<?php
}
function get_recents($n, $o = 0) {
	$query = "SELECT
	`vblog_posts`.*,
	COUNT(`vblog_comments`.`post_id`) AS `com_num`
	FROM `vblog_posts`
	LEFT JOIN `vblog_comments` ON `vblog_comments`.`post_id`=`vblog_posts`.`post_id`
	WHERE `vblog_posts`.`post_date` < UNIX_TIMESTAMP()
	GROUP BY `vblog_posts`.`post_id`
	ORDER BY `vblog_posts`.`post_date` DESC
	LIMIT ".(int)$o.",".(int)$n;
	$result = query($query);
	$rn = @mysql_num_rows($result);
	if($rn < 1) {
		echo("<p>There are no posts in this amount yet.</p>");
	} else {
		for($i = 0; $i < $rn; $i++) {
			$ra = @mysql_fetch_array($result);
			if($i == 0) $d = 1;
			display_post($ra,0,$d);
		}
	}
}
function get_one_post($id) {
	$query = "SELECT
	`vblog_posts`.*,
	COUNT(`vblog_comments`.`post_id`) AS `com_num`
	FROM `vblog_posts`
	LEFT JOIN `vblog_comments` ON `vblog_comments`.`post_id`=`vblog_posts`.`post_id`
	WHERE `vblog_posts`.`post_id`=".(int)$id."
	GROUP BY `vblog_posts`.`post_date`
	LIMIT 1";
	
	$result = query($query);
	$rn = @mysql_num_rows($result);
	if($rn < 1) {
		echo("<p>There is such post in this blog.</p>");
	} else {
		$ra = @mysql_fetch_array($result);
		display_post($ra,1,1);
		get_comments($id,$ra[4],$_GET['order']);
	}
}
?>