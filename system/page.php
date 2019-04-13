<?php
require('common.php');
require(CORE_PATH.'module/diypage.php');

$fileurl = 'page.php';
$tplfile = 'page.html';
$table = $DB->table('pages');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '页面列表';
	
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	$keyurl = !empty($keywords) ? '?keywords='.urlencode($keywords) : '';
	$pageurl .= $fileurl.$keyurl;
	
	$where = !empty($keywords) ? $where = "page_name like '%$keywords%'" : 1;
	$results = get_page_list($where, 'page_id', 'DESC', $start, $pagesize);
	$pages = array();
	foreach ($results as $row) {
		$row['page_oper'] = '<a href="'.$fileurl.'?act=edit&page_id='.$row['page_id'].'">编辑</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?act=del&page_id='.$row['page_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$pages[] = $row;
	}
	
	$total = $DB->get_count($table, $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('keywords', $keywords);
	$smarty->assign('pages', $pages);
	$smarty->assign('showpage', $showpage);
}

/** add */
if ($action == 'add') {
	$pagetitle = '添加新页面';
			
	$smarty->assign('h_action', 'saveadd');
}

/** edit */
if ($action == 'edit') {
	$pagetitle = '编辑页面';
	
	$page_id = intval($_GET['page_id']);
	$row = get_one_page($page_id);
	if (!$row) {
		alert('指定的内容不存在！');
	}
	
	$smarty->assign('row', $row);
	$smarty->assign('h_action', 'saveedit');
}

/** save data */
if (in_array($action, array('saveadd', 'saveedit'))) {
	$page_name = trim($_POST['page_name']);
	$page_intro = trim($_POST['page_intro']);
	$page_content = trim($_POST['page_content']);
	
	if (empty($page_name)) {
		alert('请输入自定义页面名称！');
	}
	
	if (empty($page_content)) {
		alert('请输入自定义页面内容！');
	}
	
	$data = array(
		'page_name' => $page_name,
		'page_intro' => $page_intro,
		'page_content' => $page_content,
	);
	
	if ($action == 'saveadd') {
    	$query = $DB->query("SELECT page_id FROM $table WHERE page_name='$page_name'");
    	if ($DB->num_rows($query)) {
        	alert('您所添加的页面已存在！');
    	}
		
		$DB->insert($table, $data);
		
		alert('自定义页面添加成功！', $fileurl);
	} elseif ($action == 'saveedit') {
		$page_id = intval($_POST['page_id']);
		$where = array('page_id' => $page_id);
		
		$DB->update($table, $data, $where);
		
		alert('自定义页面修改成功！', $fileurl);
	}
}

/** del */
if ($action == 'del') {
	$page_ids = (array) ($_POST['page_id'] ? $_POST['page_id'] : $_GET['page_id']);
	
	$DB->delete($table, 'page_id IN ('.dimplode($page_ids).')');
	
	alert('自定义标签删除成功！', $fileurl);
}

smarty_output($tplfile);
?>