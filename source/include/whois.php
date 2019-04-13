<?php
define('TLD_CN', 1);
define('TLD_COMNET', 2);
define('TLD_CC', 3);
define('TLD_ORG', 4);
define('TLD_LA', 5);
define('TLD_MOBILE', 6);
define('TLD_IN', 7);
define('TLD_INFO', 8);
define('TLD_BIZ', 9);
define('TLD_TV', 10);
define('TLD_CO', 11);
define('TLD_TW', 12);
define('TLD_HK', 13);

class domain_whois {
	public $domain;
	
	public function __construct($domain) {
		$this->domain = $domain;
	}
	
	public function get_whois() {
		if (strlen($this->domain) > 0) {
			$support = 1;
			$tld = $this->get_domain_tld($this->domain);
			if ($tld == 1) {
				$whoisserv = "whois.cnnic.net.cn";
				if (preg_match('/^[\x{4E00}-\x{9FA5}]+/u', $this->domain)) {
					$this->domain = mb_convert_encoding($this->domain, 'gbk', 'utf-8');
					$whoisserv = "cwhois.cnnic.net.cn";
				}
			} else if ($tld == 2) {
				$whoisserv = "whois.internic.net";
			} else if ($tld == 3) {
				$whoisserv = "whois.nic.cc";
			} else if ($tld == 4) {
				$whoisserv = "whois.publicinterestregistry.net";
			} else if ($tld == 5) {
				$whoisserv = "whois.nic.la";
			} else if ($tld == 6) {
				$whoisserv = "whois.dotmobiregistry.net";
			} else if ($tld == 7) {
				$whoisserv = "whois.registry.in";
			} else if ($tld == 8) {
				$whoisserv = "whois.afilias.net";
			} else if ($tld == 9) {
				$whoisserv = "whois.neulevel.biz";
			} else if ($tld == 10) {
				$whoisserv = "whois.nic.tv";
			} else if ($tld == 11) {
				$whoisserv = "whois.nic.co";
			} else if ($tld == 12) {
				$whoisserv = "whois.twnic.net.tw";
			} else if ($tld == 13) {
				$whoisserv = "whois.hkdnr.net.hk";
			} else {
				$support = 0;
				exit("($domain) TLD: $tld no supported now.");
			}
			
			if ($support) {
				$whois = $this->get_whois_online($whoisserv, $this->domain);
				if ($tld == 2 || $tld == 3 || $tld == 10) {
					$whoisserv = $this->get_whois_server($whois);
					if (strlen($whoisserv) > 0) {
						$whois .= "\n".$this->get_whois_online($whoisserv, $this->domain);
					}
				}
				return nl2br($whois);
			}
		}
	}
	
