<?php
/** website list */
function get_websites($cate_id = 0, $top_num = 10, $is_pay = false, $is_top = false, $is_best = false, $field = 'ctime', $order = 'desc') {
	global $DB;
	
	$where = 'w.web_status=3';
	if (!in_array($field, array('instat', 'outstat', 'views', 'ctime'))) $field = 'ctime';
	if ($cate_id > 0) {
		$cate = get_one_category($cate_id);
		if (!empty($cate)) $where .= " AND w.cate_id IN (".$cate['cate_arrchildid'].")";
	}
	if ($is_pay == true) $where .= " AND w.web_ispay=1";
	if ($is_top == true) $where .= " AND w.web_istop=1";
	if ($is_best == true) $where .= " AND w.web_isbest=1";
	switch ($field) {
		case 'instat' :
			$sortby = "d.web_itime";
			break;
		case 'outstat' :
			$sortby = "d.web_otime";
			break;
		case 'views' :
			$sortby = "d.web_views";
			break;
		case 'ctime' :
			$sortby = "w.web_ctime";
			break;
		default :
			$sortby = "w.web_ctime";
			break;
	}
	$order = strtoupper($order);
	
	$sql = "SELECT w.web_id, w.web_name, w.web_url, w.web_intro, w.web_ctime, c.cate_id, c.cate_name, d.web_grank, d.web_srank, d.web_arank, d.web_instat, d.web_outstat, d.web_views FROM ".$DB->table('websites')." w LEFT JOIN ".$DB->table('categories')." c ON w.cate_id=c.cate_id LEFT JOIN ".$DB->table('webdata')." d ON w.web_id=d.web_id WHERE $where ORDER BY $sortby $order LIMIT $top_num";
	$query = $DB->query($sql);
	$websites = array();
	while ($row = $DB->fetch_array($query)) {
		$row['full_url'] = format_url($row['web_url']);
		$row['web_thumb'] = get_webthumb($row['web_url']);
		$row['web_tags'] = get_format_tags($row['web_tags']);
		$row['web_arank'] = number_format($row['web_arank']);
		$row['web_ctime'] = get_format_time($row['web_ctime']);
		$row['web_link'] = get_siteinfo_url($row['web_url']);
		$row['cate_link'] = get_category_url($row['cate_id']);
		$websites[] = $row;
	}
	$DB->free_result($query);
	
	return $websites;
}

/** website list */
function get_website_list($where = 1, $field = 'ctime', $order = 'DESC', $start = 0, $pagesize = 0) {
	global $DB;
	
	if (!in_array($field, array('instat', 'outstat', 'views', 'ctime'))) $field = 'ctime';
	switch ($field) {
		case 'instat' :
			$sortby = "d.web_instat";
			break;
		case 'outstat' :
			$sortby = "d.web_outstat";
			break;
		case 'views' :
			$sortby = "d.web_views";
			break;
		case 'ctime' :
			$sortby = "w.web_ctime";
			break;
		default :
			$sortby = "w.web_ctime";
			break;
	}
	$order = strtoupper($order);
	$sql = "SELECT w.web_id, w.web_name, w.web_url, w.web_title, w.web_intro, w.web_status, w.web_ctime, c.cate_name, d.web_ip, d.web_grank, d.web_srank, d.web_arank, d.web_instat, d.web_outstat, d.web_views, d.web_utime FROM ".$DB->table('websites')." w LEFT JOIN ".$DB->table('categories')." c ON w.cate_id=c.cate_id LEFT JOIN ".$DB->table('webdata')." d ON w.web_id=d.web_id WHERE $where ORDER BY w.web_istop DESC, $sortby $order LIMIT $start, $pagesize";
	$query = $DB->query($sql);
	$websites = array();
	while ($row = $DB->fetch_array($query)) {
		switch ($row['web_status']) {
			case 1 :
				$status = '黑名单';
				break;
			case 2 :
				$status = '待审核';
				break;
			case 3 :
				$status = '已审核';
				break;
		}
		$row['full_url'] = format_url($row['web_url']);
		$row['web_thumb'] = get_webthumb($row['web_url']);
		$row['web_status'] = $status;
		$row['web_ctime'] = get_format_time($row['web_ctime']);
		$row['web_arank'] = number_format($row['web_arank']);
		$row['web_utime'] = get_format_time($row['web_utime']);
		$row['web_link'] = get_siteinfo_url($row['web_url']);
		$websites[] = $row;
	}
	$DB->free_result($query);
		
	return $websites;
}
	
