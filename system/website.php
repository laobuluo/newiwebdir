<?php
require('common.php');
require(CORE_PATH.'include/seodata.php');
require(CORE_PATH.'module/category.php');
require(CORE_PATH.'module/website.php');

$fileurl = 'website.php';
$tplfile = 'website.html';
$table = $DB->table('websites');

if (!isset($action)) $action = 'list';

/** list */
if ($action == 'list') {
	$pagetitle = '站点列表';
	
	$status = intval(trim($_GET['status']));
	$cate_id = intval(trim($_GET['cate_id']));
	$sort = intval(trim($_GET['sort']));
	$order = strtoupper(trim($_GET['order']));
	$keywords = addslashes(trim($_POST['keywords'] ? $_POST['keywords'] : $_GET['keywords']));
	if (empty($order)) $order = 'DESC';
	
	$pageurl = $fileurl.'?status='.$status.'&cate_id='.$cate_id.'&sort='.$sort.'&order='.$order;
	$keyurl = !empty($keywords) ? '&keywords='.urlencode($keywords) : '';
	$pageurl .= $keyurl;
	
	$category_option = get_category_option(0, $cate_id, 0);
	
	$smarty->assign('status', $status);
	$smarty->assign('cate_id', $cate_id);
	$smarty->assign('sort', $sort);
	$smarty->assign('order', $order);
	$smarty->assign('keywords', $keywords);
	$smarty->assign('keyurl', $keyurl);
	$smarty->assign('category_option', $category_option);
	
	$where = '';
	$sql = "SELECT a.web_id, a.cate_id, a.web_name, a.web_url, a.web_ispay, a.web_istop, a.web_isbest, a.web_islink, a.web_status, a.web_ctime, b.web_id, b.web_ip, b.web_grank, b.web_srank, b.web_arank, b.web_instat, b.web_outstat, b.web_views, c.cate_name FROM $table a LEFT JOIN ".$DB->table('webdata')." b ON a.web_id=b.web_id LEFT JOIN ".$DB->table('categories')." c ON a.cate_id=c.cate_id WHERE";
	switch ($status) {
		case 1 :
			$where .= " a.web_status=1";
			break;
		case 2 :
			$where .= " a.web_status=2";
			break;
		case 3 :
			$where .= " a.web_status=3";
			break;
		default :
			$where .= " a.web_status>-1";
			break;
	}
	
	if ($cate_id > 0) {
		$cate = get_one_category($cate_id);
		$where .= " AND a.cate_id IN (".$cate['cate_arrchildid'].")";
	}
	
	if ($keywords) $where .= " AND a.web_name like '%$keywords%'";
	
	switch ($sort) {
		case 1 :
			$field = "a.web_ctime";
			break;
		case 2 :
			$field = "b.web_grank";
			break;
		case 3 :
			$field = "b.web_arank";
			break;
		case 4 :
			$field = "b.web_instat";
			break;
		case 5 :
			$field = "b.web_outstat";
			break;
		case 6 :
			$field = "b.web_views";
			break;
		default :
			$field = "a.web_ctime";
			break;
	}
	
	$sql .= $where." ORDER BY a.web_istop DESC, $field $order LIMIT $start, $pagesize";
	$query = $DB->query($sql);
	
	$websites = array();
	while ($row = $DB->fetch_array($query)) {
		$row['web_cate'] = '<a href="'.$fileurl.'?cate_id='.$row['cate_id'].'">'.$row['cate_name'].'</a>';
		$row['web_name'] = '<a href="'.format_url($row['web_url']).'" target="_blank">'.$row['web_name'].'</a> '.($row['web_errors'] > 0 ? '<sup style="color: #f00;">error!</sup>' : '');
		$row['web_ip'] = long2ip($row['web_ip']);
		switch ($row['web_status']) {
			case 1 :
				$web_status = '<font color="#333333">黑名单</font>';
				break;
			case 2 :
				$web_status = '<font color="#ff3300">待审核</font>';
				break;
			case 3 :
				$web_status = '<font color="#008800">已审核</font>';
				break;
		}
		$web_ispay = $row['web_ispay'] > 0 ? '<font color="#ff0000">付费</font>' : '<font color="#cccccc">付费</font>';
		$web_istop = $row['web_istop'] > 0 ? '<font color="#ff0000">置顶</font>' : '<font color="#cccccc">置顶</font>';
		$web_isbest = $row['web_isbest'] > 0 ? '<font color="#ff0000">推荐</font>' : '<font color="#cccccc">推荐</font>';
		$web_islink = $row['web_islink'] > 0 ? '<font color="#ff0000">未链接</font>' : '<font color="#cccccc">链接中</font>';
		$row['web_attr'] = $web_ispay.' - '.$web_istop.' - '.$web_isbest.' - '.$web_status.' - '.$web_islink;
		$row['web_ctime'] = date('Y-m-d H:i:s', $row['web_ctime']);
		$row['web_oper'] = '<a href="'.$fileurl.'?act=edit&web_id='.$row['web_id'].'">编辑</a>&nbsp;|&nbsp;<a href="'.$fileurl.'?act=del&web_id='.$row['web_id'].'" onClick="return confirm(\'确认删除此内容吗？\');">删除</a>';
		$websites[] = $row;
	}
	$DB->free_result($query);
	
	$total = $DB->get_count($table.' a', $where);
	$showpage = showpage($pageurl, $total, $curpage, $pagesize);
	
	$smarty->assign('websites', $websites);
	$smarty->assign('showpage', $showpage);
}

