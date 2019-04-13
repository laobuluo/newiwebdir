<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '网站评论';
$pageurl = '?mod=comment';
$tplfile = 'comment.html';
$tpldir = 'comment';
$table = $DB->table('comments');

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
		
$web_id = intval($_GET['wid']);
$cache_id = $cate_id.'-'.$curpage;

if (!$smarty->isCached($tplfile, $cache_id)) {	
	$sql = "SELECT web_id, cate_id, web_name, web_url FROM ".$DB->table('websites')." WHERE web_status=3 AND web_id='$web_id' LIMIT 1";
	$row = $DB->fetch_one($sql);
	if (!$row) {
		_404();
	}
	
	$smarty->assign('site_title', '《'.$row['web_name'].'》的评论信息'.$nowpage.' - '.$options['site_name']);
	$smarty->assign('site_keywords', '《'.$row['web_name'].'》的评论信息，网站评论，网站点评');
	$smarty->assign('site_description', '网友在'.$options['site_name'].'对网站《'.$row['web_name'].'》的精彩点评。');
	$smarty->assign('site_path', get_sitepath($row['cate_id']).' &nbsp;&rsaquo;&nbsp; 《'.$row['web_name'].'》的'.$pagename);
	$smarty->assign('site_rss', get_rssfeed($row['cate_id']));

	$where = "com_status=1 AND root_id=0 AND web_id='$web_id'";
	$comments = get_comment_list($where, $start, $pagesize);
	$total = $DB->get_count($table, $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
			
	$smarty->assign('pagename', $pagename);
	$smarty->assign('nowpage', $nowpage);
	$smarty->assign('web_name', $row['web_name']);
	$smarty->assign('web_link', get_siteinfo_url($row['web_url']));
	$smarty->assign('total', $total);
	$smarty->assign('comments', $comments);
	$smarty->assign('showpage', $showpage);
}

smarty_output($tplfile, $cache_id);
?>