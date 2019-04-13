<?php
require('common.php');
require(CORE_PATH.'module/category.php');

$fileurl = 'apply.php';
$tplfile = 'apply.html';
$table = $DB->table('apply');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '站点列表';
	
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	$keyurl = !empty($keywords) ? '?keywords='.urlencode($keywords) : '';
	$pageurl .= $keyurl;
	
	$category_option = get_category_option(0, $cate_id, 0);
	
	$smarty->assign('keywords', $keywords);
	$smarty->assign('keyurl', $keyurl);
	
	$where = '';
	$sql = "SELECT w.web_id, w.cate_id, w.web_name, w.web_url, w.web_title, w.web_tags, w.web_intro, w.web_email, w.web_ctime, c.cate_name FROM $table w LEFT JOIN ".$DB->table('categories')." c ON w.cate_id=c.cate_id WHERE 1";
	if ($keywords) $where .= " AND w.web_name like '%$keywords%'";
	$sql .= $where." ORDER BY w.web_ctime DESC LIMIT $start, $pagesize";
	$query = $DB->query($sql);
	
	$websites = array();
	while ($row = $DB->fetch_array($query)) {
		$row['web_cate'] = '<a href="'.$fileurl.'?cate_id='.$row['cate_id'].'">'.$row['cate_name'].'</a>';
		$row['web_name'] = '<a href="'.format_url($row['web_url']).'" target="_blank" title="网站标题：'.$row['web_title'].'\nTAG标签：'.$row['web_tags'].'\n网站描述：'.$row['web_intro'].'">'.$row['web_name'].'</a>';
		$row['web_ctime'] = date('Y-m-d', $row['web_ctime']);
		$row['web_oper'] = '<a href="website.php?act=add&web_id='.$row['web_id'].'">收录</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?act=del&web_id='.$row['web_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$websites[] = $row;
	}
	$DB->free_result($query);
	
	$total = $DB->get_count($table.' w', $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('websites', $websites);
	$smarty->assign('showpage', $showpage);
}

/** del */
if ($action == 'del') {
	$web_ids = (array) ($_POST['web_id'] ? $_POST['web_id'] : $_GET['web_id']);
	
	$DB->delete($table, 'web_id IN ('.dimplode($web_ids).')');
	$DB->delete($DB->table('apply'), 'web_id IN ('.dimplode($web_ids).')');
	
	alert('网站删除成功！', $fileurl);
}

smarty_output($tplfile);
?>