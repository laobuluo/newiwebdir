<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '站点详情';
$pageurl = '?mod=siteinfo';
$tplfile = 'siteinfo.html';
$tpldir = 'siteinfo';
$table = $DB->table('webdata');

/** 缓存设置 */
$smarty->compile_dir .= $tpldir;
$smarty->cache_dir .= $tpldir;
$smarty->cache_lifetime = $options['cache_time_info'] * 3600;

$web_url = trim($_GET['url']);
$web_url = str_replace('_', '.', $web_url);
$cache_id = $web_url;
		
if (!$smarty->isCached($tplfile, $cache_id)) {
	$where = "w.web_status=3 AND w.web_url='$web_url'";
	$web = get_one_website($where);
	if (!$web) {
		_404();
	}
	
	$DB->query("UPDATE $table SET web_views=web_views+1 WHERE web_id='".$web['web_id']."' LIMIT 1");
	
	$smarty->assign('pagename', $pagename);
	$smarty->assign('site_title', $web['web_name'].' - '.$web['cate_name'].' - '.$options['site_name']);
	$smarty->assign('site_keywords', !empty($web['web_tags']) ? $web['web_tags'] : $options['site_keywords']);
	$smarty->assign('site_description', !empty($web['web_intro']) ? $web['web_intro'] : $options['site_description']);
	$smarty->assign('site_path', get_sitepath($web['cate_id'].','.$web['cate_arrparentid']).' &nbsp;&rsaquo;&nbsp; '.$pagename);
	$smarty->assign('site_rss', get_rssfeed($web['cate_id']));
	
	$web['full_url'] = format_url($web['web_url']);
	$web['web_thumb'] = get_webthumb($web['web_url']);
	$web['web_ctime'] = date('Y-m-d', $web['web_ctime']);
	$web['web_ip'] = long2ip($web['web_ip']);
	$web['web_arank'] = number_format($web['web_arank']);
	if ($web['web_voter'] > 0 && $web['web_score'] > 0) {
		$web['web_score'] = round($web['web_score'] / $web['web_voter'], 1);
	} else {
		$web['web_score'] = 0;
	}
	$web['web_utime'] = date('Y-m-d', $web['web_utime']);
	
	/** tags */
	$web_tags = get_format_tags($web['web_tags']);
	$smarty->assign('web_tags', $web_tags);
	
    $smarty->assign('web', $web);
	$smarty->assign('relateds', get_website_relateds($web['cate_id'], $web['web_id'], 5));
	$smarty->assign('comments', get_website_comments($web['web_id'], 10));
}
		
smarty_output($tplfile, $cache_id);
?>