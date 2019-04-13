<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '网站首页';
$pageurl = '?mod=index';
$tplfile = 'index.html';
$tpldir = 'index';

/** 缓存设置 */
$smarty->compile_dir .= $tpldir;
$smarty->cache_dir .= $tpldir;
$smarty->cache_lifetime = $options['cache_time_index'] * 3600;

if (!$smarty->isCached($tplfile)) {
	$smarty->assign('pagename', $pagename);
	$smarty->assign('site_title', $options['site_name'].' - '.$options['site_title']);
	$smarty->assign('site_keywords', $options['site_keywords']);
	$smarty->assign('site_description', $options['site_description']);
	$smarty->assign('site_path', get_sitepath().' &nbsp;&rsaquo;&nbsp; '.$pagename);
	$smarty->assign('site_rss', get_rssfeed());
}

smarty_output($tplfile);
?>