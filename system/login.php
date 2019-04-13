<?php
require('./load.php');

$pagetitle = SYS_NAME.SYS_VERSION;
$fileurl = 'login.php';
$tplfile = 'login.html';
$table = $DB->table('admin');

if (!empty($_POST)) {
	$email = trim($_POST['email']);
	$pass = trim($_POST['pass']);
	
	if (empty($email) || !is_valid_email($email)) {
		alert('请输入有效电子邮件！');
	}
	
	if (empty($pass)) {
		alert('请输入登陆密码！');
	}
	
	$row = $DB->fetch_one("SELECT admin_id, admin_pass, login_count FROM $table WHERE admin_email='$email'");
	if ($row['admin_id'] && $row['admin_pass'] == md5($pass)) {
		$ip_address = sprintf("%u", ip2long(get_client_ip()));
		$login_count = $row['login_count'] + 1;
		$data = array(
			'login_time' => time(),
			'login_ip' => $ip_address,
			'login_count' => $login_count,
		);
		$where = array('admin_id' => $row['admin_id']);
		$DB->update($table, $data, $where);
		$authcode = authcode("$row[admin_id]\t$row[admin_pass]", "ENCODE", AUTH_KEY);
		setcookie('authcode', $authcode);
		
		redirect('admin.php');
	} else {
		alert('用户名或密码错误，请重试！');
	}
}

if ($_GET['act'] == 'logout') {
	setcookie('authcode', '');
	redirect('../');
}

smarty_output($tplfile);
?>