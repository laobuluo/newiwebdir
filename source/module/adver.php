<?php
/** adver list */
function get_adver_list($where = 1, $field = 'adver_id', $order = 'DESC', $start = 0, $pagesize = 0) {
	global $DB;
	
	$sql = "SELECT adver_id, adver_name, adver_code, adver_etips, adver_days, adver_time FROM ".$DB->table('advers')." WHERE $where ORDER BY $field $order LIMIT $start, $pagesize";
	$results = $DB->fetch_all($sql);
	
	return $results;
}
	
/** one adver */
function get_one_adver($adver_id) {
	global $DB;
	
	$sql = "SELECT adver_id, adver_name, adver_code, adver_etips, adver_days, adver_time FROM ".$DB->table('advers')." WHERE adver_id='$adver_id' LIMIT 1";
	$row = $DB->fetch_one($sql);
	
	return $row;
}

/** advers*/
function get_advers() {
	global $DB;
	
	$sql = "SELECT adver_id, adver_name, adver_code, adver_etips, adver_days, adver_time FROM ".$DB->table('advers')." ORDER BY adver_id ASC";
	$query = $DB->query($sql);
	$advers = array();
	while ($row = $DB->fetch_array($query)) {
		$advers[$row['adver_name']] = $row;
	}
	$DB->free_result($query);
		
	return $advers;
}

/** type ads */
function get_adver($name) {
	global $DB;
	
	$advers = load_cache('advers') ? load_cache('advers') : get_advers();
	if (is_array($advers) && !empty($advers)) {
		$row = $advers[$name];
		if (!$row) {
			return '';
		} else {
			$endtime = $row['adver_time'] + $row['adver_days'] * 24 * 3600;
			if ($endtime > $row['adver_time']) {
				return $row['adver_etips'];	
			} else {
				return $row['adver_code'];	
			}
		}
	}
}
?>