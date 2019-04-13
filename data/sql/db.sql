
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;




CREATE TABLE IF NOT EXISTS `dir_admin` (
  `admin_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `admin_email` varchar(50) NOT NULL,
  `admin_pass` varchar(50) NOT NULL,
  `login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `login_ip` int(10) unsigned NOT NULL DEFAULT '0',
  `login_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`admin_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_advers` (
  `adver_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `adver_name` varchar(50) NOT NULL DEFAULT '',
  `adver_code` text NOT NULL,
  `adver_etips` varchar(50) NOT NULL DEFAULT '',
  `adver_days` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `adver_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`adver_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_apply` (
  `web_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `web_name` varchar(50) NOT NULL,
  `web_url` varchar(50) NOT NULL,
  `web_title` varchar(100) NOT NULL,
  `web_tags` varchar(100) NOT NULL,
  `web_intro` varchar(200) NOT NULL,
  `web_email` varchar(50) NOT NULL,
  `web_ctime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`web_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_categories` (
  `cate_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `root_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cate_name` varchar(50) NOT NULL DEFAULT '',
  `cate_dir` varchar(50) NOT NULL DEFAULT '',
  `cate_url` varchar(255) NOT NULL,
  `cate_sort` smallint(5) unsigned NOT NULL DEFAULT '0',
  `cate_isbest` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cate_keywords` varchar(255) NOT NULL DEFAULT '',
  `cate_description` varchar(255) NOT NULL DEFAULT '',
  `cate_arrparentid` varchar(255) NOT NULL,
  `cate_arrchildid` text NOT NULL,
  `cate_childcount` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cate_postcount` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cate_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_comments` (
  `com_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `root_id` int(10) unsigned NOT NULL DEFAULT '0',
  `web_id` int(10) unsigned NOT NULL DEFAULT '0',
  `com_nick` varchar(10) NOT NULL,
  `com_email` varchar(50) NOT NULL,
  `com_text` varchar(250) NOT NULL,
  `com_ip` int(10) unsigned NOT NULL DEFAULT '0',
  `com_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `com_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`com_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_feedbacks` (
  `fb_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `fb_nick` varchar(50) NOT NULL,
  `fb_email` varchar(50) NOT NULL DEFAULT '',
  `fb_content` text NOT NULL,
  `fb_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fb_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_labels` (
  `label_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `label_name` varchar(50) NOT NULL DEFAULT '',
  `label_intro` varchar(255) NOT NULL DEFAULT '',
  `label_content` text NOT NULL,
  PRIMARY KEY (`label_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_links` (
  `link_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `link_name` varchar(50) NOT NULL DEFAULT '',
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_logo` varchar(255) NOT NULL DEFAULT '',
  `link_hide` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `link_sort` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`link_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_options` (
  `option_name` varchar(30) NOT NULL DEFAULT '',
  `option_value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_pages` (
  `page_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `page_name` varchar(50) NOT NULL DEFAULT '',
  `page_intro` varchar(255) NOT NULL DEFAULT '',
  `page_content` text NOT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_webdata` (
  `web_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `web_ip` int(10) unsigned NOT NULL DEFAULT '0',
  `web_brank` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `web_grank` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `web_srank` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `web_arank` int(10) unsigned NOT NULL DEFAULT '0',
  `web_instat` int(10) unsigned NOT NULL DEFAULT '0',
  `web_outstat` int(10) unsigned NOT NULL DEFAULT '0',
  `web_voter` int(10) unsigned NOT NULL DEFAULT '0',
  `web_score` int(10) unsigned NOT NULL DEFAULT '0',
  `web_views` int(10) unsigned NOT NULL DEFAULT '0',
  `web_errors` int(10) unsigned NOT NULL DEFAULT '0',
  `web_itime` int(10) unsigned NOT NULL DEFAULT '0',
  `web_otime` int(10) unsigned NOT NULL DEFAULT '0',
  `web_utime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`web_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `dir_websites` (
  `web_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `web_name` varchar(50) NOT NULL,
  `web_url` varchar(50) NOT NULL,
  `web_title` varchar(100) NOT NULL,
  `web_tags` varchar(100) NOT NULL,
  `web_intro` varchar(200) NOT NULL,
  `web_email` varchar(50) NOT NULL,
  `web_ispay` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `web_istop` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `web_isbest` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `web_islink` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `web_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `web_ctime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`web_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



INSERT INTO `dir_options` (`option_name`, `option_value`) VALUES
('site_name', 'iWebDir网站分类目录修改版'),
('site_title', '网址大全_网址目录_上网导航_网站提交/登录入口'),
('site_url', 'https://www.example.com/'),
('site_root', '/'),
('admin_email', 'iwebdir@qq.com'),
('site_keywords', '分类目录,网站收录,网站提交,网站目录,网站推广,网站登录'),
('site_description', '全人工编辑的开放式网站分类目录，收录国内外、各行业优秀网站，旨在为用户提供网站分类目录检索、优秀网站参考、网站推广服务。'),
('site_copyright', 'Copyright &copy; 2008-2014 iwebdir.cn All Rights Reserved By itbulu.com 修改版'),
('is_gzip_open', 'no'),
('is_submit_open', 'yes'),
('submit_close_reason', '关闭说明'),
('submit_limit', '0'),
('is_linkcheck_open', 'no'),
('link_name', 'example'),
('link_url', 'www.example.com'),
('update_cycle', '3'),
('filter_words', 'fuck,他妈的'),
('is_send_mail', 'no'),
('is_cache_open', 'no'),
('cache_time_index', '2'),
('cache_time_list', '2'),
('cache_time_info', '2'),
('cache_time_other', '2'),
('link_struct', '0'),
('smtp_host', 'smtp.qq.com'),
('smtp_port', '25'),
('smtp_auth', 'yes'),
('smtp_user', 'test'),
('smtp_pass', 'test');