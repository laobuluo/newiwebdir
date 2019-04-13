<?php
if (!defined('IN_HANFOX')) exit('Access Denied');
header('Content-type: application/x-javascript');

require(CORE_PATH.'include/seodata.php');

$type = trim($_GET['type']);
$web_id = intval($_GET['wid']);

if (in_array($type, array('ip', 'brank', 'grank', 'srank', 'arank', 'outstat', 'clink', 'error'))) {
	$where = "w.web_id='".$web_id."'";
	$web = get_one_website($where);
	if (!$web) {
		exit();
	}
	
	$update_cycle = time() + (3600 * 24 * $options['update_cycle']);
	$update_time = time();
	if ($web['web_utime'] < $update_cycle) {
		$DB->query("UPDATE ".$DB->table('webdata')." SET web_utime='$update_time' WHERE web_id='".$web['web_id']."'");
		#server ip
		if ($type == 'ip') {
			$ip = get_server_ip($web['web_url']);
			$ip = sprintf("%u", ip2long($ip));
			$DB->query("UPDATE ".$DB->table('webdata')." SET web_ip='$ip' WHERE web_id='".$web['web_id']."'");
		}
		
		#baidu rank
		if ($type == 'grank') {
			$rank = get_baidu_rank($web['web_url']);
			$DB->query("UPDATE ".$DB->table('webdata')." SET web_brank='$rank' WHERE web_id='".$web['web_id']."'");	
		}
	
		#google pagerank
		if ($type == 'grank') {
			 $rank = get_google_rank($web['web_url']);
			 $DB->query("UPDATE ".$DB->table('webdata')." SET web_grank='$rank' WHERE web_id='".$web['web_id']."'");
		}
		
		#sogou rank
		if ($type == 'srank') {
			$rank = get_sogou_rank($web['web_url']);
			$DB->query("UPDATE ".$DB->table('webdata')." SET web_srank='$rank' WHERE web_id='".$web['web_id']."'");
		}
		
		#alexa
		if ($type == 'arank') {
			$rank = get_alexa_rank($web['web_url']);
			$DB->query("UPDATE ".$DB->table('webdata')." SET web_arank='$rank' WHERE web_id='".$web['web_id']."'");
		}
		
		#check link
		if ($type == 'clink') {
			if ($web['web_ispay'] == 0) {
				$content = get_url_content($web['web_url']);
				if (!empty($content)) {
					if (!preg_match('/<a(.*?)href=([\'\"]?)http:\/\/'.$options['check_linkname'].'([\/]?)([\'\"]?)(.*?)>'.$options['check_linkurl'].'<\/a>/i', $content)) {
						$DB->query("UPDATE ".$DB->table('websites')." SET web_islink=1 WHERE web_id='".$web['web_id']."'");
					} else {
						$DB->query("UPDATE ".$DB->table('websites')." SET web_islink=0 WHERE web_id='".$web['web_id']."'");
					}
				}
			}
		}
	}
	
	#outstat
	if ($type == 'outstat') {
		$DB->query("UPDATE ".$DB->table('webdata')." SET web_outstat=web_outstat+1, web_otime=".time()." WHERE web_id='".$web['web_id']."'");
	}
	
	#error
	if ($type == 'error') {
		$DB->query("UPDATE ".$DB->table('webdata')." SET web_errors=web_errors+1, web_utime=".time()." WHERE web_id='".$web['web_id']."'");
	}
}
?>