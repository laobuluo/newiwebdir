<?php
require('common.php');
require(CORE_PATH.'module/adver.php');

$fileurl = 'adver.php';
$tplfile = 'adver.html';
$table = $DB->table('advers');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '广告列表';
	
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	$keyurl = !empty($keywords) ? '?keywords='.urlencode($keywords) : '';
	$pageurl = $fileurl.$keyurl;
	
	$where .= !empty($keywords) ? " AND adver_name like '%$keywords%'" : "1";
	$results = get_adver_list($where, 'adver_id', 'DESC', $start, $pagesize);
	$advers = array();
	foreach ($results as $row) {
		$endtime = $row['adver_time'] + $row['adver_days'] * 24 * 3600;
		if ($row['adver_days'] > 0) {
			$row['adver_status'] = $endtime > $row['adver_time'] ? '<span class="gre">指定期限</span>' : '<span class="red">已过期</span>';
		} else {
			$row['adver_status'] = '<span class="ora">长期有效</span>';
		}
		$row['adver_time'] = date('Y-m-d H:i:s', $endtime);
		$row['adver_oper'] = '<a href="'.$fileurl.'?act=edit&adver_id='.$row['adver_id'].'">编辑</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?act=del&adver_id='.$row['adver_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$advers[] = $row;
	}
		
	$total = $DB->get_count($table, $where);	
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('keywords', $keywords);
	$smarty->assign('advers', $advers);
	$smarty->assign('showpage', $showpage);
}

/** add */
if ($action == 'add') {
	$pagetitle = '添加新广告';
		
	$smarty->assign('ad_type', 1);
	$smarty->assign('h_action', 'saveadd');
}

/** edit */
if ($action == 'edit') {
	$pagetitle = '编辑广告';
	
	$adver_id = intval($_GET['adver_id']);
	$row = get_one_adver($adver_id);
	if (!$row) {
		alert('指定的内容不存在！');
	}
			
	$smarty->assign('ad_type', $row['adver_type']);
	$smarty->assign('row', $row);
	$smarty->assign('h_action', 'saveedit');
}

/** save data */
if (in_array($action, array('saveadd', 'saveedit'))) {
	$adver_name = trim($_POST['adver_name']);
	$adver_code = trim($_POST['adver_code']);
	$adver_etips = trim($_POST['adver_etips']);
	$adver_days = intval($_POST['adver_days']);
	$adver_time = time();
	
	if (empty($adver_name)) {
		alert('请输入广告名称！');
	}
	
	if (empty($adver_code)) {
		alert('请输入广告代码！');
	}
	
	$data = array(
		'adver_name' => $adver_name,
		'adver_code' => $adver_code,
		'adver_etips' => $adver_etips,
		'adver_days' => $adver_days,
		'adver_time' => $adver_time,
	);
	
	if ($action == 'saveadd') {
    	$query = $DB->query("SELECT adver_id FROM $table WHERE adver_name='$adver_name'");
   		if ($DB->num_rows($query)) {
        	alert('您所添加的广告已存在！');
    	}
		
		$DB->insert($table, $data);
		update_cache('advers');
		
		alert('广告添加成功！', $fileurl);
	} elseif ($action == 'saveedit') {
		$adver_id = intval($_POST['adver_id']);
		$where = array('adver_id' => $adver_id);
		
		$DB->update($table, $data, $where);
		update_cache('advers');
		
		alert('广告修改成功！', $fileurl);
	}
}

/** del */
if ($action == 'del') {
	$adver_ids = (array) ($_POST['adver_id'] ? $_POST['adver_id'] : $_GET['adver_id']);
	
	$DB->delete($table, 'adver_id IN ('.dimplode($adver_ids).')');
	update_cache('advers');
	
	alert('广告删除成功！', $fileurl);
}

smarty_output($tplfile);
?>