/** one website */
function get_one_website($where = 1) {
	global $DB;
	
	$row = $DB->fetch_one("SELECT w.cate_id, w.web_id, w.web_name, w.web_url, w.web_title, w.web_tags, w.web_intro, w.web_ispay, w.web_istop, w.web_isbest, w.web_status, w.web_ctime, d.web_ip, d.web_brank, d.web_grank, d.web_srank, d.web_arank, d.web_instat, d.web_outstat, d.web_voter, d.web_score, d.web_views, d.web_utime, c.cate_id, c.cate_name, c.cate_arrparentid FROM ".$DB->table('websites')." w LEFT JOIN ".$DB->table("webdata")." d ON w.web_id=d.web_id LEFT JOIN ".$DB->table('categories')." c ON w.cate_id=c.cate_id WHERE $where LIMIT 1");
	
	return $row;
}

/** website relateds */
function get_website_relateds($cate_id = 0, $web_id = 0, $top_num = 10) {
	global $DB;
	
	$sql = "SELECT web_id, web_name, web_url, web_title, web_intro, web_ctime FROM ".$DB->table('websites')." WHERE web_status=3 AND cate_id='$cate_id' AND web_id<>'$web_id' ORDER BY web_id ASC LIMIT $top_num";
	$query = $DB->query($sql);
	$websites = array();
	while ($row = $DB->fetch_array($query)) {
		$row['full_url'] = format_url($row['web_url']);
		$row['web_thumb'] = get_webthumb($row['web_url']);
		$row['web_ctime'] = get_format_time($row['web_ctime']);
		$row['web_link'] = get_siteinfo_url($row['web_url']);
		$websites[] = $row;
	}
	$DB->free_result($query);
	
	return $websites;
}

/** website comments */
function get_website_comments($web_id = 0, $top_num = 10) {
	global $DB;
	
	$sql = "SELECT com_id, web_id, root_id, com_nick, com_email, com_text, com_ip, com_time FROM ".$DB->table('comments')." WHERE com_status=1 AND root_id=0 AND web_id='$web_id' ORDER BY com_id DESC";
	if ($top_num > 0) $sql .= " LIMIT $top_num";
	$query = $DB->query($sql);
	$comments = array();
	while ($row = $DB->fetch_array($query)) {
		$count = get_comment_count($row['com_id'], $row['web_id']);
		if ($count > 0) {
			$row['reply_comments'] = get_comments($row['com_id'], $row['web_id']);
		}
		$row['com_ip'] = long2ip($row['com_ip']);
		$row['com_time'] = get_format_time($row['com_time']);
		$comments[] = $row;
	}
	$DB->free_result($query);
	
	return $comments;
}

/** url list */
function get_weburl_list($top_num = 30) {
	global $DB;
	
	$sql = "SELECT web_url FROM ".$DB->table('websites')." WHERE web_status=3 ORDER BY web_id DESC LIMIT $top_num";
	$query = $DB->query($sql);
	$websites = array();
	while ($row = $DB->fetch_array($query)) {
		$row['web_link'] = get_whois_url($row['web_url']);
		$websites[] = $row;
	}
	$DB->free_result($query);
	
	return $websites;
}

