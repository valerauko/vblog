<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

require_once("backbone/admin_ping.php");

function list_posts($categ = 0, $month = 0) {
  $u = $GLOBALS['blog']['url'];
  $result = query("select vblog_posts.post_id, vblog_posts.post_title, vblog_posts.post_date, count(vblog_comments.com_id) from vblog_posts left join vblog_comments on vblog_posts.post_id=vblog_comments.post_id group by vblog_posts.post_id order by vblog_posts.post_date desc limit 20");
  $rn = @mysql_num_rows($result);
  $out = <<<EOD
<h2>Manage posts</h2>
<p><a href="{$u}admin/posts/add/">Add a new post</a></p>
EOD;
  if($rn < 1) $out .= <<<EOD
<p>There are no posts.</p>
EOD;
  else {
    $out .= <<<EOD
<table cellpadding="0" cellspacing="0">
 <thead>
  <tr><th width="30">#</th><th width="240">Title</th><th width="150">Date</th><th width="15">C</th><th>Actions</th></tr>
  <tr>
   <form action="">
   <td><input type="text" onkeyup="doSearch('id',this.value)" maxlength="5" size="3" /></td>
   <td><input type="text" onkeyup="doSearch('title',this.value)" size="15" /></td>
   <td id="dateselect"></td>
   <td colspan="2"></td>
  </tr>
 </thead>
 <tbody>
EOD;
    while (list($i,$t,$d,$c)=@mysql_fetch_row($result)) {
      $t = short(format($t,'text'),40);
      $tu = urlsafe($t);
      $d = vdate($d);
      
      $out .= <<<EOD
  <tr><td>{$i}</td><td><a href="{$u}post/{$i}/{$tu}/">{$t}</a></td><td>{$d}</td><td>{$c}</td><td><a href="{$u}admin/posts/edit/{$i}">Edit</a>, <a href="{$u}admin/posts/del/{$i}">Delete</a></td></tr>
EOD;
    }
    $out .= <<<EOD
 </tbody>
</table>
EOD;
  }
  echo $out;
}
function fetch_all_categ() {
	$result = query("SELECT * FROM `vblog_categ`");
	$rn = @mysql_num_rows($result);
	if($rn < 1) {
		return false;
	} else {
		for($i = 0; $i < $rn; $i++) {
			$row = @mysql_fetch_row($result);
			if(!$row) return false;
			$array[$i+1] = array("key" => $row[0], "name" => $row[1], "desc" => dequote(format($row[2],"text")));
		}
		return $array;
	}
	return false;
}
function fetch_all_files() {
	$result = query("SELECT `file_id`,`file_name` FROM `vblog_files`");
	$rn = @mysql_num_rows($result);
	if($rn < 1) {
		return false;
	} else {
		for($i = 0; $i < $rn; $i++) {
			$row = @mysql_fetch_row($result);
			if(!$row) return false;
			$array[$row[0]] = $row[1];
		}
		return $array;
	}
	return false;
}
function fetch_one_file($id) {
	$result = query("SELECT `file_id`,`file_name` FROM `vblog_files` WHERE `file_id`=".(int)$id." LIMIT 1");
	$rn = @mysql_num_rows($result);
	if($rn < 1) {
		return false;
	} else {
		$row = @mysql_fetch_row($result);
		if(!$row) return false;
		$array = array($row[0],$row[1]);
		return $array;
	}
	return false;
}
function fetch_all_avatars() {
	if ($dir = @opendir("avatars/admin/")) {
		$k = 0;
		while (false !== ($file = @readdir($dir))) {
			if ($file != "." && $file != ".." && substr($file,-4,4) == ".jpg") {
				$avatars[$k] = $file;
				$k++;
			}
		}
		@closedir($dir);
		if(empty($avatars)) return false;
		else {sort($avatars); return $avatars; }
	} else {
		return false;
	}
}
function fetch_all_lang() {
	$result = query("SELECT `lang_id`,`lang_name` FROM `vblog_languages`");
	$rn = @mysql_num_rows($result);
	if($rn < 1) {
		return false;
	} else {
		for($i = 0; $i < $rn; $i++) {
			$row = @mysql_fetch_row($result);
			if(!$row) return false;
			$array[$row[0]] = $row[1];
		}
		return $array;
	}
	return false;
}

