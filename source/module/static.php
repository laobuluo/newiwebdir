<?php
/** write cache */
function write_cache($cache_name, $cache_data = '') {
	$cache_dir = ROOT_PATH.'data/static/';
	$cache_file = $cache_dir.$cache_name.'.php';
	
	if (!is_dir($cache_dir)) {
		@mkdir($cache_dir, 0777);
	}
	
	if ($fp = @fopen($cache_file, 'wb')) {
		@fwrite($fp, "<?php\r\n//File name: ".$cache_name.".php\r\n//Creation time: ".date('Y-m-d H:i:s')."\r\n\r\nif (!defined('IN_HANFOX')) exit('Access Denied');\r\n\r\n".$cache_data."\r\n?>");
		@fclose($fp);
		@chmod($cache_file, 0777);
	} else {
		echo 'Error: Can\'t write to '.$cache_name.' cache files, please check directory.!';
		exit;
	}
}

/** load cache */
function load_cache($cache_name) {
	static $static_data = array();
	if (!empty($cache_name)) {
		$cache_file = ROOT_PATH.'data/static/'.$cache_name.'.php';
		if (is_file($cache_file)) {
			@require($cache_file);
			return $static_data;
		} else {
			return false;
		}
	}
}

/** update cache */
function update_cache($cache_name = '') {
	$update_list = empty($cache_name) ? array() : (is_array($cache_name) ? $cache_name : array($cache_name));
	foreach ($update_list as $entry) {
		call_user_func($entry.'_cache');
	}
}

/** config cache */
function options_cache() {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('options');
	$options = $DB->fetch_all($sql);
	$contents = "\$static_data = array(\r\n";
	foreach ($options as $opt) {
		$contents .= "\t'".$opt['option_name']."' => '".$opt['option_value']."',\r\n";
	}
	$contents .= ");";
	
	write_cache('options', $contents);
}

/** adver cache */
function advers_cache() {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('advers')." ORDER BY adver_id DESC";
	$advers = $DB->fetch_all($sql);
	$contents = "\$static_data = array(\r\n";
	foreach ($advers as $ad) {
		$contents .= "\t'".$ad['adver_name']."' => array(\r\n\t\t'adver_id' => '".$ad['adver_id']."',\r\n\t\t'adver_name' => '".$ad['adver_name']."',\r\n\t\t'adver_code' => '".$ad['adver_code']."',\r\n\t\t'adver_etips' => '".$ad['adver_etips']."',\r\n\t\t'adver_days' => '".$ad['adver_days']."',\r\n\t\t'adver_time' => '".$ad['adver_time']."'\r\n\t),\r\n";
	}
	$contents .= ");";
	
	write_cache('advers', $contents);
}

/** link cache */
function links_cache() {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('links')." WHERE link_hide=1 ORDER BY link_sort ASC";
	$links = $DB->fetch_all($sql);
	$contents = "\$static_data = array(\r\n";
	foreach ($links as $link) {
		$contents .= "\t'".$link['link_id']."' => array(\r\n\t\t'link_name' => '".$link['link_name']."',\r\n\t\t'link_url' => '".$link['link_url']."',\r\n\t\t'logo_url' => '".$link['link_logo']."',\r\n\t),\r\n";
	}
	$contents .= ");";
	
	write_cache('links', $contents);
}