/** rssfeed */
function get_website_rssfeed($cate_id = 0) {
	global $DB, $options;
		
	$where = "w.web_status=3";
	$cate = get_one_category($cate_id);
	if (!empty($cate)) $where .= " AND c.cate_id IN (".$cate['cate_arrchildid'].")";

	$sql = "SELECT w.web_id, w.cate_id, w.web_name, w.web_url, w.web_intro, w.web_ctime, c.cate_name FROM ".$DB->table('websites')." w LEFT JOIN ".$DB->table('categories')." c ON w.cate_id=c.cate_id";
	$sql .= " WHERE $where ORDER BY w.web_id DESC LIMIT 50";
	$query = $DB->query($sql);
	$websites = array();
	while ($row = $DB->fetch_array($query)) {
		$row['web_link'] = str_replace('&', '&amp;', get_siteinfo_url($row['web_url']));
		$row['web_intro'] = htmlspecialchars(strip_tags($row['web_intro']));
		$row['web_ctime'] = date('Y-m-d H:i:s', $row['web_ctime']);
		$websites[] = $row;
	}
	$DB->free_result($query);
		
	header("Content-Type: application/xml;");
	echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
	echo "<rss version=\"2.0\">\n";
	echo "<channel>\n";
	echo "<title>".$options['site_name']."</title>\n";
	echo "<link>".$options['site_url']."</link>\n";
	echo "<description>".$options['site_description']."</description>\n";
	echo "<language>zh-cn</language>\n";
	echo "<copyright><!--CDATA[".$options['site_copyright']."]--></copyright>\n";
	echo "<webmaster>".$options['site_name']."</webmaster>\n";
	echo "<generator>".$options['site_name']."</generator>\n";
	echo "<image>\n";
	echo "<title>".$options['site_name']."</title>\n";
	echo "<url>".$options['site_url']."logo.gif</url>\n";
	echo "<link>".$options['site_url']."</link>\n";
	echo "<description>".$options['site_description']."</description>\n";
	echo "</image>\n";
	
	foreach ($websites as $row) {
		echo "<item>\n";
		echo "<link>".$row['web_link']."</link>\n";
		echo "<title>".$row['web_name']."</title>\n";
		echo "<author>".$options['site_name']."</author>\n";
		echo "<category>".$row['cate_name']."</category>\n";
		echo "<pubDate>".$row['web_ctime']."</pubDate>\n";
		echo "<guid>".$row['web_link']."</guid>\n";
		echo "<description>".$row['web_intro']."</description>\n";
		echo "</item>\n";
	}
	echo "</channel>\n";
	echo "</rss>";
}
	
/** sitemap */
function get_website_sitemap($cate_id = 0) {
	global $DB, $options;
	
	$where = "web_status=3";
	$cate = get_one_category($cate_id);
	if (!empty($cate)) {
		if ($cate['cate_childcount'] > 0) {
			$where .= " AND cate_id IN (".$cate['cate_arrchildid'].")";
		} else {
			$where .= " AND cate_id=$cate_id";
		}
	}

	$sql = "SELECT web_id, web_url, web_ctime FROM ".$DB->table('websites');
	$sql .= " WHERE $where ORDER BY web_id DESC LIMIT 50";
	$query = $DB->query($sql);
	$websites = array();
	while ($row = $DB->fetch_array($query)) {
		$row['web_link'] = str_replace('&', '&amp;', get_siteinfo_url($row['web_url']));
		$row['web_ctime'] = date('Y-m-d H:i:s', $row['web_ctime']);
		$websites[] = $row;
	}
	$DB->free_result($query);
	
	header("Content-Type: application/xml;");
	echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
	echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
	echo "<url>\n";
	echo "<loc>".$options['site_url']."</loc>\n";
	echo "<lastmod>".iso8601('Y-m-d\TH:i:s\Z')."</lastmod>\n";
	echo "<changefreq>always</changefreq>\n";
	echo "<priority>0.9</priority>\n";
	echo "</url>\n";
	
	$now = time();
	foreach ($websites as $row) {
		$prior = 0.5;
		
		if (datediff('h', $row['web_ctime']) < 24) {
			$freq = "hourly";
			$prior = 0.8;
		} elseif (datediff('d', $row['web_ctime']) < 7) {
			$freq = "daily";
			$prior = 0.7;
		} elseif (datediff('w', $row['web_ctime']) < 4) {
			$freq = "weekly";
		} elseif (datediff('m', $row['web_ctime']) < 12) {
			$freq = "monthly";
		} else {
			$freq = "yearly";
		}
		
		echo "<url>\n";
		echo "<loc>".$row['web_link']."</loc>\n";
		echo "<lastmod>".iso8601('Y-m-d\TH:i:s\Z', $row['web_ctime'])."</lastmod>\n";
		echo "<changefreq>".$freq."</changefreq>\n";
		if ($prior != 0.5) {
			echo "<priority>".$prior."</priority>\n";
		}
		echo "</url>\n";
	}
	echo "</urlset>";
}