function print_form($post = 0) {
	if($post != 0) {
		$result = query("SELECT `vblog_posts`.*, COUNT(`vblog_comments`.`post_id`) FROM `vblog_posts` LEFT JOIN `vblog_comments` ON `vblog_comments`.`post_id`=`vblog_posts`.`post_id` WHERE `vblog_posts`.`post_id`=".(int)$post." GROUP BY `vblog_posts`.`post_date` LIMIT 1");
		$rn = @mysql_num_rows($result);
		if($rn !== 1) {
			$msg = "There is no such post.";
		} else {
			$row = @mysql_fetch_row($result);
			if(!$row) {
				$msg = "Some errors occurred.";
			} 
		}
	}
/*
// This form sends the following $_POST array:
// post_submit, posttitle, postbody, longbody, file, categ_ ..., avatar, lang status, com, trackback, year, month, day, hour, minute, second
*/
?>

   <div>
    <script type="text/javascript"><!--
     setInterval('checkSchedule()',1000);
    --></script>
<?php if(!empty($msg)) { $post = 0; ?><p class="error"><?=$msg;?></p><?php }?>
    <form action="<?=$GLOBALS['blog']['url'];?>admin/posts/<?php if($post!= 0) { ?>edit/<?=(int)$post; } else { ?>add/<?php }?>" method="post" onsubmit="return check();" name="post" id="post">
     <fieldset>
<?php if($post != 0) { ?>
      <input type="hidden" id="pid" name="pid" value="<?=$post;?>" />
<?php }?>
      <input type="hidden" id="begin" name="begin" value="<?=(($post != 0) ? time()-$row[10] : time());?>" />
      <p class="wide">
       <h3><label for="posttitle">Post title</label></h3>
       <input type="text" id="posttitle" name="posttitle" maxlength="512" size="50"<?=(($post != 0) ? " value=\"".$row[4]."\"" : "");?> />
      </p>
      <p class="wide">
       <h3><label for="postbody">Post body</label></h3>
       <ul id="buttons">
        <li onclick="surroundText('[b]','[/b]',document.forms.post.postbody)">[bold]</li>
        <li onclick="surroundText('[i]','[/i]',document.forms.post.postbody)">[italic]</li>
        <li onclick="surroundText('[u]','[/u]',document.forms.post.postbody)">[underline]</li>
        <li onclick="surroundText('[quote]','[/quote]',document.forms.post.postbody)">[quote]</li>
        <li onclick="surroundText('[bq]','[/bq]',document.forms.post.postbody)">[blockquote]</li>
        <li onclick="surroundText('[h]','[/h]',document.forms.post.postbody)">[holy]</li>
        <li onclick="surroundText('[sup]','[/sup]',document.forms.post.postbody)">[sup]</li>
        <li onclick="surroundText('[sub]','[/sub]',document.forms.post.postbody)">[sub]</li>
        <li onclick="surroundText('[ov]','[/ov]',document.forms.post.postbody)">[over]</li>
        <li onclick="surroundText('[code]','[/code]',document.forms.post.postbody)">[code]</li>
       </ul>
      </p>
      <p class="wide">
       <textarea rows="5" cols="50" id="postbody" name="postbody" onkeypress="savetext(this.value,'m')"><?php if($post != 0) { echo html_entity_decode($row[5]); } elseif(!empty($_COOKIE['vblog_save_m'])) { echo(urldecode(decode_unicode_url($_COOKIE['vblog_save']))); } ?></textarea>
      </p>
      <p class="wide">
       <h3><label for="longbody">Long body</label></h3>
       <ul id="buttons">
        <li onclick="surroundText('[b]','[/b]',document.forms.post.postbody)">[bold]</li>
        <li onclick="surroundText('[i]','[/i]',document.forms.post.postbody)">[italic]</li>
        <li onclick="surroundText('[u]','[/u]',document.forms.post.postbody)">[underline]</li>
        <li onclick="surroundText('[quote]','[/quote]',document.forms.post.postbody)">[quote]</li>
        <li onclick="surroundText('[bq]','[/bq]',document.forms.post.postbody)">[blockquote]</li>
        <li onclick="surroundText('[h]','[/h]',document.forms.post.postbody)">[holy]</li>
        <li onclick="surroundText('[sup]','[/sup]',document.forms.post.postbody)">[sup]</li>
        <li onclick="surroundText('[sub]','[/sub]',document.forms.post.postbody)">[sub]</li>
        <li onclick="surroundText('[ov]','[/ov]',document.forms.post.postbody)">[over]</li>
        <li onclick="surroundText('[code]','[/code]',document.forms.post.postbody)">[code]</li>
       </ul>
      </p>
      <p class="wide">
       <textarea rows="5" cols="50" id="longbody" name="longbody" onkeypress="savetext(this.value,'l')"><?php if($post != 0) { echo  html_entity_decode($row[6]); } elseif(!empty($_COOKIE['vblog_save_l'])) { echo(urldecode(decode_unicode_url($_COOKIE['vblog_save']))); } ?></textarea>
      </p>
	  <p class="wide"><input type="submit" id="<?=(($post != 0) ? "edit_submit" : "post_submit")?>" name="<?=(($post != 0) ? "edit_submit" : "post_submit")?>" value="<?=(($post!=0) ? "Edit it!" : "Post it!");?>" /></p>
      <p class="wide">
       <h3>File attachment</h3>
<?php if($files = fetch_all_files()) { ?>
<?php if($post != 0) { $file = fetch_one_file($row[9]); ?>
       <p><?=(($file == false) ? "There are no attached files" : "Current attachment: <span title=\"".$row[9]."\">".$file[1]."</span>");?></p>
<?php }?>
       
       <select id="file" name="file">
        <option value="0"<?php if(!$file) { ?>selected="selected"<?php } ?>>No attachment</option>
<?php foreach($files as $id => $name) { ?>
        <option value="<?=$id;?>"<?php if($post != 0 and $row[9] == $id) {?>selected="selected"<?php } ?>><?=format($name,'text');?></option>
<?php } ?>
       </select>
<?php } else { ?>
       <ul>
        <li>There are no files to attach.</li>
       </ul>
<?php } ?>
      </p>
      <p class="wide">
       <h3><label for="trackback">Trackback</label></h3>
      </p>
     </fieldset>
     <fieldset>
      <p class="right">
       <h3>Categories</h3>
       <ul>
<?php
if($categ = fetch_all_categ()) {
  if($post!=0) {
    $r = query("select categ_id from vblog_c2p where post_id=".(int)$post);
    $c = array();
    $i = 0;
    while($c[$i] = @mysql_result($r,$i++));
  }
  foreach($categ as $thing) { ?>
        <li><input type="checkbox" id="categ_<?=$thing['key'];?>" <?php if($post != 0 and array_search($thing['key'],$c) != false) {?> checked="checked"<?php }?> name="categ_<?=$thing['key'];?>" value="1" /><label for="categ_<?=$thing['key'];?>" title="<?=$thing['desc'];?>"><?=format($thing['name'],'text');?></label></li>
<?php } } else { ?>
        <li>There are no categories</li>
<?php } ?>
       </ul>
      </p>
      <p class="right">
       <h3>Language</h3>
       <ul>
<?php if($lang = fetch_all_lang()) { foreach($lang as $key => $value) { ?>
        <li><input type="radio" id="lang_<?=$key;?>" name="lang" value="<?=$key;?>"<?php if($post != 0 and $row[8] == $key) {?> checked="checked"<?php }?> /><label for="lang_<?=$key;?>"><?=format($value,'text');?></label></li>
<?php } } else { ?>
        <li>There are no categories</li>
<?php } ?>
       </ul>
      </p>
      <p class="right">
       <h3>Avatars</h3>
<?php if(($ava = fetch_all_avatars()) != false) { ?>
       <p style="text-align:center"><img width="50" height="50" alt="Avatar Preview" id="avaPrev" /></p>
       <select id="avatar" name="avatar" onchange="document.getElementById('avaPrev').src='http://blog.valerauko.net/avatars/admin/'+this.value">
        <option onmouseover="document.getElementById('avaPrev').src=''" value="0"<?php if($post == 0) { ?>selected="selected"<?php } ?>>No avatar</option>
<?php foreach($ava as $thing) { ?>
        <option onmouseover="document.getElementById('avaPrev').src='http://blog.valerauko.net/avatars/admin/<?=$thing;?>'" value="<?=$thing;?>"<?php if($post != 0 and $row[7] == $thing) {?>selected="selected"<?php } ?>><?=$thing;?></option>
<?php } ?>
       </select>
<?php } else { ?>
       <ul>
        <li>There are no avatars</li>
       </ul>
<?php } ?>
      </p>
      <p class="right">
       <h3>Status</h3>
       <ul>
        <li><input type="radio" name="status" id="public" value="public" checked="checked" /><label for="public">Public</label></li>
        <li><input type="radio" name="status" id="sketch" value="sketch" /><label for="sketch">Sketch</label></li>
        <li><input type="radio" name="status" id="scheduled" value="scheduled" onclick="checkSchedule();" /><label for="scheduled">Scheduled</label></li>
       </ul>
      </p>
      <p class="right">
       <h3>Commentability</h3>
       <ul>
        <li><input type="radio" name="com" id="open" value="open" checked="checked" /><label for="open">Open</label></li>
        <li><input type="radio" name="com" id="locked" value="locked" /><label for="locked">Locked</label></li>
        <li><input type="radio" name="com" id="regged" value="regged" /><label for="regged">Regged</label></li>
       </ul>
      </p>
      <p class="right">
       <h3>Misc</h3>
       <ul>
        <li><input type="checkbox" id="trackback" name="trackback" value="1" checked="checked" /><label for="trackback">Enable trackbacks</label></li>
        <?php
        $d = (($post != 0) ? @mysql_result(query("select post_date from vblog_posts where post_id=".$post),0) : time())+7*3600;
       ?>
        <li id="schedule" <?php if(empty($post)) { ?>style="display:none;"<?php } else {?>title="s"<?php }?>><h4 title="Will only take effect if Status is Scheduled">Set timestamp</h4><br />
         <label for="year" class="hidden">Year:</label><input type="text" id="year" name="year" title="Year" maxlength="4" size="4" value="<?=date("Y",$d);?>" />
         <label for="month" class="hidden">Month:</label><input type="text" id="month" name="month" title="Month" maxlength="2" size="2" value="<?=date("m",$d);?>" />
         <label for="day" class="hidden">Day:</label><input type="text" id="day" name="day" title="Day" maxlength="2" size="2" value="<?=date("d",$d);?>" /><br />
         <label for="hour" class="hidden">Hour:</label><input type="text" id="hour" name="hour" title="Hour" maxlength="2" size="2" value="<?=date("H",$d);?>" />:
         <label for="minute" class="hidden">Minute:</label><input type="text" id="minute" name="minute" title="Minute" maxlength="2" size="2" value="<?=date("i",$d);?>" />:
         <label for="second" class="hidden">Second:</label><input type="text" id="second" name="second" title="Second" maxlength="2" size="2" value="<?=date("s",$d);?>" />
        </li>
       </ul>
      </p>
     </fieldset>
    </form>
<?php
}

