<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

function smarty_output($template, $cache_id = NULL, $compile_id = NULL) {
	global $smarty, $options;
	
	template_exists($template);
	
	#common
	$options = stripslashes_deep($options);
	$stats = get_stats();
	$labels = stripslashes_deep(get_labels());
	
	$smarty->assign('site_root', $options['site_root']);
	$smarty->assign('site_name', $options['site_name']);
	$smarty->assign('site_url', $options['site_url']);
	$smarty->assign('site_copyright', $options['site_copyright']);
	
	$smarty->assign('option', $options); #options
	$smarty->assign('stat', $stats); #stats
	$smarty->assign('label', $labels); #labels
	$smarty->assign('script_time', get_script_time()); #script time
	
	#parse template and output
	$content = $smarty->fetch($template, $cache_id, $compile_id);
	
	#strip blank
	$content = preg_replace('/\s(?=\s)/', '', $content);
	$content = preg_replace('/>\s</', '><', $content);
	
	#rewrite
	if ($options['link_struct'] != 0) {
		$content = rewrite_output($content);
	}
	echo $content;
	
	#gzip
	$buffer = ob_get_contents();
	ob_end_clean();
	$options['is_gzip_open'] == 'yes' ? ob_start('ob_gzhandler') : ob_start();
	
	echo $buffer;
}

function alert($msg, $url = 'javascript: history.go(-1);') {
	global $smarty;
	
	$template = 'msgbox.html';
	template_exists($template);
	
	$smarty->assign('msg', $msg);
	$smarty->assign('url', $url);
	echo $smarty->fetch('msgbox.html');
	@ob_end_flush();
	exit();
}

function redirect($url) {
    header('location:'.$url, false, 301);
	exit;
}

function _404() {
	global $options;
	send_http_status(404);
	header("Location: ".$options['site_root']."404.html");	
}

function get_script_time() {
	global $DB, $options, $start_time;
	
	$mtime = explode(' ', microtime());
	$end_time = $mtime[1] + $mtime[0];
	$exec_time = number_format(($end_time - $start_time), 6);
	$gzip = $options['is_gzip_open'] == 'yes' ? 'Enabled' : 'Disabled';
	
	return 'Processed in '.$exec_time.' second(s), '.$DB->queries.' Queries, Gzip '.$gzip.'<br />'.base64_decode('PHNwYW4gc3R5bGU9ImRpc3BsYXk6IG5vbmU7Ij48YSBocmVmPSJodHRwOi8vd3d3LjM1ZGlyLmNvbS8iIHRhcmdldD0iX2JsYW5rIj5Qb3dlcmVkIEJ5IDM1ZGlyLmNvbTwvYT48L3NwYW4+');
}

function insert_script_time() {
	return get_script_time();
}

/** rss link */
function get_rssfeed($cate_id = 0) {
	global $options;
	
	return '<a href="?mod=rssfeed'.($cate_id > 0 ? '&cid='.$cate_id : '').'" target="_blank"><img src="'.$options['site_root'].'public/images/rss.gif" alt="订阅RssFeed" border="0" /></a>';
}
	
/** site path */
function get_sitepath($cate_ids = '') {
	global $options;
	
	$strpath = '当前位置：<a href="'.$options['site_url'].'">'.$options['site_name'].'</a>'.(!empty($cate_ids) ? get_category_path($cate_ids) : '');
	
	return $strpath;
}

/** format tags */
function get_format_tags($str) {
	$arrstr = !empty($str) && strpos($str, ',') > 0 ? explode(',', $str) : (array) $str;
	$count = count($arrstr);
	
	$newarr = array();
	for ($i = 0; $i < $count; $i++) {
		$tag = trim($arrstr[$i]);
		$newarr[$i]['tag_name'] = $tag;
		$newarr[$i]['tag_link'] = get_search_url('tags', $tag);
	}
	
	return $newarr;
}
?>