/** sodir api */
function get_website_api($cate_id = 0, $start = 0, $pagesize = 0) {
	global $DB, $options;
		
	$where = "w.web_status=3";
	$cate = get_one_category($cate_id);
	if (!empty($cate)) {
		if ($cate['cate_childcount'] > 0) {
			$where .= " AND w.cate_id IN (".$cate['cate_arrchildid'].")";
		} else {
			$where .= " AND w.cate_id=$cate_id";
		}
	}

	$sql = "SELECT w.web_id, w.cate_id, w.web_name, w.web_url, w.web_tags, w.web_intro, w.web_ctime, c.cate_name FROM ".$DB->table('websites')." w LEFT JOIN ".$DB->table('categories')." c ON w.cate_id=c.cate_id";
	$sql .= " WHERE $where ORDER BY w.web_id DESC LIMIT $start, $pagesize";
	$query = $DB->query($sql);
	$websites = array();
	while ($row = $DB->fetch_array($query)) {
		$row['web_link'] = str_replace('&', '&amp;', get_siteinfo_url($row['web_url']));
		$row['web_intro'] = htmlspecialchars(strip_tags($row['web_intro']));
		$row['web_ctime'] = date('Y-m-d H:i:s', $row['web_ctime']);
		$websites[] = $row;
	}
	$DB->free_result($query);
	
	$total = $DB->get_count($DB->table('websites').' w', $where);
	
	header("Content-Type: application/xml;");
	echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
	echo "<urlset xmlns=\"http://www.sodir.org/sitemap/\">\n";
	echo "<total>".$total."</total>";
	
	foreach ($websites as $row) {
		echo "<url>\n";
		echo "<name>".$row['web_name']."</name>\n";
		echo "<link>".$row['web_link']."</link>\n";
		echo "<tags>".$row['web_tags']."</tags>\n";
		echo "<desc>".$row['web_intro']."</desc>\n";
		echo "<cate>".$row['cate_name']."</cate>\n";
		echo "<time>".$row['web_ctime']."</time>\n";		
		echo "</url>\n";
	}
	echo "</urlset>\n";
}

/** archives */
function get_archives() {
	global $DB;
	
	$archives = array();
	if (load_cache('archives')) {
		$archives = load_cache('archives');
	} else {
		$time = array();
		$sql = "SELECT web_ctime FROM ".$DB->table('websites')." WHERE web_status=3 ORDER BY web_ctime DESC";
		$query = $DB->query($sql);
		while ($row = $DB->fetch_array($query)) {
			$time[] = date('Y-m', $row['web_ctime']);
		}
		$DB->free_result($query);
		
		$count = array_count_values($time);
		
		foreach ($count as $key => $val) {
			list($year, $month) = explode('-', $key);
			$archives[$year][$month] = $val;
		}
	}
		
	$newarr = array();
	foreach ($archives as $year => $arr) {
		foreach ($arr as $month => $count) {
			$newarr[$year][$month]['site_count'] = $count;
			$newarr[$year][$month]['arc_link'] = get_archives_url($year.$month);
		}
	}
	
	return $newarr;
}

/** rss  */
function iso8601($format, $timestamp = NULL) {
	if ($timestamp === NULL) {
		$timestamp = time() - date('Z');
	} elseif ($timestamp <= 0) {
		return '';
	}
	$timestamp += (8 * 3600);
	
	return gmdate($format, time());
}
?>