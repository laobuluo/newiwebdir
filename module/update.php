<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '最近更新';
$pageurl = '?mod=update';
$tplfile = 'update.html';
$tpldir = 'update';
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
		
$setdays = intval($_GET['days']);
$cache_id = $setdays.'-'.$curpage;

if (!$smarty->isCached($tplfile, $cache_id)) {
	$smarty->assign('site_title', $pagename.$nowpage.' - '.$options['site_name']);
	$smarty->assign('site_keywords', '最近更新，最新收录，每日最新');
	$smarty->assign('site_description', '让你及时了解最新收录内容，可按时间段（最近24小时、三天内、一星期、一个月、一年、所有时间）查询，让你及时了解网站在某一时间段内的收录情况。');
	$smarty->assign('site_path', get_sitepath().' &nbsp;&rsaquo;&nbsp; '.$pagename);
	$smarty->assign('site_rss', get_rssfeed());
	
	$newarr = array();
	$i = 0;
	foreach ($timescope as $key => $val) {
		$newarr[$i]['time_id'] = $key;
		$newarr[$i]['time_text'] = $val;
		$newarr[$i]['time_link'] = $pageurl.'&days='.$key;
		$i++;
	}
	
	$where = "w.web_status=3";
	if ($setdays > 0) {
		$smarty->assign('site_title', '最近'.$timescope[$setdays].'收录详情 - '.$nowpage.$options['site_name']);
		$smarty->assign('site_path', get_sitepath().' &nbsp;&rsaquo;&nbsp; <a href="'.$pageurl.'">'.$pagename.'</a> &nbsp;&rsaquo;&nbsp; '.$timescope[$setdays]);
		$pageurl .= '&days='.$setdays;
		
		$now = time();
		switch ($setdays) {
			case 1 :
				$time = $now - (3600 * 24);
				break;
			case 3 :
				$time = $now - (3600 * 24 * 3);
				break;
			case 7 :
				$time = $now - (3600 * 24 * 7);
				break;
			case 30 :
				$time = $now - (3600 * 24 * 30);
				break;
			case 365 :
				$time = $now - (3600 * 24 * 365);
				break;
			default :
				$time = 0;
				break;
		}
		$where .= " AND w.web_ctime>='$time'";
	}
			
	$websites = get_website_list($where, 'web_ctime', 'DESC', $start, $pagesize);
	$total = $DB->get_count($table.' w', $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
			
	$smarty->assign('pagename', $pagename);
	$smarty->assign('nowpage', $nowpage);
	$smarty->assign('timescope', $newarr);
	$smarty->assign('timestr', $timescope[$setdays]);
	$smarty->assign('days', $setdays);
	$smarty->assign('total', $total);
	$smarty->assign('websites', $websites);
	$smarty->assign('showpage', $showpage);
}
	
smarty_output($tplfile, $cache_id);
?>