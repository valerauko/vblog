<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function resp_link($id = 0, $name = '') {
	if((int)$id < 1) return '';
	$result = query("select * from `vblog_respect` where `resp_id`=".(int)$id);
	$ra = @mysql_fetch_row($result);
	$return = "<a href=\"".$GLOBALS['blog']['url']."resp/".(int)$ra[0]."/".format($ra[1],'url')."/\" title=\"".dequote(format($ra[3],'text'))."\"".((!empty($ra[4])) ? " rel=\"".format($ra[4],'text')."\"" : "").">".format((empty($name) ? $ra[3] : $name),'text')."</a>";
	return $return;
}
$mb_search = array();
function make_utf_to_ascii() {
	$h = array(0,1,2,3,4,5,6,7,8,9,'a','b','c','d','e','f');
	$c = array('c3','c4','c5');
	$f = array('á','é','í','ó','ö','ú','ü');
	$r = array('a','e','i','o','o','u','u');
	foreach($f as $k) $k = mb_convert_encoding($k,'utf-8');
	foreach($r as $k) $k = mb_convert_encoding($k,'utf-8');
	
	for($i=0;$i<3;$i++) {
		for($j=8;$j<12;$j++) {
			for($k=0;$k<16;$k++) {
				$f[] = chr((int)"0x{$c[$i]}0x{$h[$j]}0x{$h[$k]}");
				if($i == 0) {
					if($j == 8 or $j == 10) {
						if($k < 7) $r[] = 'a';
						elseif($k < 8) $r[] = 'c';
						elseif($k < 11) $r[] = 'e';
						elseif($k < 16) $r[] = 'i';
					} elseif($j == 9 or $j == 11) {
						if($k < 1) $r[] = 'd';
						elseif($k < 2) $r[] = 'n';
						elseif($k < 7 or $k == 8) $r[] = 'o';
						elseif($k < 13 and $k != 7) $r[] = 'u';
						elseif($k == 13 or $k == 15) $r[] = 'y';
						elseif($k == 7) $r[] = '-';
					}
				} elseif($i == 1) {
					if($j == 8) {
						if($k < 6) $r[] = 'a';
						elseif($k < 14) $r[] = 'c';
						else $r[] = 'd';
					} elseif($j == 9) {
						if($k < 2) $r[] = 'd';
						elseif($k < 12) $r[] = 'e';
						else $r[] = 'g';
					} elseif($j == 10) {
						if($k < 4) $r[] = 'g';
						elseif($k < 8) $r[] = 'h';
						else $r[] = 'i';
					} elseif($j == 11) {
						if($k < 2) $r[] = 'i';
						elseif($k < 6) $r[] = 'j';
						elseif($k < 9) $r[] = 'k';
						else $r[] = 'l';
					}	
				} else {
					if($j == 8) {
						if($k < 3) $r[] = 'l';
						elseif($k < 12) $r[] = 'n';
						else $r[] = 'o';
					} elseif($j == 9) {
						if($k < 4) $r[] = 'o';
						elseif($k < 10) $r[] = 'r';
						else $r[] = 's';
					} elseif($j == 10) {
						if($k < 2) $r[] = 's';
						elseif($k < 8) $r[] = 't';
						else $r[] = 'u';
					} elseif($j == 11) {
						if($k < 4) $r[] = 'u';
						elseif($k < 6) $r[] = 'w';
						elseif($k < 8) $r[] = 'y';
						elseif($k < 15) $r[] = 'z';
						else $r[] = 's';
					}
				}
			}
		}
	}
	$GLOBALS['mb_search'] = array($f,$r);
}
$mb_search = array(
	array("ä","á","ë","é","í","ó","ö","ő","ú","ü","ű","ÿ"),
	array("a","a","ë","e","i","o","o","o","u","u","u","y")
	);
#make_utf_to_ascii();
	function urlSafe($str = '') {
		$find = array('Á','Ä','Â','À','Å','Ā','Ã','á','ä','â','à','å','ā','ã','É','È','Ë','Ê','Ē','é','è','ë','ê','ē','Í','Ì','Ï','Ī','Î','í','ì','ï','ī','î','Ó','Ò','Ø','Ö','Ő','Õ','Ô','Ō','ó','ò','ø','ö','ő','õ','ô','ō','Ú','Ù','Ü','Ű','Ũ','Ū','ú','ù','ű','ü','ũ','ū');
		$repl = array('a','a','a','a','a','a','a','a','a','a','a','a','a','a','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','u','u','u','u');
		return preg_replace(array("'([^a-z0-9]+)'","'(\-)+'"),array('-','-'),strtolower(str_replace($find,$repl,$str)));
	}
