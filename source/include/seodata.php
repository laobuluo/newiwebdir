<?php
/** 获取META信息 */
function get_website_meta($content) {		
	$meta = array();
	if (!empty($content)) {
		#Title
		if (preg_match('/<TITLE>(.*?)<\/TITLE>/i', $content, $matches)) {
			$meta['title'] = trim($matches[1]);
		}
		
		#Keywords
		#1
		if (preg_match('/<META\s+name=\"keywords\"\s+content=\"(.*?)\"/i', $content, $matches)) {
			$meta['keywords'] = trim($matches[1]);
		}
		
		if (preg_match('/<META\s+name=\'keywords\'\s+content=\'(.*?)\'/i', $content, $matches)) {
			$meta['keywords'] = trim($matches[1]);
		}
		
		if (preg_match('/<META\s+name=keywords\s+content=(.*?)/i', $content, $matches)) {
			$meta['keywords'] = trim($matches[1]);
		}
		
		if (preg_match('/<META\s+name=\"keywords\"\s+content=\'(.*?)\'/i', $content, $matches)) {
			$meta['keywords'] = trim($matches[1]);
		}
		
		#2
		if (preg_match('/<META\s+content=\"(.*?)\"\s+name=\"keywords\"/i', $content, $matches)) {
			$meta['keywords'] = trim($matches[1]);
		}
		
		if (preg_match('/<META\s+content=\'(.*?)\'\s+name=\'keywords\'/i', $content, $matches)) {
			$meta['keywords'] = trim($matches[1]);
		}
		
		if (preg_match('/<META\s+content=(.*?)\s+name=keywords/i', $content, $matches)) {
			$meta['keywords'] = trim($matches[1]);
		}
		
		#3
		if (preg_match('/<META\s+http-equiv=\"keywords\"\s+content=\"(.*?)\"/i', $content, $matches)) {
			$meta['keywords'] = trim($matches[1]);
		}
		
		if (preg_match('/<META\s+http-equiv=\'keywords\'\s+content=\'(.*?)\'/i', $content, $matches)) {
			$meta['keywords'] = trim($matches[1]);
		}
		
		if (preg_match('/<META\s+http-equiv=keywords\s+content=(.*?)/i', $content, $matches)) {
			$meta['keywords'] = trim($matches[1]);
		}
		
		#Description
		#1
		if (preg_match('/<META\s+name=\"description\"\s+content=\"(.*?)\"/i', $content, $matches)) {
			$meta['description'] = trim($matches[1]);
		}
		
		if (preg_match('/<META\s+name=\'description\'\s+content=\'(.*?)\'/i', $content, $matches)) {
			$meta['description'] = trim($matches[1]);
		}
		
		if (preg_match('/^<META\s+name=description\s+content=(.*?)$/i', $content, $matches)) {
			$meta['description'] = trim($matches[1]);
		}
		
		if (preg_match('/<META\s+name=\"description\"\s+content=\'(.*?)\'/i', $content, $matches)) {
			$meta['description'] = trim($matches[1]);
		}		
		
		#2
		if (preg_match('/<META\s+content=\"(.*?)\"\s+name=\"description\"/i', $content, $matches)) {
			$meta['description'] = trim($matches[1]);
		}
		
		if (preg_match('/<META\s+content=\'(.*?)\'\s+name=\'description\'/i', $content, $matches)) {
			$meta['description'] = trim($matches[1]);
		}
		
		if (preg_match('/<META\s+content=(.*?)\s+name=description/i', $content, $matches)) {
			$meta['description'] = trim($matches[1]);
		}
		
		#3
		if (preg_match('/<META\s+http-equiv=\"description\"\s+content=\"(.*?)\"/i', $content, $matches)) {
			$meta['description'] = trim($matches[1]);
		}
		
		if (preg_match('/<META\s+http-equiv=\'description\'\s+content=\'(.*?)\'/i', $content, $matches)) {
			$meta['description'] = trim($matches[1]);
		}
		
		if (preg_match('/<META\s+http-equiv=description\s+content=(.*?)/i', $content, $matches)) {
			$meta['description'] = trim($matches[1]);
		}
	}

	return $meta; 
}

/** Server IP */
function get_server_ip($url) {
	$domain = get_domain($url);
	if ($domain) {
		$ip = gethostbyname($domain);
	} else {
		$ip = 0;
	}
	
	return $ip;
}

