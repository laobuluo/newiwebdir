<?php
/** check login */
function check_admin_login($authcode) {
	global $DB;
	
	$authcode = str_replace(' ', '+', $authcode);
	list($admin_id, $admin_pass) = !empty($authcode) ? explode("\t", authcode($authcode, 'DECODE', AUTH_KEY)) : array('', '');
	$admin_id = intval($admin_id);
	$admin_pass = addslashes($admin_pass);
	
	$newarr = array();
	if ($admin_id && $admin_pass) {
		$row = $DB->fetch_one("SELECT admin_id, admin_email, admin_pass, login_time, login_ip, login_count FROM ".$DB->table('admin')." WHERE admin_id='$admin_id'");	
		if ($row['admin_pass'] == $admin_pass) {
			$newarr = array(
				'admin_id' => $row['admin_id'],
				'admin_email' => $row['admin_email'],
				'login_time' => date('Y-m-d H:i:s', $row['login_time']),
				'login_ip' => long2ip($row['login_ip']),
				'login_count' => $row['login_count'],
			);
		}
	}
	
	return $newarr;
}

function smarty_output($template, $cache_id = NULL, $compile_id = NULL) {
	global $smarty, $action, $fileurl, $pagetitle;
	
	template_exists($template);
	
	$smarty->assign('action', $action);
	$smarty->assign('fileurl', $fileurl);
	$smarty->assign('pagetitle', $pagetitle);
	$smarty->display($template, $cache_id, $compile_id);
}

function alert($msg, $url = 'javascript: history.go(-1);') {
	global $smarty;
	
	$template = 'msgbox.html';
	template_exists($template);
	
	$smarty->assign('msg', $msg);
	$smarty->assign('url', $url);
	echo $smarty->display('msgbox.html');
	exit();
}

function redirect($url) {
	header('location:'.$url, false, 301);
    exit;
}

function get_real_size($size) {
	$kb = 1024;         // Kilobyte
	$mb = 1024 * $kb;   // Megabyte
	$gb = 1024 * $mb;   // Gigabyte
	$tb = 1024 * $gb;   // Terabyte

	if ($size < $kb) {
		return $size.' Byte';
	} else if ($size < $mb) {
		return round($size / $kb, 2).' KB';
	} else if ($size < $gb) {
		return round($size / $mb, 2).' MB';
	} else if ($size < $tb) {
		return round($size / $gb, 2).' GB';
	} else {
		return round($size / $tb,2).' TB';
	}
}

// 转换时间单位:秒 to XXX
function format_timespan($seconds = '') {
	if ($seconds == '') $seconds = 1;
	$str = '';
	$years = floor($seconds / 31536000);
	if ($years > 0) {
		$str .= $years.' 年, ';
	}
	$seconds -= $years * 31536000;
	$months = floor($seconds / 2628000);
	if ($years > 0 || $months > 0) {
		if ($months > 0) {
			$str .= $months.' 月, ';
		}
		$seconds -= $months * 2628000;
	}
	$weeks = floor($seconds / 604800);
	if ($years > 0 || $months > 0 || $weeks > 0) {
		if ($weeks > 0)	{
			$str .= $weeks.' 周, ';
		}
		$seconds -= $weeks * 604800;
	}
	$days = floor($seconds / 86400);
	if ($months > 0 || $weeks > 0 || $days > 0) {
		if ($days > 0) {
			$str .= $days.' 天, ';
		}
		$seconds -= $days * 86400;
	}
	$hours = floor($seconds / 3600);
	if ($days > 0 || $hours > 0) {
		if ($hours > 0) {
			$str .= $hours.' 小时, ';
		}
		$seconds -= $hours * 3600;
	}
	$minutes = floor($seconds / 60);
	if ($days > 0 || $hours > 0 || $minutes > 0) {
		if ($minutes > 0) {
			$str .= $minutes.' 分钟, ';
		}
		$seconds -= $minutes * 60;
	}
	if ($str == '') {
		$str .= $seconds.' 秒, ';
	}
	$str = substr(trim($str), 0, -1);
	return $str;
}
?>