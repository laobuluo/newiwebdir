<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '网站目录';
$pageurl = '?mod=webdir';
$tplfile = 'webdir.html';
$tpldir = 'webdir';
$table = $DB->table('websites');

/** 缓存设置 */
$smarty->compile_dir .= $tpldir;
$smarty->cache_dir .= $tpldir;
$smarty->cache_lifetime = $options['cache_time_list'] * 3600;

$pagesize = 10;
$curpage = intval($_GET['page']);
if ($curpage > 1) {
	$start = ($curpage - 1) * $pagesize;
} else {
	$start = 0;
	$curpage = 1;
}
$nowpage = ($curpage > 0) ? ' - 第'.$curpage.'页': '';
		
$cate_id = intval($_GET['cid']);
$cache_id = $cate_id.'-'.$curpage;

if (!$smarty->isCached($tplfile, $cache_id)) {
	$smarty->assign('site_title', $pagename.$nowpage.' - '.$options['site_name']);
	$smarty->assign('site_keywords', $options['site_keywords']);
	$smarty->assign('site_description', $options['site_description']);
	$smarty->assign('site_path', get_sitepath().' &nbsp;&rsaquo;&nbsp; '.$pagename);
	$smarty->assign('site_rss', get_rssfeed());
	
	$where = "w.web_status=3";
	if ($cate_id > 0) {
		$pageurl .= '&cid='.$cate_id;
		$cate = get_one_category($cate_id);
		if (!$cate) {
			_404();
		}
		
		$smarty->assign('site_title', $cate['cate_name'].$nowpage.' - '.$pagename.' - '.$options['site_name']);
		$smarty->assign('site_keywords', !empty($cate['cate_keywords']) ? $cate['cate_keywords'] : "$cate[cate_name]分类，$cate[cate_name]网站，$cate[cate_name]网址，$cate[cate_name]目录");
		$smarty->assign('site_description', !empty($cate['cate_description']) ? $cate['cate_description'] : "$cate[cate_name]网站目录，主要收录与$cate[cate_name]相关的优秀网站，为用户提供最优的$cate[cate_name]等相关内容。");
		$smarty->assign('site_path', get_sitepath($cate['cate_id']));
		$smarty->assign('site_rss', get_rssfeed($cate['cate_id']));
		
		if ($cate['cate_childcount'] > 0) {
			$where .= " AND w.cate_id IN (".$cate['cate_arrchildid'].")";
			$categories = get_categories($cate['cate_id']);
		} else {
			$where .= " AND w.cate_id='$cate_id'";
			$categories = get_categories($cate['root_id']);
		}
	} else {
		$categories = get_categories();
	}
	
	$websites = get_website_list($where, 'web_ctime', 'DESC', $start, $pagesize);
	$total = $DB->get_count($table.' w', $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
			
	$smarty->assign('pagename', $pagename);
	$smarty->assign('nowpage', $nowpage);
	$smarty->assign('category_id', $cate['cate_id']);
	$smarty->assign('category_name', isset($cate['cate_name']) ? $cate['cate_name'] : $pagename);
	$smarty->assign('categories', $categories);
	$smarty->assign('total', $total);
	$smarty->assign('websites', $websites);
	$smarty->assign('showpage', $showpage);
}

smarty_output($tplfile, $cache_id);
?>