/** location */
function get_location($ip) {
	require(CORE_PATH.'include/location.php');
	$filepath = ROOT_PATH.'data/ipdata/qqwry.dat';
	if (!is_file($filepath)) {
		exit("错误：丢失IP数据库文件“qqwry.dat”，路径为“".ROOT_PATH."data/ipdata/”。");
	}
	
	$iploc = new ip_location($filepath);
	if (!empty($ip)) {
		$loc = $iploc->get_location($ip);
	} else {
		$client_ip = get_client_ip();
		$loc = $iploc->get_location($client_ip);
	}
	$location = $loc['country'].' '.$loc['area'];
	$location = iconv('gb2312', 'utf-8', $location);
	
	return $location;
}

/** Baidu Pagerank */
function get_baidu_rank($url) {
	$content = get_url_content("http://mytool.chinaz.com/baidusort.aspx?host=$url");
	if (preg_match('/百度权重：<font color=\"blue\">(\d+)<\/font>/i', $content, $matches)) {
		$rank = intval($matches[1]);
	} else {
		$rank = 0;
	}
	return $rank;
}

/** Google Pagerank */
function get_google_rank($url) {
	require(CORE_PATH.'include/pagerank.php');
	
	$pr = new PageRank();
	$rank = $pr->getGPR($url);
	
	return $rank;
}

/** Sogou Pagerank */
function get_sogou_rank($url) {
	$content = get_url_content("http://rank.ie.sogou.com/sogourank.php?ur=$url");
	if (preg_match('/sogourank=(\d+)/i', $content, $matches)) {
		$rank = intval($matches[1]);
	} else {
		$rank = 0;
	}
	
	return $rank;
}

/** Alexa Rank */
function get_alexa_rank($url) {
	$content = get_url_content("http://xml.alexa.com/data?cli=10&dat=nsa&ver=quirk-searchstatus&url=$url");
	if (preg_match('/<POPULARITY[^>]*URL[^>]*TEXT[^>]*\"([0-9]+)\"/i', $content, $matches)) {
		$rank = strip_tags($matches[1]);
	} else {
		$rank = 0;
	}
	
	return $rank;
}

/* Baidu Index */
function get_baidu_index($url) {
	$content = get_url_content("http://www.baidu.com/s?wd=site%3A$url");
	if (preg_match('/<span\s+class="nums"\s+style="margin-left:120px"\s+>百度为您找到相关结果(.*?)个<\/span>/i', $content, $matches)) {
		$result = str_replace('约', '', $matches[1]);
		$result = str_replace(',', '', $result);
	} else {
		$result = 0;
	}
	
	return $result;		
}

/* Google Index */
function get_google_index($url) {
	$content = get_url_content("http://www.google.com.hk/search?hl=zh-CN&q=site%3A$url");
	if (preg_match('/<div\s+id=resultStats>找到(.*?)条结果<nobr>/i', $content, $matches)) {
		$result = str_replace('约', '', $matches[1]);
		$result = str_replace(',', '', $result);
	} else {
		$result = 0;
	}
	
	return $result;	
}

/* Soso Index */
function get_soso_index($url) {
	$content = get_url_content("http://www.soso.com/q?w=site%3A$url");
	if (preg_match('/<div\s+id="sInfo">搜索到(.*?)项结果(.*?)<\/div>/i', $content, $matches)) {
		$result = str_replace('约', '', $matches[1]);
		$result = str_replace(',', '', $result);
	} else {
		$result = 0;
	}
	
	return $result;
}

/* Sogou Index */
function get_sogou_index($url) {
	$content = get_url_content("http://www.sogou.com/web?query=site%3A$url");
	if (preg_match('/<!--resultbarnum:(.*?)-->/i', $content, $matches)) {
		$result = str_replace(',', '', $matches[1]);
	} else {
		$result = 0;
	}
	
	return $result;
}

/* 360so Index */
function get_360so_index($url) {
	$content = get_url_content("http://www.so.com/s?q=site%3A$url");
	if (preg_match('/<span\s+class="nums"\s+style="margin-left:120px">找到相关结果(.*?)个<\/span>/i', $content, $matches)) {
		$result = str_replace('约', '', $matches[1]);
		$result = str_replace(',', '', $result);
	} else {
		$result = 0;
	}
	
	return $result;
}

