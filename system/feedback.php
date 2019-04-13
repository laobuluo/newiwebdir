<?php
require('common.php');
require(CORE_PATH.'module/feedback.php');

$fileurl = 'feedback.php';
$tplfile = 'feedback.html';
$table = $DB->table('feedbacks');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '意见反馈列表';
	
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	$keyurl = $keywords ? '?keywords='.urlencode($keywords) : '';
	$pageurl = $fileurl.$keyurl;
	
	$where = !empty($keywords) ? "fb_nick like '%$keywords%' OR fb_email like '%$keywords%'" : 1;
	$results = get_feedback_list($where, 'fb_id', 'DESC', $start, $pagesize);
	$feedbacks = array();
	foreach ($results as $row) {
		$row['fb_date'] = date('Y-m-d H:i:s', $row['fb_date']);
		$row['fb_oper'] = '<a href="'.$fileurl.'?act=view&fb_id='.$row['fb_id'].'">查看</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?act=del&fb_id='.$row['fb_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$feedbacks[] = $row;
	}
	
	$total = $DB->get_count($table, $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('keywords', $keywords);
	$smarty->assign('feedbacks', $feedbacks);
	$smarty->assign('showpage', $showpage);
}

/** view */
if ($action == 'view') {
	$pagetitle = '查看意见信息';
	
	$fb_id = intval($_GET['fb_id']);
	$row = get_one_feedback($fb_id);
	if (!$row) {
		alert('指定的内容不存在！');
	}
			
	$row['fb_date'] = date('Y-m-d H:i:s', $row['fb_date']);
	$smarty->assign('row', $row);
}

/** del */
if ($action == 'del') {
	$fb_ids = (array) ($_POST['fb_id'] ? $_POST['fb_id'] : $_GET['fb_id']);
	
	$DB->delete($table, 'fb_id IN ('.dimplode($fb_ids).')');
	
	alert('反馈信息删除成功！', $fileurl);
}

smarty_output($tplfile);
?>