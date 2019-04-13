<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

$pagename = '网站提交入口';
$pageurl = '?mod=addurl';
$tplfile = 'addurl.html';
$tpldir = 'other';

/** 缓存设置 */
$smarty->caching = false;
$smarty->compile_dir .= $tpldir;
$smarty->cache_dir .= $tpldir;
$smarty->cache_lifetime = $options['cache_time_other'] * 3600;

if (!$smarty->isCached($tplfile)) {
	$smarty->assign('pagename', $pagename);
	$smarty->assign('site_title', $pagename.' - '.$options['site_name']);
	$smarty->assign('site_keywords', '网站提交，网址提交，网站提交入口，网站登录入口');
	$smarty->assign('site_description', '欢迎您提交各类优秀网站到'.$options['web_name'].'！');
	$smarty->assign('site_path', get_sitepath().' &nbsp;&rsaquo;&nbsp; '.$pagename);
	$smarty->assign('site_rss', get_rssfeed());
	
	#统计当日提交的站点数量
	if ($options['submit_limit'] > 0) {
		$today_count = $DB->get_count($DB->table('apply'), "FROM_UNIXTIME(web_ctime, '%Y-%m-%d') = CURDATE()");
		$submit_limit = $options['submit_limit'] - $today_count;
		$smarty->assign('submit_limit', $submit_limit);
		
		if ($options['submit_limit'] == $today_count) {
			exit('<div style="background: #ffffe7; border: dashed 2px #f90; margin: 100px auto; padding: 20px 0; text-align: center; width: 500px;">今日允许提交的站点已达到最大上限（即每日<span style="color: #f60; font: 24px Arial;">'.$options['submit_limit'].'</span>个站点）</div>');	
		}
	}
	
	if (!empty($_POST)) {
		$cate_id = intval($_POST['cid']);
		$web_name = trim($_POST['name']);
		$web_url = trim($_POST['url']);
		$web_title = trim($_POST['title']);
		$web_tags = trim($_POST['tags']);
		$web_intro = strip_tags(trim($_POST['intro']));
		$web_email = trim($_POST['email']);
		$check_code = md5($_POST['code']);
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
		} else {
			if (utf8_strlen($web_name) > 10) {
				alert('网站名称长度不能超过10个字符！');	
			} else {
				if (!censor_words($options['filter_words'], $web_name)) {
					alert('网站名称中含有非法关键词！');	
				}	
			}
		}
		
		if (empty($web_url)) {
			alert('请输入网站域名！');
		} else {
			if (!is_valid_domain($web_url)) {
				alert('域名格式不正确，请重新输入！');
			}
		}
		
		if (empty($web_title)) {
			alert('请输入网站标题！');
		}
		
		if (empty($web_tags)) {
			alert('请输入TAG标签！');
		} else {
			$web_tags = str_replace('|', ',', $web_tags);
			$web_tags = str_replace('、', ',', $web_tags);
			$web_tags = str_replace('，', ',', $web_tags);
			if (substr($web_tags, -1) == ',') {
				$web_tags = substr($web_tags, 0, strlen($web_tags) - 1);
			}
		}
		
		if (empty($web_intro)) {
			alert('请输入网站描述！');
		}
		
		if (empty($web_email)) {
			alert('请输入电子邮箱！');
		} else {
			if (!is_valid_email($web_email)) {
				alert('电子邮箱格式不正确，请重新输入！');
			}
		}
		
		if (empty($check_code) || $check_code != $_SESSION['code']) {
			unset($_SESSION['code']);
			alert('您输入的验证码不正确，请重新输入！');	
		}
		
		#检查非法关键词
		if (!censor_words($options['filter_words'], $web_title) || !censor_words($options['filter_words'], $web_tags) || !censor_words($options['filter_words'], $web_intro)) {
			alert('您的网站含有非法关键词！');	
		}
		
		#验证网站是否存在
		$query = $DB->query("SELECT web_id FROM ".$DB->table('websites')." WHERE web_url='$web_url'");
    	if ($DB->num_rows($query) > 0) {
        	alert('您的网站“'.$web_url.'”已经被本站收录，请勿重复提交！');
		}
		
		#验证友情链接
		if ($options['is_linkcheck_open'] == 'yes') {
			#获取远程内容
			require(CORE_PATH.'include/seodata.php');
			$content = get_url_content('http://'.$web_url);
			#去除空白
			$content = preg_replace('/\s(?=\s)/', '', $content);
			$content = preg_replace('/>\s</', '><', $content);		
			if (!preg_match('/<a(.*?)href=([\'\"]?)http:\/\/'.$options['link_url'].'([\/]?)([\'\"]?)(.*?)>'.$options['link_name'].'<\/a>/i', $content)) { # /href=([\'\"]?)http:\/\/www.iwebdir.cn([\/]?)([\'\"]?)/i
				alert('未能提交成功！出错原因：在您网站首页不能找到本站的链接。');
			}
		}
		
		#网站提交
		$base_info = array(
			'cate_id' => $cate_id,
			'web_name' => $web_name,
			'web_url' => $web_url,
			'web_title' => $web_title,
			'web_tags' => $web_tags,
			'web_intro' => $web_intro,
			'web_email' => $web_email,
			'web_ctime' => $web_time,
		);
		$DB->insert($DB->table('apply'), $base_info);
		
		$insert_id = $DB->insert_id();
		$data_info = array('web_utime' => $web_time);
		$DB->insert($DB->table('webdata'), $data_info);
		
		#发送通知邮件
		if ($options['is_send_mail'] == 'yes') {
			require(CORE_PATH.'include/sendmail.php');
			
			$smarty->assign('web_name', $web_name);
			$smarty->assign('web_url', $web_url);
			$mailbody = $smarty->fetch('sendmail.html');
			sendmail($web_email, '欢迎提交网站至'.$options['web_name'].'！', $mailbody);	
		}
		
		unset($_SESSION['code']);
		
		alert('您的网站已提交成功！');
	}
}

smarty_output($tplfile);
?>