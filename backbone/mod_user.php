<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function register() {
	$data = (isset($_POST['r_submit'])) ? $_POST : false;
	if(!empty($data)) {
		$msg = "";
		if(!empty($data['r_username']) and !empty($data['r_email'])) {
			if(mb_strlen($data['r_username']) > 1 and preg_match("'(^[A-Za-z0-9_\-\.]{2,}$)'",$data['r_username'])) {
				$username = format($data['r_username'],'db');					#
				if(mb_strlen($data['r_email']) > 5 and mb_strpos($data['r_email'],'@') and mb_strpos($data['r_email'],'.')) {
					$email = format($data['r_email'],'db');						#
					if(!empty($data['r_dispname'])) {
						$dispname = format($data['r_dispname'],'db');			#
					} else {
						$dispname = $username;									#
					}
					if(!empty($data['r_pass1']) and !empty($data['r_pass2'])) {
						if($data['r_pass1'] === $data['r_pass2']) {
							$pass = md5($data['r_pass1']);						#
							$actkey = md5(rand(time(),time()*2));
							$query = "INSERT INTO `vblog_users` VALUES(0,".time().",'".$username."','".$dispname."','".$pass."',NULL,'".$email."',NULL,'normal','".$actkey."',0)";
							$db = query($query);
							if(!$db) {
								$msg .= "Some unexpected errors occurred while processing your request. Please try again later.";
							}
							$send = <<<MAIL
Dear {$dispname},
your registration at vale[ blog ] is almost complete.
Please visit the following URL to activate your account.
{$GLOBALS['blog']['url']}activate/{$actkey}/
Best wishes.
MAIL;
							$ml = @mail($email,'vale[ blog ] registration',$send,"From:".$GLOBALS['blog']['contact']."\n"."Reply-to:".$GLOBALS['blog']['contact']);
							if(!$ml) {
								$msg .= "Your activation e-mail couldn't be sent. Please contact Vale.";
							}
							$msg = ($db && $ml)
								? "You have been registered. Check your mailbox for your activation e-mail.<br />If your account is not activated within a week, it'll be automatically deleted."
								: "Some unexpected errors occurred while processing your request. Please try again later.";
						} else {
							$msg .= "The two entered passwords do not match.<br />";
						}
					} else {
						$msg .= "Enter your password.<br />";
					}
				} else {
					$msg .= "The entered e-mail address is invalid.";
				}
			} else {
				$msg .= "The entered username is invalid.";
			}
		}
	}
?>

    <h3>Register</h3>
    <div>By registering you get the following privileges:
     <ul class="regul">
      <li>You can comment on posts that are locked for outsiders</li>
      <li>Your data are protected, so no-one could impersonate you</li>
     </ul>
    </div>
<?php if(!empty($msg)) { ?>
    <div><strong><?=$msg;?></strong></div>
<?php } ?>
    <form action="<?=$GLOBALS['blog']['url'];?>register/" method="post" id="regform" onsubmit="return check('reg');">
     <fieldset>
      <div>
       <label for="r_username" title="The username you'll have to enter to log in. Please don't use any special characters.">Username:</label><b>*</b><br /><input type="text" class="text" name="r_username" id="r_username" /><br />
       <label for="r_dispname" title="The name displayed">Display name:</label><br /><input type="text" class="text" name="r_dispname" id="r_dispname" /><br />
       <label for="r_email" title="Could be seen only by the admin">E-mail address:</label><b>*</b><br /><input type="text" class="text" name="r_email" id="r_email" /><br />
       <label for="r_pass1" title="Enter your password">Password:</label><b>*</b><br /><input type="password" class="text" name="r_pass1" id="r_pass1"  /><br />
       <label for="r_pass2" title="Confirm your passowrd">Confirm password:</label><b>*</b><br /><input type="password" class="text" name="r_pass2" id="r_pass2" /><br />
       <input type="submit" name="r_submit" id="r_submit" class="submit" value="Register" />
      </div>
      <div><b>*</b> required</div>
     </fieldset>
    </form>
<?php
}
function activate($key) {
	return query("UPDATE `vblog_users` SET `user_active`=1 WHERE `user_actkey`='".substr(preg_replace("'([^A-Fa-f0-9])'","",$key),0,32)."'");
}

function showprofile($id) {
	$id = ($id == 'self') ? (int)get_data('id') : (int)$id;
	$result = query("SELECT * FROM `vblog_users` WHERE `user_id`=".$id);
	if(@mysql_num_rows($result) !== 1) {
		echo "<strong>Error:</strong><br />There is no such user or you are not logged in (if you used the \"self\" identifier).";
	} else {
		$row = @mysql_fetch_assoc($result);
?>
    <h3>User profile: <?=$row['user_disp'];?></h3>
    <div id="profile">
     <ul>
      <li><strong>Registered since</strong> <?=vdate($row['user_regdate']);?></li>
<?php if(!empty($row['user_sig'])) { ?>
      <li><strong>Signature:</strong></li>
      <li><?=$row['user_sig'];?></li>
<?php } ?>
<?php if(!empty($row['user_site'])) { ?>
      <li><strong>Website:</strong> <a href="<?=$GLOBALS['blog']['url']?>link/<?=$row['user_site'];?>"><?=$row['user_disp'].((substr($row['user_disp'],-1,1) == 's') ? "'" : "'s");?> website</a></li>
<?php } ?>
     </ul>
    </div>
<?php	
	}
}
?>