	public function get_basic_info($whois) {
		if (empty($whois)) return ;
		
		$result = array();
		$postfix = strrchr($this->domain, ".");
		if ($postfix == ".cn") {
			if (preg_match('/Domain\s+Name:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['domain'] = trim($matches[1]);
			}
			if (preg_match('/Domain\s+Status:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['status'] = trim($matches[1]);
			}
			if (preg_match('/Sponsoring\s+Registrar:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['registrar'] = trim($matches[1]);
			}
			if (preg_match_all('/Name\s+Server:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['name_server'] = implode('　&nbsp;', $matches[1]);
			}
			if (preg_match('/Registration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['creation_date'] = trim($matches[1]);
			}
			if (preg_match('/Expiration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['expiration_date'] = trim($matches[1]);
			}
			if (preg_match('/Administrative\s+Email:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['email'] = trim($matches[1]);
			}
		} else if ($postfix == ".com" || $postfix == ".net") {
			if (preg_match('/Domain\s+Name:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['domain'] = trim($matches[1]);
			}
			if (preg_match_all('/Status:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['status'] = implode('<br />', $matches[1]);
			}
			if (preg_match('/Registrar:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['registrar'] = trim($matches[1]);
			}
			if (preg_match_all('/Name\s+Server:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['name_server'] = implode('　&nbsp;', $matches[1]);
			}
			if (preg_match('/Creation\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['creation_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Expiration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['expiration_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Email\s+:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['email'] = trim($matches[1]);
			}
		} else if ($postfix == ".cc") {
			if (preg_match('/Domain\s+Name:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['domain'] = trim($matches[1]);
			}
			if (preg_match_all('/Status:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['status'] = implode('<br />', $matches[1]);
			}
			if (preg_match('/Registrar:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['registrar'] = trim($matches[1]);
			}
			if (preg_match_all('/Name\s+Server:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['name_server'] = implode('　&nbsp;', $matches[1]);
			}
			if (preg_match('/Creation\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['creation_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Expiration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['expiration_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/\((.*?)\)<br\s+\/>/i', $whois, $matches)) {
				$result['email'] = trim($matches[1]);
			}
		} else if ($postfix == ".org") {
			if (preg_match('/Domain\s+Name:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['domain'] = trim($matches[1]);
			}
			if (preg_match_all('/Status:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['status'] = implode('<br />', $matches[1]);
			}
			if (preg_match('/Sponsoring\s+Registrar:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['registrar'] = trim($matches[1]);
			}
			if (preg_match_all('/Name\s+Server:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$namearr = $matches[1];
				$namestr = '';
				foreach ($namearr as $key => $val) {
					$namearr[$key] = trim($namearr[$key]);
					if (!empty($namearr[$key])) {
						$namestr .= $namearr[$key].'　&nbsp;';
					}
				}
				$result['name_server'] = $namestr;
			}
			if (preg_match('/Created\s+On:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['creation_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Expiration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['expiration_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Tech\s+Email:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['email'] = trim($matches[1]);
			}
		} else if ($postfix == ".la") {
			if (preg_match('/Domain\s+Name:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['domain'] = trim($matches[1]);
			}
			if (preg_match_all('/Status:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['status'] = implode('<br />', $matches[1]);
			}
			if (preg_match('/Sponsoring\s+Registrar\s+Organization:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['registrar'] = trim($matches[1]);
			}
			if (preg_match_all('/Name\s+Server:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['name_server'] = implode('　&nbsp;', $matches[1]);
			}
			if (preg_match('/Created\s+On:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['creation_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Expiration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['expiration_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Registrant\s+Email:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['email'] = trim($matches[1]);
			}
		} else if ($postfix == ".mobi") {
			if (preg_match('/Domain\s+Name:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['domain'] = trim($matches[1]);
			}
			if (preg_match_all('/Status:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['status'] = implode('<br />', $matches[1]);
			}
			if (preg_match('/Sponsoring\s+Registrar:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['registrar'] = trim($matches[1]);
			}
			if (preg_match_all('/Name\s+Server:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['name_server'] = implode('　&nbsp;', $matches[1]);
			}
			if (preg_match('/Created\s+On:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['creation_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Expiration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['expiration_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Registrant\s+Email:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['email'] = trim($matches[1]);
			}
		} else if ($postfix == ".in") {
			if (preg_match('/Domain\s+Name:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['domain'] = trim($matches[1]);
			}
			if (preg_match_all('/Status:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['status'] = implode('<br />', $matches[1]);
			}
			if (preg_match('/Sponsoring\s+Registrar:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['registrar'] = trim($matches[1]);
			}
			if (preg_match_all('/Name\s+Server:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$namearr = $matches[1];
				$namestr = '';
				foreach ($namearr as $key => $val) {
					$namearr[$key] = trim($namearr[$key]);
					if (!empty($namearr[$key])) {
						$namestr .= $namearr[$key].'　&nbsp;';
					}
				}
				$result['name_server'] = $namestr;
			}
			if (preg_match('/Created\s+On:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['creation_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Expiration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['expiration_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Registrant\s+Email:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['email'] = trim($matches[1]);
			}
		} else if ($postfix == ".info") {
			if (preg_match('/Domain\s+Name:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['domain'] = trim($matches[1]);
			}
			if (preg_match_all('/Status:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['status'] = implode('<br />', $matches[1]);
			}
			if (preg_match('/Sponsoring\s+Registrar:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['registrar'] = trim($matches[1]);
			}
			if (preg_match_all('/Name\s+Server:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$namearr = $matches[1];
				$namestr = '';
				foreach ($namearr as $key => $val) {
					$namearr[$key] = trim($namearr[$key]);
					if (!empty($namearr[$key])) {
						$namestr .= $namearr[$key].'　&nbsp;';
					}
				}
				$result['name_server'] = $namestr;
			}
			if (preg_match('/Created\s+On:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['creation_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Expiration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['expiration_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Registrant\s+Email:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['email'] = trim($matches[1]);
			}
		} else if ($postfix == ".biz") {
			if (preg_match('/Domain\s+Name:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['domain'] = trim($matches[1]);
			}
			if (preg_match_all('/Status:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['status'] = implode('<br />', $matches[1]);
			}
			if (preg_match('/Sponsoring\s+Registrar:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['registrar'] = trim($matches[1]);
			}
			if (preg_match_all('/Name\s+Server:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$namearr = $matches[1];
				$namestr = '';
				foreach ($namearr as $key => $val) {
					$namearr[$key] = trim($namearr[$key]);
					if (!empty($namearr[$key])) {
						$namestr .= $namearr[$key].'　&nbsp;';
					}
				}
				$result['name_server'] = $namestr;
			}
			if (preg_match('/Domain\s+Registration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['creation_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Expiration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['expiration_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Registrant\s+Email:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['email'] = trim($matches[1]);
			}
		} else if ($postfix == ".tv") {
			if (preg_match('/Domain\s+Name:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['domain'] = trim($matches[1]);
			}
			if (preg_match_all('/Status:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['status'] = implode('<br />', $matches[1]);
			}
			if (preg_match('/Registrar:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['registrar'] = trim($matches[1]);
			}
			if (preg_match_all('/Name\s+Server:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$namearr = $matches[1];
				$namestr = '';
				foreach ($namearr as $key => $val) {
					$namearr[$key] = trim($namearr[$key]);
					if (!empty($namearr[$key])) {
						$namestr .= $namearr[$key].'　&nbsp;';
					}
				}
				$result['name_server'] = $namestr;
			}
			if (preg_match('/Creation\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['creation_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Expiration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['expiration_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/\((.*?)\)<br\s+\/>/i', $whois, $matches)) {
				$result['email'] = trim($matches[1]);
			}
		} else if ($postfix == ".co") {
			if (preg_match('/Domain\s+Name:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['domain'] = trim($matches[1]);
			}
			if (preg_match_all('/Domain\s+Status:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['status'] = implode('<br />', $matches[1]);
			}
			if (preg_match('/Registrar:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['registrar'] = trim($matches[1]);
			}
			if (preg_match_all('/Name\s+Server:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$namearr = $matches[1];
				$namestr = '';
				foreach ($namearr as $key => $val) {
					$namearr[$key] = trim($namearr[$key]);
					if (!empty($namearr[$key])) {
						$namestr .= $namearr[$key].'　&nbsp;';
					}
				}
				$result['name_server'] = $namestr;
			}
			if (preg_match('/Domain\s+Registration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['creation_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Domain\s+Expiration\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['expiration_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Registrant\s+Email:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['email'] = trim($matches[1]);
			}
		} else if ($postfix == ".tw") {
			if (preg_match('/Domain\s+Name:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['domain'] = trim($matches[1]);
			}
			if (preg_match_all('/Domain\s+Status:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['status'] = implode('<br />', $matches[1]);
			}
			if (preg_match('/Registration\s+Service\s+Provider:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['registrar'] = trim($matches[1]);
			}
			if (preg_match_all('/Domain\s+servers\s+in\s+listed\s+order:<br\s+\/>\n([\s\S]*?)<br\s+\/>\n<br\s+\/>/i', $whois, $matches)) {
				$result['name_server'] = trim(implode('<br />', $matches[1]));
			}
			if (preg_match('/Record\s+created\s+on(.*?)\(YYYY-MM-DD\)<br\s+\/>/i', $whois, $matches)) {
				$result['creation_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Record\s+expires\s+on(.*?)\(YYYY-MM-DD\)<br\s+\/>/i', $whois, $matches)) {
				$result['expiration_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Contact:<br\s+\/>\n([\s\S]*?)<br\s+\/>/i', $whois, $matches)) {
				$result['email'] = trim($matches[1]);
			}
		} else if ($postfix == ".hk") {
			if (preg_match('/Domain\s+Name:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['domain'] = trim($matches[1]);
			}
			if (preg_match_all('/Re-registration\s+Status:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['status'] = implode('<br />', $matches[1]);
			}
			if (preg_match('/Name\s+of\s+Registrar:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['registrar'] = trim($matches[1]);
			}
			if (preg_match_all('/Name\s+Servers\s+Information:<br\s+\/>\n<br\s+\/>([\s\S]*?)<br\s+\/>\n<br\s+\/>/i', $whois, $matches)) {
				$result['name_server'] = implode('<br />', $matches[1]);
			}
			if (preg_match('/Domain\s+Name\s+Commencement\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['creation_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Expiry\s+Date:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['expiration_date'] = date('Y-m-d', strtotime(trim($matches[1])));
			}
			if (preg_match('/Email:(.*?)<br\s+\/>/i', $whois, $matches)) {
				$result['email'] = trim($matches[1]);
			}
		} else {
			return -1;
		}
		
		return $result;
	}
	
	private function get_domain_tld($name) {
		$TLD = 0;
		
		$postfix = strrchr($name, ".");
		if ($postfix == ".cn") {
			$TLD = TLD_CN;
		} else if ($postfix == ".com" || $postfix == ".net") {
			$TLD = TLD_COMNET;
		} else if ($postfix == ".cc") {
			$TLD = TLD_CC;
		} else if ($postfix == ".org") {
			$TLD = TLD_ORG;
		} else if ($postfix == ".la") {
			$TLD = TLD_LA;
		} else if ($postfix == ".mobi") {
			$TLD = TLD_MOBILE;
		} else if ($postfix == ".in") {
			$TLD = TLD_IN;
		} else if ($postfix == ".info") {
			$TLD = TLD_INFO;
		} else if ($postfix == ".biz") {
			$TLD = TLD_BIZ;
		} else if ($postfix == ".tv") {
			$TLD = TLD_TV;
		} else if ($postfix == ".co") {
			$TLD = TLD_CO;
		} else if ($postfix == ".tw") {
			$TLD = TLD_TW;
		} else if ($postfix == ".hk") {
			$TLD = TLD_HK;
		} else {
			return -1;
		}
		return $TLD;
	}
	
	private function get_domain_prefix($name) {
		if (strpos($name, '.') === false) {
			$prefix = $name;
		} else {
			$prefix = substr($name, 0, strpos($name, '.'));
		}
		return $prefix;
	}

	private function get_whois_online($whoisserv, $domain) {
		$result = '';
		$fp = fsockopen($whoisserv, 43, $errno, $errstr, 300);
		if (!$fp) {
			$result = "$errstr ($errno)<br />\n";
		} else {
			$command = $domain."\r\n";
			fwrite($fp, $command);
			while (!feof($fp)) {
				$result .= fgets($fp, 128);
			}
			fclose($fp);
		}
		return $result;
	}

	private function get_whois_server($whois) {
		$info = array();
		if (preg_match_all("/Whois Server: ([^\r\n]*)/i", $whois, $info)) {
			return $info[1][0];
		} else {
			return '';
		}
	}
}
?>