/** add */
if ($action == 'add') {
	$pagetitle = '添加站点';

	$web_id = intval($_GET['web_id']);
	$sql = "SELECT cate_id, web_name, web_url, web_title, web_tags, web_intro, web_email FROM ".$DB->table('apply')." WHERE web_id='$web_id'";
	$row = $DB->fetch_one($sql);
	
	$smarty->assign('row', $row);
	$smarty->assign('status', 3);
	$smarty->assign('h_action', 'saveadd');
}

/** edit */
if ($action == 'edit') {
	$pagetitle = '编辑站点';
	
	$web_id = intval($_GET['web_id']);
	$where = "w.web_id='$web_id'";
	$row = get_one_website($where);
	if (!$row) {
		alert('指定的内容不存在！');
	}
	
	#分类ID
	$parent_cids = get_category_parent_ids($row['cate_id']).','.$row['cate_id'];
	if (strpos($parent_cids, ',') !== false) {
		$cate_pids = explode(',', $parent_cids);
		array_shift($cate_pids);
	} else {
		$cate_pids = (array) $parent_cids;
	}
	
	#IP
	$row['web_ip'] = long2ip($row['web_ip']);
	
	#状态
	$status = $row['web_status'];
	
	$smarty->assign('cate_pids', $cate_pids);
	$smarty->assign('status', $status);
	$smarty->assign('row', $row);
	$smarty->assign('h_action', 'saveedit');
}

/** move */
if ($action == 'move') {
	$pagetitle = '移动站点';
			
	$web_ids = (array) ($_POST['web_id'] ? $_POST['web_id'] : $_GET['web_id']);
	if (empty($web_ids)) {
		alert('请选择要移动的站点！');
	} else {
		$wids = dimplode($web_ids);
	}
	
	$category_option = get_category_option(0, 0, 0);
	$websites = $DB->fetch_all("SELECT web_id, web_name FROM $table WHERE web_id IN ($wids)");
	
	$smarty->assign('category_option', $category_option);
	$smarty->assign('websites', $websites);
	$smarty->assign('h_action', 'savemove');
}

/** attr */
if ($action == 'attr') {
	$pagetitle = '属性设置';
	
	$web_ids = (array) ($_POST['web_id'] ? $_POST['web_id'] : $_GET['web_id']);
	if (empty($web_ids)) {
		alert('请选择要设置的站点！');
	} else {
		$wids = dimplode($web_ids);
	}
	
	$category_option = get_category_option(0, 0, 0);
	$websites = $DB->fetch_all("SELECT web_id, web_name FROM $table WHERE web_id IN ($wids)");
	
	$smarty->assign('category_option', $category_option);
	$smarty->assign('websites', $websites);
	$smarty->assign('h_action', 'saveattr');
}