/** category cache */
function categories_cache() {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('categories')." ORDER BY cate_sort ASC, cate_id ASC";
	$categories = $DB->fetch_all($sql);
	$contents .= "\$static_data = array(\r\n";
	foreach ($categories as $cate) {
		$contents .= "\t'".$cate['cate_id']."' => array(\r\n\t\t'cate_id' => '".$cate['cate_id']."',\r\n\t\t'root_id' => '".$cate['root_id']."',\r\n\t\t'cate_name' => '".$cate['cate_name']."',\r\n\t\t'cate_dir' => '".$cate['cate_dir']."',\r\n\t\t'cate_url' => '".$cate['cate_url']."',\r\n\t\t'cate_isbest' => '".$cate['cate_isbest']."',\r\n\t\t'cate_keywords' => '".$cate['cate_keywords']."',\r\n\t\t'cate_description' => '".$cate['cate_description']."',\r\n\t\t'cate_arrparentid' => '".$cate['cate_arrparentid']."',\r\n\t\t'cate_arrchildid' => '".$cate['cate_arrchildid']."',\r\n\t\t'cate_childcount' => '".$cate['cate_childcount']."',\r\n\t\t'cate_postcount' => '".$cate['cate_postcount']."'\r\n\t),\r\n";
		
		$contents_1 = "\$static_data = array(\r\n";
		$contents_1 .= "\t'cate_id' => '".$cate['cate_id']."',\r\n\t'root_id' => '".$cate['root_id']."',\r\n\t'cate_name' => '".$cate['cate_name']."',\r\n\t'cate_dir' => '".$cate['cate_dir']."',\r\n\t'cate_url' => '".$cate['cate_url']."',\r\n\t'cate_isbest' => '".$cate['cate_isbest']."',\r\n\t'cate_keywords' => '".$cate['cate_keywords']."',\r\n\t'cate_description' => '".$cate['cate_description']."',\r\n\t'cate_arrparentid' => '".$cate['cate_arrparentid']."',\r\n\t'cate_arrchildid' => '".$cate['cate_arrchildid']."',\r\n\t'cate_childcount' => '".$cate['cate_childcount']."',\r\n\t'cate_postcount' => '".$cate['cate_postcount']."',\r\n";
		$contents_1 .= ");";
		
		write_cache('category_'.$cate['cate_id'], $contents_1);
	}
	$contents .= ");";
	
	write_cache('categories', $contents);
}

/** label cache */
function labels_cache() {
	global $DB;
	
	$sql = "SELECT * FROM ".$DB->table('labels');
	$labels = $DB->fetch_all($sql);
	$contents = "\$static_data = array(\r\n";
	foreach ($labels as $label) {
		$contents .= "\t'".$label['label_name']."' => '".$label['label_content']."',\r\n";
	}
	$contents .= ");";
	
	write_cache('labels', $contents);
}

/** archives cache */
function archives_cache() {
	global $DB;
	
	$sql = "SELECT web_ctime FROM ".$DB->table('websites')." WHERE web_status=3 ORDER BY web_ctime DESC";
	$temparr = $DB->fetch_all($sql);
	$archives = array();
	foreach ($temparr as $row) {
		$archives[] = date('Y-m', $row['web_ctime']);
	}
		
	$count = array_count_values($archives);
	
	foreach ($count as $key => $val) {
		list($year, $month) = explode('-', $key);
		$archives[$year][$month] = $val;
	}
	
	$contents = "\$static_data = array(\r\n";
	if (!empty($archives)) {
		foreach ($archives as $year => $arr) {
			$contents .= "\t'".$year."' => array(";
			ksort($arr);
			foreach ($arr as $month => $num) {
				$contents .= "\r\n\t\t'".$month."' => '".$num."',";
			}
			$contents .= "\r\n\t),\r\n";
		}
	}
	$contents .= ");";
	
	write_cache('archives', $contents);
}

/** stats cache */
function stats_cache() {
	global $DB;
	
	$category = $DB->get_count($DB->table('categories'));
	$website = $DB->get_count($DB->table('websites'));
	$apply = $DB->get_count($DB->table('apply'));
	$adver = $DB->get_count($DB->table('advers'));
	$link = $DB->get_count($DB->table('links'));
	$feedback = $DB->get_count($DB->table('feedbacks'));
	$label = $DB->get_count($DB->table('labels'));
	$page = $DB->get_count($DB->table('pages'));
	
	$contents = "\$static_data = array(\r\n";
	$contents .= "\t'category' => '".$category."',\r\n";
	$contents .= "\t'website' => '".$website."',\r\n";
	$contents .= "\t'apply' => '".$apply."',\r\n";
	$contents .= "\t'adver' => '".$adver."',\r\n";
	$contents .= "\t'link' => '".$link."',\r\n";
	$contents .= "\t'feedback' => '".$feedback."',\r\n";
	$contents .= "\t'label' => '".$label."',\r\n";
	$contents .= "\t'page' => '".$page."',\r\n";
	$contents .= ");";
	
	write_cache('stats', $contents);
}
?>