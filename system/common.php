<?php
require('./load.php');

$authcode = $_COOKIE['authcode'];
$myself = check_admin_login($authcode);
if (empty($myself)) {
	alert('您还未登录或无权限！', './login.php');
}

$smarty->assign('authcode', $authcode);
$smarty->assign('myself', $myself);
?>