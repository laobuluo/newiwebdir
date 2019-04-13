<?php
/** module */
function get_module_url($module = 'index') {
	global $options;
	
	if ($module != 'index') {
		if ($options['link_struct'] == 1) {
			$strurl = $module.'.html';
		} elseif ($options['link_struct'] == 2 || $options['link_struct'] == 3) {
			$strurl = $module.'/';
		} else {
			$strurl = '?mod='.$module;
		}
	}
	
	return $options['site_url'].$strurl;
}

/** category */
function get_category_url($cate_id = 0, $page = 1) {
	global $options;
	
	$row = get_one_category($cate_id);
	if ($row) {
		$cate_id = $row['cate_id'];
		$cate_dir = !empty($row['cate_dir']) ? $row['cate_dir'] : 'category';
	} else {
		$cate_id = 0;
		$cate_dir = 'category';
	}
	
	$page = isset($page) && $page > 0 ? $page : 1;
	
	if ($options['link_struct'] == 1) {
		$strurl = 'webdir-'.$cate_dir.'-'.$cate_id.'-'.$page.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = 'webdir/'.$cate_dir.'-'.$cate_id.'-'.$page;
	} elseif ($options['link_struct'] == 3) {
		$strurl = 'webdir/'.$cate_dir.'-'.$cate_id.'-'.$page.'.html';
	} else {
		$strurl = '?mod=webdir&cid='.$cate_id;
	}
	
	return $options['site_url'].$strurl;
}

/** update */
function get_update_url($days = 0, $page = 1) {
	global $options;
	
	$days = isset($days) && $days > 0 ? $days : 0;
	$page = isset($page) && $page > 0 ? $page : 1;
	
	if ($options['link_struct'] == 1) {
		$strurl = 'update-'.$days.'-'.$page.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = 'update/'.$days.'-'.$page;
	} elseif ($options['link_struct'] == 3) {
		$strurl = 'update/'.$days.'-'.$page.'.html';
	} else {
		$strurl = '?mod=update&days='.$days;
	}
	
	return $options['site_url'].$strurl;
}

/** archives */
function get_archives_url($date = 0, $page = 1) {
	global $options;
	
	$date = isset($date) && strlen($date) == 6 ? $date : 0;
	$page = isset($page) && $page > 0 ? $page : 1;
	
	if ($options['link_struct'] == 1) {
		$strurl = 'archives-'.$date.'-'.$page.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = 'archives/'.$date.'-'.$page;
	} elseif ($options['link_struct'] == 3) {
		$strurl = 'archives/'.$date.'-'.$page.'.html';
	} else {
		$strurl = '?mod=archives&date='.$date;
	}
	
	return $options['site_url'].$strurl;
}

/** search */
function get_search_url($type = 'name', $query = 'all', $page = 1) {
	global $options;

	$query = isset($query) && !empty($query) ? urlencode($query) : '';
	$page = isset($page) && $page > 0 ? $page : 1;
	
	if ($options['link_struct'] == 1) {
		$strurl = 'search-'.$type.'-'.$query.'-'.$page.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = 'search/'.$type.'-'.$query.'-'.$page;
	} elseif ($options['link_struct'] == 3) {
		$strurl = 'search/'.$type.'-'.$query.'-'.$page.'.html';
	} else {
		$strurl = '?mod=search&type='.$type.'&query='.$query;
	}
	
	return $options['site_url'].$strurl;
}

/** siteinfo */
function get_siteinfo_url($web_url = '', $abs_path = false) {
	global $options;
	
	$web_url = str_replace('.', '_', $web_url);
	if ($options['link_struct'] == 1) {
		$strurl = $url_prefix.'http_'.$web_url.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = $url_prefix.'siteinfo/'.$web_url;
	} elseif ($options['link_struct'] == 3) {
		$strurl = $url_prefix.'siteinfo/'.$web_url.'.html';
	} else {
		$strurl = $url_prefix.'?mod=siteinfo&url='.$web_url;
	}
	
	return $options['site_url'].$strurl;
}

/** comment */
function get_comment_url($web_id = 0, $page = 1) {
	global $options;
	
	$page = isset($page) && $page > 0 ? $page : 1;
	if ($options['link_struct'] == 1) {
		$strurl = $url_prefix.'comment-'.$web_id.'-'.$page.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = $url_prefix.'comment/'.$web_id.'-'.$page;
	} elseif ($options['link_struct'] == 3) {
		$strurl = $url_prefix.'comment/'.$web_id.'-'.$page.'.html';
	} else {
		$strurl = $url_prefix.'?mod=comment&wid='.$web_id;
	}
	
	return $options['site_url'].$strurl;
}

/** diypage */
function get_diypage_url($page_id = 0) {
	global $options;
	
	if ($options['link_struct'] == 1) {
		$strurl = 'diypage-'.$page_id.'.html';
	} elseif ($options['link_struct'] == 2) {
		$strurl = 'diypage/'.$page_id;
	} elseif ($options['link_struct'] == 3) {
		$strurl = 'diypage/'.$page_id.'.html';
	} else {
		$strurl = '?mod=diypage&pid='.$page_id;
	}
	
	return $options['site_url'].$strurl;
}

/** rssfeed */
function get_rssfeed_url($cate_id = 0) {
	global $options;
	
	if ($cate_id > 0) {
		if ($options['link_struct'] == 1) {
			$strurl = 'rssfeed-'.$cate_id.'.html';
		} elseif ($options['link_struct'] == 2) {
			$strurl = 'rssfeed/'.$cate_id;
		} elseif ($options['link_struct'] == 3) {
			$strurl = 'rssfeed/'.$cate_id.'.html';
		} else {
			$strurl = '?mod=rssfeed&cid='.$cate_id;
		}
	} else {
		if ($options['link_struct'] == 1) {
			$strurl = 'rssfeed.html';
		} elseif ($options['link_struct'] == 2 || $options['link_struct'] == 3) {
			$strurl = 'rssfeed/';
		} else {
			$strurl = '?mod=rssfeed';
		}
	}
	
	return $options['site_url'].$strurl;
}

/** sitemap */
function get_sitemap_url($cate_id = 0) {
	global $options;
	
	if ($cate_id > 0) {
		if ($options['link_struct'] == 1) {
			$strurl = 'sitemap-'.$cate_id.'.html';
		} elseif ($options['link_struct'] == 2) {
			$strurl = 'sitemap/'.$cate_id;
		} elseif ($options['link_struct'] == 3) {
			$strurl = 'sitemap/'.$cate_id.'.html';
		} else {
			$strurl = '?mod=sitemap&cid='.$cate_id;
		}
	} else {
		if ($options['link_struct'] == 1) {
			$strurl = 'sitemap.html';
		} elseif ($options['link_struct'] == 2 || $options['link_struct'] == 3) {
			$strurl = 'sitemap/';
		} else {
			$strurl = '?mod=sitemap';
		}
	}
	
	return $options['site_url'].$strurl;
}

/** thumbs */
function get_webthumb($web_url) {
	return 'https://blinky.nemui.org/shot?'.str_replace('.', '.', $web_url);
}
?>