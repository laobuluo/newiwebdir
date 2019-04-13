<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '';
$pageurl = '?mod=diypage';
$tplfile = 'diypage.html';
$table = $DB->table('pages');
$tpldir = 'page';

/** 缓存设置 */
$smarty->compile_dir .= $tpldir;
$smarty->cache_dir .= $tpldir;
$smarty->cache_lifetime = $options['cache_time_other'] * 3600;

$page_id = intval($_GET['pid']);
$cache_id = $page_id;
		
if (!$smarty->isCached($tplfile, $cache_id)) {
	$page = get_one_page($page_id);
	if (!$page) {
		_404();
	}
	
	$smarty->assign('site_title', $page['page_name'].' - '.$options['site_name']);
	$smarty->assign('site_keywords', $options['site_keywords']);
	$smarty->assign('site_description', $options['site_description']);
	$smarty->assign('site_path', get_sitepath().' &nbsp;&rsaquo;&nbsp; '.$page['page_name']);
	$smarty->assign('site_rss', get_rssfeed());
    $smarty->assign('page_id', $page_id);
	$smarty->assign('page', $page);
}
		
smarty_output($tplfile, $cache_id);
?>