/** save data */
if (in_array($action, array('saveadd', 'saveedit'))) {
	$cate_id = intval($_POST['cate_id']);
	$web_name = trim($_POST['web_name']);
	$web_url = trim($_POST['web_url']);
	$web_title = trim($_POST['web_title']);
	$web_tags = addslashes(trim($_POST['web_tags']));
	$web_intro = addslashes(trim($_POST['web_intro']));
	$web_ip = trim($_POST['web_ip']);
	$web_brank = intval($_POST['web_brank']);
	$web_grank = intval($_POST['web_grank']);
	$web_srank = intval($_POST['web_srank']);
	$web_arank = intval($_POST['web_arank']);
	$web_instat = intval($_POST['web_instat']);
	$web_outstat = intval($_POST['web_outstat']);
	$web_views = intval($_POST['web_views']);
	$web_errors = intval($_POST['web_errors']);
	$web_ispay = intval($_POST['web_ispay']);
	$web_istop = intval($_POST['web_istop']);
	$web_isbest = intval($_POST['web_isbest']);
	$web_status = intval($_POST['web_status']);
	$web_time = time();
	
	if ($cate_id <= 0) {
		alert('请选择网站所属分类！');
	} else {
		$cate = get_one_category($cate_id);
		if ($cate['cate_childcount'] > 0) {
			alert('指定的分类下有子分类，请选择子分类进行操作！');
		}
	}
	
	if (empty($web_name)) {
		alert('请输入网站名称！');
	}
	
	if (empty($web_url)) {
		alert('请输入网站域名！');
	} else {
		if (!is_valid_domain($web_url)) {
			alert('请输入正确的网站域名！');
		}
	}
	
	if (!empty($web_tags)) {
		$web_tags = str_replace('|', ',', $web_tags);
		$web_tags = str_replace('、', ',', $web_tags);
		$web_tags = str_replace('，', ',', $web_tags);
		$web_tags = str_replace(',,', ',', $web_tags);
		if (substr($web_tags, -1) == ',') {
			$web_tags = substr($web_tags, 0, strlen($web_tags) - 1);
		}	
	}
	
	if (empty($web_intro)) {
		alert('请输入网站简介！');
	}
	
	$web_ip = sprintf("%u", ip2long($web_ip));
	
	$web_data = array(
		'cate_id' => $cate_id,
		'web_name' => $web_name,
		'web_url' => $web_url,
		'web_title' => $web_title,
		'web_tags' => $web_tags,
		'web_intro' => $web_intro,
		'web_ispay' => $web_ispay,
		'web_istop' => $web_istop,
		'web_isbest' => $web_isbest,
		'web_status' => $web_status,
		'web_ctime' => $web_time,
	);
	
	if ($action == 'saveadd') {
    	$query = $DB->query("SELECT web_id FROM $table WHERE web_url='$web_url'");
    	if ($DB->num_rows($query)) {
        	alert('您所添加的网站已存在！');
    	}
		$DB->insert($table, $web_data);
		
		$stat_data = array(
			'web_ip' => $web_ip,
			'web_brank' => $web_brank,
			'web_grank' => $web_grank,
			'web_srank' => $web_srank,
			'web_arank' => $web_arank,
			'web_instat' => $web_instat,
			'web_outstat' => $web_outstat,
			'web_views' => $web_views,
			'web_errors' => $web_errors,
			'web_utime' => $web_time,
		);
		$DB->insert($DB->table('webdata'), $stat_data);
		$DB->query("UPDATE ".$DB->table('categories')." SET cate_postcount=cate_postcount+1 WHERE cate_id='$cate_id'");
		update_cache('archives');
		
		alert('网站添加成功！', $fileurl.'?act=add&cate_id='.$cate_id);	
	} elseif ($action == 'saveedit') {
		$web_id = intval($_POST['web_id']);
		$where = array('web_id' => $web_id);
		unset($web_data['web_ctime']);
		
		$DB->update($table, $web_data, $where);
		
		$stat_data = array(
			'web_ip' => $web_ip,
			'web_brank' => $web_brank,
			'web_grank' => $web_grank,
			'web_srank' => $web_srank,
			'web_arank' => $web_arank,
			'web_instat' => $web_instat,
			'web_outstat' => $web_outstat,
			'web_views' => $web_views,
			'web_errors' => $web_errors,
			'web_utime' => $web_time,
		);
		$DB->update($DB->table('webdata'), $stat_data, $where);
		
		$DB->query("UPDATE ".$DB->table('categories')." SET cate_postcount=cate_postcount+1 WHERE cate_id='$cate_id'");
		update_cache('archives');
		
		alert('网站修改成功！', $fileurl);
	}
}

/** del */
if ($action == 'del') {
	$web_ids = (array) ($_POST['web_id'] ? $_POST['web_id'] : $_GET['web_id']);
	
	$DB->delete($table, 'web_id IN ('.dimplode($web_ids).')');
	$DB->delete($DB->table('webdata'), 'web_id IN ('.dimplode($web_ids).')');
	update_cache('archives');
	
	alert('网站删除成功！', $fileurl);
}

/** move */
if ($action == 'savemove') {
	$web_ids = (array) $_POST['web_id'];
	$cate_id = intval(trim($_POST['cate_id']));
	if (empty($web_ids)) {
		alert('请选择要移动的内容！');
	}
	if ($cate_id <= 0) {
		alert('请选择分类！');
	} else {
		$cate = get_one_category($cate_id);
		if ($cate['cate_childcount'] > 0) {
			alert('指定的分类下有子分类，请选择子分类进行操作！');
		}
	}
	
	$DB->update($table, array('cate_id' => $cate_id), 'web_id IN ('.dimplode($web_ids).')');
	update_cache('archives');
	
	alert('网站移动成功！', $fileurl);
}

/** attr */
if ($action == 'saveattr') {
	$web_ids = (array) $_POST['web_id'];
	$web_ispay = intval($_POST['web_ispay']);
	$web_istop = intval($_POST['web_istop']);
	$web_isbest = intval($_POST['web_isbest']);
	$web_status = intval($_POST['web_status']);
	if (empty($web_ids)) {
		alert('请选择要设置的内容！');
	}
	
	$DB->update($table, array('web_ispay' => $web_ispay, 'web_istop' => $web_istop, 'web_isbest' => $web_isbest, 'web_status' => $web_status), 'web_id IN ('.dimplode($web_ids).')');
	
	alert('网站属性设置成功！', $fileurl);
}

smarty_output($tplfile);
?>