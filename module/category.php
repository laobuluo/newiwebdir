<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '分类浏览';
$pageurl = '?mod=category';
$tplfile = 'category.html';
$table = $DB->table('categories');
$tpldir = 'other';

/** 缓存设置 */
$smarty->compile_dir .= $tpldir;
$smarty->cache_dir .= $tpldir;
$smarty->cache_lifetime = $options['cache_time_other'] * 3600;

if (!$smarty->isCached($tplfile)) {
	$categories = get_categories();
	
	$smarty->assign('pagename', $pagename);
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_keywords', '开放分类，网址分类，目录分类，行业分类');
	$smarty->assign('site_description', '对网站进行很详细的分类，这样有助于帮你找到感兴趣的内容。');
	$smarty->assign('site_path', get_sitepath().' &nbsp;&rsaquo;&nbsp; '.$pagename);
	$smarty->assign('site_rss', get_rssfeed());
	$smarty->assign('total', count($categories));
}

smarty_output($tplfile);
?>