function format($str,$mode="text") {
	switch($mode):
		case "db": /* database */
#			$str = mb_strtolower($str);
			$arr = array(
				"\r",
				'\\',
				"\x00",
				"\x1a",
				'|',
				'--',
				"'",
				'"',
				'='
				);
			$rep = array(
				'',
				'',
				'',
				'',
				'&#124;',
				'&#045;&#045;',
				'&#039;',
				'&quot;',
				'&#061;'
				);
			$str = str_replace($arr,$rep,$str);
			break;
		case "full": /* bbcode */
			$find = array(
				"'<'",
				"'>'",
				"'(\n)'mis",
				"'(\r)*'mis",
				"'\|'",
				"'\{blog\}'i",
				"'\[b\](.*?)\[/b\]'i",
				"'\[i\](.*?)\[/i\]'i",
				"'\[u\](.*?)\[/u\]'i",
				'~(?!<[^>]*)&quot;(.*?)&quot;(?![^<]*>)~',
				"'\[resp&#(0)?61;([0-9]+)\](.*?)\[/resp\]'ie",
				"'\[link\](.*?)\[/link\]'i",
				"'\[link&#(0)?61;(.*?)\](.*?)\[/link\]'i",
				"'\[inner&#(0)?61;(.*?)\](.*?)\[/inner\]'i",
				"'\[color&#(0)?61;(.*?)\](.*?)\[/color\]'i",
				"'\[size&#(0)?61;(.*?)\](.*?)\[/size\]'i",
				"'\[font&#(0)?61;(.*?)\](.*?)\[/font\]'i",
				"'\[align&#(0)?61;(.*?)\](.*?)\[/align\]'i",
				"'\[img\](.*?)\[/img\]'i",
				"'\[q\](.*?)\[/q\]'mi",
				"'\[quote\](.*?)\[/quote\](<br/>)*(\n)*'mi",
				"'\[quote&#(0)?61;(.*?)\](.*?)\[/quote\](<br/>)*(\n)*'mi",
				"'\[list\](<br/>)*(\n)*(\r)*(.*?)\[/list\](<br/>)*(\n)*(\r)*'mis",
				"'\[\*\](.*?)\[/\*\](<br/>)*(\n)*'mis",
				"'\[c\](<br/>)*(\n)*(\r)*(.*?)\[/c\](<br/>)*(\n)*(\r)*'mis",
				"'\[col&#(0)?61;([0-9]{1,2})\](.*?)\[/col\](<br/>)*(\n)*'mis",
				"'\[h\](.*?)\[/h\]'mis",
				"'\[sup\](.*?)\[/sup\]'mis",
				"'\[sub\](.*?)\[/sub\]'mis",
				"'\[ov\](.*?)\[/ov\]'mis",
				"'\[jp\](.*?)\[/jp\](<br/>)*(\n)*'mis",
				"'\[en\](.*?)\[/en\](<br/>)*(\n)*'mis",
				"'\[acr title&#(0)?61;(.*?)\](.*?)\[/acr\]'i",
				"'\[acr&#(0)?61;(.*?)\](.*?)\[/acr\]'i",
				"'\[bq\](.*?)\[/bq\](<br/>)*(\n)*'mis",
				"'\[bq&#(0)?61;(.*?)\](.*?)\[/bq\](<br/>)*(\n)*'mis",
				"'\[devfav&#(0)?61;(.*?)\](.*?)\[/devfav\](<br/>)*(\n)*'mis",
				"'\[code\](.*?)\[/code\](<br/>)*(\n)*'mis",
				"'\[re&#(0)?61;([0-9]+)\](.*?)\[/re\]'mis",
				"'\[edit\](.*?)\[/edit\](<br/>)*(\n)*'is",
				"'\[tab\](.*?)\[/tab\]'mis",
				"'\[tab&#(0)?61;([0-9]+)\](.*?)\[/tab\]'mis",
	#			"'([0-9]+)(st|nd|rd|th)'i",
				"'(\s+)&#045;&#045;(\s+)'",
				"'(\s+)&#045;(\s+)'",
				"'&#045;&#045;'",
				"'(\w{7,})\-(\w{7,})'",
				"'\.\.\.'",
#				"'^\	'",
#				"'(&otilde;|&#245;)'",
#				"'(&Otilde;|&#213;)'",
#				"'(&ucirc;|&#251;)'",
#				"'(&Ucirc;|&#219;)'",
				"'&#(0)?61;'",
				"'&#(0)?39;'"
				);
			$replace = array(
				"&lt;",
				"&gt;",
				"<br/>",
				"",
				"&#124;",
				$GLOBALS['blog']['url'],
				"<strong>\\1</strong>",
				"<em>\\1</em>",
				"<span style=\"text-decoration:underline;\">\\1</span>",
				"<q>\\1</q>",
				"resp_link(\\2,'\\3')", # <a href=\"".$GLOBALS['blog']['url']."resp/\\2\">\\3</a> # rel=\"'.print_rel(\\2).'\"
				"<a href=\"".$GLOBALS['blog']['url']."link/\\1\">\\1</a>",
				"<a href=\"".$GLOBALS['blog']['url']."link/\\2\">\\3</a>",
				"<a href=\"".$GLOBALS['blog']['url']."\\2\" class=\"inner\">\\3</a>",
				"<span style=\"color: \\2;\">\\3</span>",
				"<span style=\"font-size: \\2;\">\\3</span>",
				"<span style=\"font-family: \\2;\">\\3</span>",
				"<div style=\"text-align: \\2\">\\3</div>",
				"<a href=\"".$GLOBALS['blog']['url']."uploads/files/\\1\" class=\"file\"><img src=\"".$GLOBALS['blog']['url']."uploads/files/show/\\1\" alt=\"Image\" title=\"\\1\" /></a>",
				"<q>\\1</q>",
				"<blockquote>\\1</blockquote>",
				"<blockquote>\\3<span class=\"from\">\\2</span></blockquote>",
				"<ul>\\4</ul>",
				"<li>\\1</li>",
				"<div class=\"c\">\\4</div>",
				"<div class=\"col\" style=\"width:\\2%\">\\3</div>",
				"<span class=\"h\">\\1</span>",
				"<sup>\\1</sup>",
				"<sub>\\1</sub>",
				"<span class=\"over\">\\1</span>",
				"<span xml:lang=\"ja\">\\1</span>",
				"<span xml:lang=\"en\">\\1</span>",
				"<acronym title=\"\\2\">\\3</acronym>",
				"<acronym title=\"\\2\">\\3</acronym>",
				"<div class=\"bq\">\\1</div>",
				"<div class=\"bq\">\\3<div class=\"bqfrom\">\\2</div></div>",
				"<a href=\"".$GLOBALS['blog']['url']."link/\\2\" class=\"file\"><img src=\"".$GLOBALS['blog']['url']."devfav/\\3\" alt=\"Deviant Favourite \\3\" title=\"\\2\" /></a><br />",
				"<code class=\"bq\" style=\"text-align:left !important; display:block;\">\\1</code>",
				"<a href=\"#comment_\\2\">\\3</a>",
				"<p class=\"edit\">Edit \\1</p>",
				"<span style=\"padding-left:10px\">\\1</span>",
				"<span style=\"padding-left:\\2px\">\\3</span>",
	#			"\\1<sup>\\2</sup>",
				"\\1&ndash;\\2",
				"\\1&ndash;\\2",
				"&mdash;",
				"\\1-\xe2\x80\x8b\\2",
				"&hellip;",
#				"<pre>".chr(9)."</pre>",
#				"&#337;",
#				"&#336;",
#				"&#369;",
#				"&#368;",
				"=",
				"'"
				);
			$str = preg_replace($find,$replace,$str);
			$str = html_entity_decode($str);
			$str = strip_tags($str,"<h3>,<h4>,<h5>,<h6>,<p>,<div>,<pre>,<code>,<span>,<acronym>,<strike>,<blockquote>,<q>,<strong>,<em>,<a>,<ul>,<li>,<sub>,<sup>,<img>,<img/>,<br>,<br/>,<ins>,<del>,<table>,<thead>,<tbody>,<tr>,<th>,<td>");
			$str = str_replace(array("#gt.","#lt.","<"."?","?".">"),array(">","<","&lt;?","?&gt;"),$str);
			break;

		case "url": /* url free *//*
			global $mb_search;
			$str = mb_convert_encoding($str,'utf-8');
			$str = mb_strtolower($str);
global $k;
$t = $str;
			$str = str_replace($mb_search[0],$mb_search[1],$str);
$k[] = array($t,$str);
#			$str = mb_convert_encoding($str,'ISO-8859-1');
#			$result = query("SELECT `set_value` FROM `vblog_settings` WHERE `set_id`='tmp_charfix'");
#			$row = @mysql_result($result,0);
#			$row = explode('|',$row);
#			$str = strtr($row[0],$row[1],$str);
#			$str = strtr($str,"áä"."éë"."í"."óöő"."úüű","aa"."ee"."i"."ooo"."uuu");
#			$str = str_replace(array('á','ä','é','í','ó','ú'),array('a','a','e','i','o','u'),$str);
#			$t = get_html_translation_table(HTMLENTITIES);
#			$str = strtr($str,$t,$t);
#			$str = mb_convert_encoding($str,'ascii','utf-8');
			$str = htmlentities($str);
			$rex = array(
				"'(\&|\&amp;)(.)(acute|circ|grave|tilde|uml|slash|cedil);'mis",
				"'(\&|\&amp;)#(336|337|213|245);'mis",
				"'(\&|\&amp;)#(368|369|219|251);'mis",
				"'(\&|\&amp;)[A-Za-z]+[0-9]*;'mis",
				"'([^A-Za-z0-9-]|(\&|\&amp;)#[0-9]+;)'mis",
				"'(-{2,})'",
				"'(-)$'",
				"'(\W+)'"
				);
			$rep = array(
				"\\2",
				"o",
				"u",
				"",
				"-",
				"-",
				"",
				'-'
				);

			$str = preg_replace($rex,$rep,$str);
			$str = strip_tags($str);*/
			$str = urlSafe($str);
			break;
		case "text": /* text only */
		default:
			$find = array(
				"'<'",
				"'>'",
				',",',
				"'\n'i",
				"'\[b\](.*?)\[/b\]'i",
				"'\[i\](.*?)\[/i\]'i",
				"'\[u\](.*?)\[/u\]'i",
				"'\[acr title&#61;(.*?)\](.*?)\[/acr\]'i",
				"'\[acr&#61;(.*?)\](.*?)\[/acr\]'i",
				"'\[h\](.*?)\[/h\]\n?'mis",
				"'\[sup\](.*?)\[/sup\]\n?'mis",
				"'\[sub\](.*?)\[/sub\]\n?'mis",
				"'\[ov\](.*?)\[/ov\]\n?'mis",
				"'(&otilde;|&#245;)'",
				"'(&Otilde;|&#213;)'",
				"'(&ucirc;|&#251;)'",
				"'(&Ucirc;|&#219;)'"
				);
			$replace = array(
				"&lt;",
				"&gt;",
				"&quot;",
				"",
				"<strong>\\1</strong>",
				"<em>\\1</em>",
				"<span style=\"text-decoration:underline;\">\\1</span>",
				"<span class=\"acr\" title=\"\\1\">\\2</span>",
				"<span class=\"acr\" title=\"\\1\">\\2</span>",
				"<span class=\"holy\">\\1</span>",
				"<sup>\\1</sup>",
				"<sub>\\1</sub>",
				"<span class=\"over\">\\1</span>",
				"&#337;",
				"&#336;",
				"&#369;",
				"&#368;"
				);
			$str = preg_replace($find,$replace,$str);
#			$str = html_entity_decode($str);
			$str = strip_tags($str,"<span>,<strong>,<em>,<sup>,<sub>,<q>");
			break;
	endswitch;
	return $str;
}
function dequote($str) {
	return str_replace('"',"&quot;",$str);
}
function decode_unicode_url($str = "") {
	if(!$str) return false;
	$res = '';
	
	$i = 0;
	$max = strlen($str) - 6;
	while ($i <= $max) {
		$character = $str[$i];
		if ($character == '%' && $str[$i + 1] == 'u') {
			$value = hexdec(substr($str, $i + 2, 4));
			$i += 6;
			if ($value < 0x0080) // 1 byte: 0xxxxxxx
				$character = chr($value);
			else if ($value < 0x0800) // 2 bytes: 110xxxxx 10xxxxxx
				$character = chr((($value & 0x07c0) >> 6) | 0xc0)
					. chr(($value & 0x3f) | 0x80);
			else // 3 bytes: 1110xxxx 10xxxxxx 10xxxxxx
				$character = chr((($value & 0xf000) >> 12) | 0xe0)
					. chr((($value & 0x0fc0) >> 6) | 0x80)
					. chr(($value & 0x3f) | 0x80);
		} else {
			$i++;
		}
		$res .= $character;
	}
	return $res . substr($str, $i);
}
function short($str,$num) {
	if($num > mb_strlen($str)) {
		return $str;
	} elseif($num < 5) {
		$num = 5;
	}
	$str = mb_substr($str,0,$num+3);
	$space = mb_strrpos($str," ");
	$dash = mb_strrpos($str,"-");
	$last = ($space > $dash) ? $space : $dash;
	if($last >= $num-7 and $last <= $num+2) {
		$str = mb_substr($str,0,$last);
	} else {
		$str = mb_substr($str,0,$num);
	}
	$str .= "...";
	return $str;
}

?>