/* Youdao Index */
function get_youdao_index($url) {
	$content = get_url_content("http://www.youdao.com/search?q=site%3A$url");
	if (preg_match('/<span\s+class="srd">共(.*?)条结果<\/span>/i', $content, $matches)) {
		$result = str_replace('约', '', $matches[1]);
		$result = str_replace(',', '', $result);
	} else {
		$result = 0;
	}
	
	return $result;	
}

/* Baidu Backlink */
function get_baidu_backlink($url) {
	$content = get_url_content("http://www.baidu.com/s?wd=\"$url\"");
	if (preg_match('/<span\s+class="nums"\s+style="margin-left:120px"\s+>百度为您找到相关结果(.*?)个<\/span>/i', $content, $matches)) {
		$result = str_replace('约', '', $matches[1]);
		$result = str_replace(',', '', $result);
	} else {
		$result = 0;
	}
	
	return $result;
}

/* Google Backlink */
function get_google_backlink($url) {
	$content = get_url_content("http://www.google.com.hk/search?hl=zh-CN&q=link%3A$url");
	if (preg_match('/<div\s+id=resultStats>找到(.*?)条结果<nobr>/i', $content, $matches)) {
		$result = str_replace('约', '', $matches[1]);
		$result = str_replace(',', '', $result);
	} else {
		$result = 0;
	}
	
	return $result;	
}

/* Soso Backlink */
function get_soso_backlink($url) {
	$content = get_url_content("http://www.soso.com/q?w=link%3A$url");
	if (preg_match('/<div\s+id="sInfo">搜索到(.*?)项结果(.*?)<\/div>/i', $content, $matches)) {
		$result = str_replace('约', '', $matches[1]);
		$result = str_replace(',', '', $result);
	} else {
		$result = 0;
	}
	
	return $result;
}

/* Sogou Backlink */
function get_sogou_backlink($url) {
	$content = get_url_content("http://www.sogou.com/web?query=\"$url\"");
	if (preg_match('/<!--resultbarnum:(.*?)-->/i', $content, $matches)) {
		$result = str_replace(',', '', $matches[1]);
	} else {
		$result = 0;
	}
	
	return $result;
}

/* 360so Backlink */
function get_360so_backlink($url) {
	$content = get_url_content("http://www.so.com/s?q=\"$url\"");
	if (preg_match('/<span\s+class="nums"\s+style="margin-left:120px">找到相关结果(.*?)个<\/span>/i', $content, $matches)) {
		$result = str_replace('约', '', $matches[1]);
		$result = str_replace(',', '', $result);
	} else {
		$result = 0;
	}
	
	return $result;
}

/* Youdao Backlink */
function get_youdao_backlink($url) {
	$content = get_url_content("http://www.youdao.com/search?q=link%3A$url");
	if (preg_match('/<span\s+class="srd">共(.*?)条结果<\/span>/i', $content, $matches)) {
		$result = str_replace('约', '', $matches[1]);
		$result = str_replace(',', '', $result);
	} else {
		$result = 0;
	}
	
	return $result;	
}

/* whois */
function get_whois($url) {
	require(CORE_PATH.'include/whois.php');
	$domain = new domain_whois($url);
	$content = $domain->get_whois();
	$whois = $domain->get_basic_info($content);
	
	return $whois;
}

/* header */
function get_header($url) {
	$url = format_url($url);
	if (function_exists('curl_init')) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_NOBODY, 1);
		curl_setopt($curl, CURLOPT_ENCODING, "gzip, deflate");
		$data = curl_exec($curl);
		
		$headers = explode("\r\n", $data);
		curl_close($curl);
	} else {
		$headers = get_headers($url, 1); //不是curl获取的参数是看不到 gzip 的
		if (is_array($headers['Vary'])) {
			$headers['Vary'] = implode(',', $headers['Vary']);
		}
	}
	
	return $headers;
}

/* icp */
function get_icpinfo($url) {
	$newarr = array();
	$content = get_url_content("http://icp.alexa.cn/index.php?q=$url");
	if (preg_match('/<table(.*?)>([\s\S]*?)<\/table>/i', $content, $matches)) {
		if (preg_match_all('/<td\s+valign="middle"\s+bgcolor="#F3F9FC">([\s\S]*?)<\/td>/i', $matches[2], $temparr)) {
			$newarr['num'] = trim(strip_tags($temparr[1][2]));
			$newarr['type'] = trim($temparr[1][1]);
			$newarr['name'] = trim($temparr[1][0]);
			$newarr['time'] = trim($temparr[1][5]);
		}
	}
	
	return $newarr;
}
?>