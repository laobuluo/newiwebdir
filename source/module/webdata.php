<?php
/** ip list */
function get_webip_list($top_num = 30) {
	global $DB;
	
	$sql = "SELECT web_ip FROM ".$DB->table('webdata')." WHERE web_ip!=0 ORDER BY web_id DESC LIMIT $top_num";
	$query = $DB->query($sql);
	$websites = array();
	while ($row = $DB->fetch_array($query)) {
		$row['web_ip'] = long2ip($row['web_ip']);
		$row['ip_link'] = get_location_url($row['web_ip']);
		$websites[] = $row;
	}
	$DB->free_result($query);
	
	return $websites;
}

/** index info */
function get_index_info($web_id = 0) {
	global $DB;
	
	$row = $DB->fetch_one("SELECT index_baidu, index_google, index_soso, index_sogou, index_360so, index_youdao FROM ".$DB->table('index')." WHERE web_id='$web_id' LIMIT 1");
	
	return $row;
}

/** backlink info */
function get_backlink_info($web_id = 0) {
	global $DB;
	
	$row = $DB->fetch_one("SELECT blink_baidu, blink_google, blink_soso, blink_sogou, blink_360so, blink_youdao FROM ".$DB->table('backlink')." WHERE web_id='$web_id' LIMIT 1");
	
	return $row;
}

/** whois info */
function get_whois_info($web_id = 0) {
	global $DB;
	
	$row = $DB->fetch_one("SELECT domain_status, domain_regtime, domain_exptime, domain_registrar, name_server FROM ".$DB->table('whois')." WHERE web_id='$web_id' LIMIT 1");
	$row['domain_regtime'] = date('Y年m月d日', $row['domain_regtime']);
	$row['domain_exptime'] = date('Y年m月d日', $row['domain_exptime']);
	
	return $row;
}

/** icp info */
function get_icp_info($web_id = 0) {
	global $DB;
	
	$row = $DB->fetch_one("SELECT icp_num, icp_type, icp_name, icp_time FROM ".$DB->table('icpinfo')." WHERE web_id='$web_id' LIMIT 1");
	
	return $row;
}
?>