<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

require(CORE_PATH.'include/seodata.php');

$type = trim($_GET['type']);
/** check site */
if ($type == 'check') {
	$url = trim($_GET['url']);
	
	if (empty($url)) {
		exit('请输入网站域名!');
	} else {
		if (!is_valid_domain($url)) {
			exit('网站域名格式不正确！');
		}
	}
			
	$query = $DB->query("SELECT web_id FROM ".$DB->table('websites')." WHERE web_url='$url'");
	if ($DB->num_rows($query) > 0) {
		exit('此URL已存在！');
	} else {
		exit('1');
	}
}

/** category */
if ($type == 'category') {
	$key = $_GET['key'];
	$ids = explode(',', $key);
	$cid = array_pop($ids);
	$cid = intval($cid);
	
	$sql = "SELECT cate_id, cate_name FROM ".$DB->table('categories')." WHERE root_id='$cid' ORDER BY cate_id ASC";
	$categories = $DB->fetch_all($sql);
	if (!empty($categories) && is_array($categories)) {
		$temp = array();
		foreach ($categories as $row) {
			$temp[$row['cate_id']]	= $row['cate_name'];
		}
		
		echo json_encode($temp);
	}
}

/** metainfo */
if ($type == 'metainfo') {
	$url = trim($_GET['url']);
	if (empty($url)) {
		exit('请输入网站域名！');
	} else {
		if (!is_valid_domain($url)) {
			exit('请输入正确的网站域名！');
		}
	}
	
	$content = get_url_content('http://'.$url);
	#strip blank
	$content = preg_replace('/\s(?=\s)/', '', $content);
	$content = preg_replace('/>\s</', '><', $content);
	$meta = get_website_meta($content);	
	echo json_encode($meta);
}

/** webdata */
if ($type == 'webdata') {
	$url = trim($_GET['url']);
	if (empty($url)) {
		exit('请输入网站域名！');
	} else {
		if (!is_valid_domain($url)) {
			exit('请输入正确的网站域名！');
		}
	}
	
	$data = array();
	$data['ip'] = get_server_ip($url);
	$data['brank'] = get_baidu_rank($url);
	$data['grank'] = get_google_rank($url);
	$data['srank'] = get_sogou_rank($url);
	$data['arank'] = get_alexa_rank($url);
	echo json_encode($data);
}
?>