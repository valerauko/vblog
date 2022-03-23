<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

class stats {
/*
// ids in database:
// 1: total hits
// 2: today hits
// 3: total visits
// 4: today visits
// 5: outgoing links
// 6: online
*/
	var $posts_average;
	var $total_hits;
	var $total_visitors;
	var $today_hits;
	var $today_visitors;
	var $online;
	var $links_out;
	var $loadtime;
	var $posts;
	var $dte;
	var $comments;
	
	function stats() {
		$this->start();
		$this->single();
		$this->unique();
#		$this->average();
		$this->online();
		$this->days();
		$this->gets();
	}
	function days() {
		$result = query("select floor((max(post_date)-min(post_date))/(3600*24)) from vblog_posts");
		$days = @mysql_result($result,0);
		$year = ( (($days-($days % 365)) > 0) ? (($days-($days % 365))/365) . " years and " : "" );
		$days = (empty($year) ? $days : ($days%365))." days";
		$this->dte = $year.$days;
	}
	function gets() {
		$result = query("SELECT * FROM `vblog_stats`");
		for($i = 0; $i < @mysql_num_rows($result); $i++) {
			$row = mysql_fetch_row($result);
			switch($row[0]) {
				case 1:$this->total_hits = $row[1]; break;
				case 2:$this->today_hits = $row[1]; break;
				case 3:$this->total_visitors = $row[1]; break;
				case 4:$this->today_visitors = $row[1]; break;
				case 5:$this->links_out = $row[1]; break;
			}
		}
		$result = query("SELECT `post_id` FROM `vblog_posts`");
		$this->posts = (int)@mysql_num_rows($result);
		$result = query("SELECT `post_id` FROM `vblog_comments`");
		$this->comments = (int)@mysql_num_rows($result);
		if($this->comments > 0) $this->posts_average = round($this->posts/$this->comments,1);
		else $this->posts_average = 0;
	}
	function single() {
		query("UPDATE `vblog_stats` SET `stat_num`=`stat_num`+1 WHERE `stat_id`=1 LIMIT 1");
		
		$result = query("SELECT `stat_num`,`stat_misc` FROM `vblog_stats` WHERE `stat_id`=2 LIMIT 1");
		if(mysql_result($result,0,1) != date('d')) {
			query("UPDATE `vblog_stats` SET `stat_num`=1, `stat_misc`='".date('d')."' WHERE `stat_id`=2 LIMIT 1");
		} else {
			query("UPDATE `vblog_stats` SET `stat_num`=`stat_num`+1 WHERE `stat_id`=2 LIMIT 1");
		}
	}
	function unique() {
		if(!isset($_COOKIE['vblog_visitor']) and !isset($_SESSION['vblog_visitor'])) {
			query("UPDATE `vblog_stats` SET `stat_num`=`stat_num`+1 WHERE `stat_id`=3 LIMIT 1");
			$c = @setcookie('vblog_visitor',rand(1000,9999),time()+3600*24*7,'/',$_SERVER['HTTP_HOST'],0);
			if(!$c) {
				$_SESSION['vblog_visitor'] = rand(1000,9999);
			}
			$result = query("SELECT `stat_num`,`stat_misc` FROM `vblog_stats` WHERE `stat_id`=4 LIMIT 1");
			if(mysql_result($result,0,1) != date('d')) {
				query("UPDATE `vblog_stats` SET `stat_num`=1, `stat_misc`='".date('d')."' WHERE `stat_id`=4 LIMIT 1");
			} else {
				query("UPDATE `vblog_stats` SET `stat_num`=`stat_num`+1 WHERE `stat_id`=4 LIMIT 1");
			}
		}
	}
	function average() {
		$res = query("SELECT `post_date` FROM `vblog_posts` ORDER BY `post_date` ASC");
		$rn = mysql_num_rows($res);
		$min_date = mysql_result($res,0);
		$max_date = mysql_result($res,$rn-1);
		$days = @ceil(($max_date-$min_date)/(24*3600));
		$avg = round($rn/$days,1);
		$this->avg = $avg;
	}
	function start() {
		$time = microtime();
		$time = explode(" ", $time);
		$time = $time[1] + $time[0];
		$this->start = $time;
	}
	function stop() {
		$time = microtime();
		$time = explode(" ", $time);
		$time = $time[1] + $time[0];
		$this->loadtime = round($time - $this->start,4);
	}
	function online(){
		$ctime = time();
		$tout = 600;
		$time = $ctime - $tout;
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) and $_SERVER['HTTP_X_FORWARDED_FOR'] != ""){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		$result = query("SELECT `stat_misc` FROM `vblog_stats` WHERE `stat_id`=6");;
		$old = mysql_result($result,0);
		
		$olda = explode(">",$old);
		if(count($olda) > 50) {
			$old = "";
			for($i = 0; $i < count($olda)-25; $i++) {
				$old .= $olda[$i+25].">";
			}
		} else {
			$old = "";
			for($i = 0; $i < count($olda); $i++) {
				$old .= $olda[$i].">";
			}
		}
	
		$new = $old.$ip."||".$ctime;
		query("UPDATE `vblog_stats` SET `stat_misc`='".$new."' WHERE `stat_id`=6");
		
		$result = query("SELECT `stat_misc` FROM `vblog_stats` WHERE `stat_id`=6");
		$got = mysql_result($result,0);
		
		$lines = explode(">",$got);
		$online = array();
		for($i = 0; $i < count($lines); $i++){
			list($ip,$lasttime) = explode("||",$lines[$i]);
			if($lasttime >= $time){
				array_push($online,$ip);
			}
		}
		
		$online = array_unique($online);
		$this->online = count($online);
	}
	function redir_resp($id) {
		$result = query("SELECT `resp_url` FROM `vblog_respect` WHERE `resp_id`=".(int)$id);
		if(@mysql_num_rows($result) !== 1) { return false; }
		$link = @mysql_result($result,0);
		if(empty($_SESSION['resped']) or !in_array($id,explode("|",$_SESSION['resped']))) {
			if(empty($_SESSION['resped'])) $_SESSION['resped'] = "";
			query("UPDATE `vblog_respect` SET `resp_hits`=`resp_hits`+1 WHERE `resp_id`=".(int)$id." LIMIT 1");
			$_SESSION['resped'] .= $id."|";
		}
		header("Location: ".$link);
	}
	function redir_link($link = "") {
		$link = substr($_SERVER['REQUEST_URI'],6);
#		var_dump($link);
		query("UPDATE `vblog_stats` SET `stat_num`=`stat_num`+1 WHERE `stat_id`=5 LIMIT 1");
		header("Location: ".str_replace(array("\xe2\x80\x8b","%E2%80%8B"),'',$link));
	}
}

?>