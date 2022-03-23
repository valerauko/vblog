<?php
if(!defined('vblog')) { header("Location: http://blog.valerauko.net/"); }

function pager($pnum,$page,$link) {
	if(substr($link,-1,1) != "/") { $link = $link."/"; }
	if($pnum > 1) {
		$pager = "<div class=\"pager\"><ul>";
		if($pnum > 10) {
			if($page > 1) {
				$pager .= "<li><a href=\"".$link."page/1/\" title=\"First page\">&laquo;</a></li>";
				$pager .= "<li><a href=\"".$link."page/".($page-1)."/\" title=\"Previous page\" rel=\"prev\">&lsaquo;</a></li>";
			} else {
				$pager .= "<li>&laquo;</li><li>&lsaquo;</li>";
			}
			switch($page):
				case 1:
					$pager .= "<li>".(int)$page."</li>";
					for($i = $page+1; $i <= $page+5; $i++) {
						$pager .= "<li><a href=\"".$link."page/".$i."/\">".$i."</a></li>";
					}
					$pager .= "<li>&hellip;</li>";
					break;
				case 2:
					$pager .= "<li><a href=\"".$link."page/".($page-1)."/\">".($page-1)."</a></li>";
					$pager .= "<li>".(int)$page."</li>";
					for($i = $page+1; $i <= $page+4; $i++) {
						$pager .= "<li><a href=\"".$link."page/".$i."/\">".$i."</a></li>";
					}
					$pager .= "<li>&hellip;</li>";
					break;
				case $pnum:
					$pager .= "<li>&hellip;</li>";
					for($i = $pnum-5; $i <= $pnum-1; $i++) {
						$pager .= "<li><a href=\"".$link."page/".$i."/\">".$i."</a></li>";
					}
					$pager .= "<li>".(int)$pnum."</li>";
					break;
				case $pnum-1:
					$pager .= "<li>&hellip;</li>";
					for($i = $pnum-5; $i < $pnum-1; $i++) {
						$pager .= "<li><a href=\"".$link."page/".$i."/\">".$i."</a></li>";
					}
					$pager .= "<li>".((int)$pnum-1)."</li>";
					$pager .= "<li><a href=\"".$link."page/".$pnum."/\">".$pnum."</a></li>";
					break;
				default:
					$pager .= "<li>&hellip;</li>";
					for($i = $page-2; $i < $page+3; $i++) {
						if($i == $page) {
							$pager .= "<li>".$i."</li>";
						} else {
							$pager .= "<li><a href=\"".$link."page/".$i."/\">".$i."</a></li>";
						}
					}
					$pager .= "<li>&hellip;</li>";
			endswitch;
			if($page < $pnum) {
				$pager .= "<li><a href=\"".$link."page/".($page+1)."/\" title=\"Next page\" rel=\"next\">&rsaquo;</a></li>";
				$pager .= "<li><a href=\"".$link."page/".$pnum."/\" title=\"Last page\">&raquo;</a></li>";
			} else {
				$pager .= "<li>&rsaquo;</li><li>&raquo;</li>";
			}
		} else {
			for($i = 1; $i <= $pnum; $i++) {
				if((int)$page == $i) {
					$pager .= "<li>".$i."</li>";
				} else {
					$pager .= "<li><a href=\"".$link."page/".$i."/\">".$i."</a></li>";
				}
			}
		}
		$pager .= "</ul></div>";
	} else {
		$pager = "";
	}
	return $pager;
}
?>