if(!empty($_GET['subpage'])) {
  if(($_GET['subpage'] == "add") or ($_GET['subpage'] == "edit" and !empty($_GET['bottom']))) {
    # post_submit, posttitle, postbody, longbody, file, categ_ ..., status, com, trackback, year, month, day, hour, minute, second
    if(isset($_POST['post_submit']) or isset($_POST['edit_submit'])) {
      foreach($_POST as $key => $value):
        if($key == "posttitle") { $title = format($value,'db'); }
        elseif($key == "postbody") { $sbody = format($value,'db'); }
        elseif($key == "longbody") { $lbody = format($value,'db'); }
        elseif($key == "file") { $file = (int)$value; }
        elseif($key == "status") { switch($value){case "public":case "sketch":case "scheduled":$status=$value;break;default:$status="public";} }
        elseif($key == "com") { switch($value){case "open":case "closed":case "regged":$com=$value;break;default:$com="open";} }
        elseif(substr($key,0,6) == "categ_") { if($value == 1) { $categ[] = substr($key,6,2); } }
        elseif($key == "year" or $key == "month" or $key == "day" or $key == "hour" or $key == "minute" or $key == "second") {
          if(($key == "hour" or $key == "minute" or $key == "second") and strlen($key) == 1) $$key = "0".$value;
          else $$key = $value;
        }
        elseif($key == "avatar") { $avatar = format($value,'db'); }
        elseif($key == "lang") { $lang = (int)$value; }
      endforeach;
      if(isset($title,$sbody,$status,$com)) {
        $e = isset($_POST['edit_submit'],$_POST['pid']);
		$trav = isset($_POST['begin']) ? time()-$_POST['begin'] : 0;
        if(isset($_POST['post_submit'])) { $query = "INSERT INTO `vblog_posts` VALUES(0,"; } elseif($e) { $query = "UPDATE `vblog_posts` SET "; }
        if(($status != "scheduled" and empty($_POST['pid'])) or
           !isset($year,$month,$day,$hour,$minute,$second) or
           (int)$year < (int)date("Y") or
           (int)$month > 12 or (int)$month < 1 or
           (int)$day > 31 or (int)$day < 1 or
           (int)$hour > 23 or (int)$hour < 0 or
           (int)$minute > 59 or (int)$minute < 0 or
           (int)$second > 59 or (int)$second < 0) {
          $query .= ($e ? "" : time().",");
        } else {
          $query .= ($e ? "`post_date`=" : "")."UNIX_TIMESTAMP('".(int)$year."-".(int)$month."-".(int)$day." ".((strlen($hour) == 1) ? "0".(int)$hour : $hour).":".((strlen($minute) == 1) ? "0".(int)$minute : $minute).":".((strlen($second) == 1) ? "0".(int)$second : $second)."')-7*3600,";
        }
        $query .= ($e ? " `post_status`=" :"")."'".(isset($status)?$status:'public')."',".($e ? "`post_commentable`=" : "")."'".(isset($com)?$com:'open')."',".($e ? "`post_title`=" : "")."'".$title."',".($e ? "`post_main`=" : "")."'".$sbody."',".($e ? "`post_long`=" : "")."'".$lbody."',".((!empty($avatar))?($e ? "`post_avatar`=" : "")."'".$avatar."'":($e ? "`post_avatar`=" : "")."NULL").",".($e ? "`lang_id`=" : "")."".(isset($lang)?$lang:1).",".($e ? "`file_id`=" : "").(isset($file)?$file:0).",".($e ? "`post_travail`=" : "").$trav.($e ? " where `post_id`=".(int)$_POST['pid'] : ")");
        var_dump($query);
        query($query);
        $r = ($e ? $_POST['pid'] : mysql_result(query("SELECT `post_id` FROM `vblog_posts` ORDER BY `post_date` DESC LIMIT 1"),0) );
        foreach($categ as $cat) { query("INSERT IGNORE INTO `vblog_c2p` VALUES(".(int)$cat.",".(int)$r.")"); }
        if(!$e) ping(true);
      }
    } else {
      print_form((($_GET['subpage'] == "edit" and !empty($_GET['bottom'])) ? $_GET['bottom'] : ''));
    }
  } elseif($_GET['subpage'] == "del" and !empty($_GET['bottom'])) {
    echo("will delete post ".$_GET['bottom']);
  } else {
    list_posts();
  }
} else {
  list_posts();
}
?>