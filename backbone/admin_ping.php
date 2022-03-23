<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function get_ping_urls() {
	$result = query("SELECT * FROM `vblog_pings`");
	$array = array();
	for($i = 0; $i < @mysql_num_rows($result); $i++) {
		$array[] = @mysql_fetch_array($result);
	}
	return $array;
}
function increase_ping($id) {
	$result = query("UPDATE `vblog_pings` SET `ping_ping`=`ping_ping`+1 WHERE `ping_id`=".(int)$id);
	return $result;
}
function ping($debug = false) {
	global $blog;
	$urls = get_ping_urls();
	$content = "";
	foreach($urls as $data):
#	$purl = ($which == 1) ? "http://ping.blogsearch.hu/" : "http://rpc.pingomatic.com";	
		$url = $data['ping_url'];
		$url = parse_url($url);
		$purl = $url['host'];
		$path = !empty($url['path']) ? $url['path'] : "/";
		$port = !empty($url['port']) ? $url['port'] : 80;
		$r = "\r\n";
		$xml = "<"."?xml version=\"1.0\" encoding=\"utf-8\"?".">\n";
		$mtd = ($data['ping_mode'] == 1) ? "extendedPing" : "ping";
		$xtd = ($data['ping_mode'] == 1) ? "<param><value><string>{$blog['url']}rss/</string></value></param>" : "";
		$xml .= <<<EOD
<methodCall>
 <methodName>weblogUpdates.{$mtd}</methodName>
 <params>
  <param><value><string>{$blog['title']}</string></value></param>
  <param><value><string>{$blog['url']}</string></value></param>
  {$xtd}
 </params>
</methodCall>
EOD;
		$request  = "POST ".$path." HTTP/1.1".$r;
		$request .= "Host: ".$purl.$r;
		$request .= "Content-Type: text/xml".$r;
		$request .= "User-Agent: vBlog".$r;
		$request .= "Content-length: ".strlen($xml).$r.$r;
		$request .= $xml;
		$fp = fsockopen($purl,$port);
		if(!$fp) return __LINE__;
		fputs($fp,$request);
		$fl = false;
		$gh = true;
		while (!feof($fp)) {
			$line = fgets($fp, 4096);
			if (!$fl) {
				if (strstr($line, '200') === false) continue(2);
				$fl = true;
			}
			if (trim($line) == '') {
				$gh = false;
			}
			if (!$gh) {
				$content .= trim($line)."\n";
			}
		}
		$content .= "\n";
		increase_ping($data['ping_id']);
	endforeach;
	if($debug) var_dump("<code>".nl2br(htmlentities($content))."</code>");
	return true;
}
?>