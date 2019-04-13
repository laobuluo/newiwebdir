<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '友情链接';
$pageurl = '?mod=link';
$tplfile = 'link.html';
$table = $DB->table('links');
$tpldir = 'other';

/** 缓存设置 */
$smarty->compile_dir .= $tpldir;
$smarty->cache_dir .= $tpldir;
$smarty->cache_lifetime = $options['cache_time_other'] * 3600;

$pagesize = 10;
$curpage = intval($_GET['page']);
if ($curpage > 1) {
	$start = ($curpage - 1) * $pagesize;
} else {
	$start = 0;
	$curpage = 1;
}

if (!$smarty->isCached($tplfile)) {
	$smarty->assign('pagename', $pagename);
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_keywords', $options['site_keywords']);
	$smarty->assign('site_description', $options['site_description']);
	$smarty->assign('site_path', get_sitepath().' &nbsp;&rsaquo;&nbsp; '.$pagename);
	$smarty->assign('site_rss', get_rssfeed());
	
	$linklist = get_link_list('link_hide = 1', 'link_id', 'DESC', $start, $pagesize);
	$total = $DB->get_count($table, $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('total', $total);
	$smarty->assign('linklist', $linklist);
	$smarty->assign('showpage', $showpage);
}

smarty_output($tplfile);
?>