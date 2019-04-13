<?php
require('common.php');

$pagetitle = '修改密码';
$fileurl = 'editpwd.php';
$tplfile = 'editpwd.html';
$table = $DB->table('admin');

if ($action == 'saveedit') {
	$admin_id = intval($_POST['admin_id']);
	$admin_email = trim($_POST['admin_email']);
	$admin_pass = trim($_POST['admin_pass']);
	$new_pass = trim($_POST['new_pass']);
	$new_pass1 = trim($_POST['new_pass1']);
	
	if (empty($admin_email) || !is_valid_email($admin_email)) {
		alert('请输入正确的电子邮箱！');
	}
	
	if (empty($admin_pass)) {
		alert('请输入原始密码！');
	}
	
	if (empty($new_pass)) {
		alert('请输入新密码！');
	}
	
	if (empty($new_pass1)) {
		alert('请输入确认密码！');
	}
	
	if ($new_pass != $new_pass1) {
		alert('您两次输入的密码不一致！');
	}
	
	$admin_pass = md5($admin_pass);
	$new_pass = md5($new_pass);
	
	$row = $DB->fetch_one("SELECT admin_id, admin_pass FROM $table WHERE admin_id='$admin_id'");
	if (!$row) {
		alert('不存在此用户！');
	} else {
		if ($admin_pass != $row['admin_pass']) {
			alert('您输入的原始密码不正确！');
		}
		$DB->update($table, array('admin_email' => $admin_email, 'admin_pass' => $new_pass), array('admin_id' => $row['admin_id']));
	}
	
	alert('帐号密码修改成功！', $fileurl);
}

smarty_output($tplfile);
?>