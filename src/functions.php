<?php

if ( ! defined('NEIL_FUNCTIONS')) {
	define('NEIL_FUNCTIONS', 1);

	function d($variable) {
		echo '<meta charset="UTF-8">';
		echo '<pre>';
		var_dump($variable);
		die;
	}

	function r($variable) {
		echo '<meta charset="UTF-8">';
		echo '<pre>';
		print_r($variable);
		die;
	}

	function s($variable) {
		echo '<meta charset="UTF-8">';
		echo '<pre>';
		echo $variable;
		die;
	}

	function j($data) {
		return json_encode($data);
	}

	function ie($version = false) {
		if (empty($_SERVER['HTTP_USER_AGENT'])) return false;
		$agent = $_SERVER['HTTP_USER_AGENT'];
		if ($version == false) {
			return strpos($agent, 'MSIE') !== false;
		} else {
			if (is_array($version)) {
				foreach ($version as $v) {
					$v = (int)$v;
					if (strpos($agent, "MSIE {$v}.0") !== false) {
						return true;
					}
				}
				return false;
			} else {
				$version = (int)$version;
				return strpos($agent, "MSIE {$version}.0") !== false;
			}
		}
	}

	function down($file, $name = null, $attachment = true, $mimeType = null, $headers = []) {
		file_exists($file) or die;
		$size = filesize($file);
		$name = $name ?: basename($file);
		header('Cache-Control: public, must-revalidate, max-age=0');
		if ( ! ie()) {
			header("Cache-Control: no-cache");
			header("Pragma: no-cache");
		}
		foreach ($headers as $k => $v) {
			header($k.': '.$v);
		}
		if ($mimeType) {
			header('Content-Type: '.$mimeType);
		} else {
			header('Content-Type: application/octet-stream');
			header("Content-Transfer-Encoding: binary");
		}
		header('Accept-Ranges: bytes');
		header('Accept-Length: '.$size);
		if ($attachment) {
			header('Content-Disposition: attachment; filename="'.rawurlencode($name).'"; filename*=utf-8\'\''.rawurlencode($name));
		}
		ob_clean();
		flush();
		readfile($file);
	}

	function mobile() {
		if (empty($_SERVER['HTTP_USER_AGENT'])) return false;
	  $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
	  return strpos($ua, 'mobile') !== false;
	}

	function wx() {
		if (empty($_SERVER['HTTP_USER_AGENT'])) return false;
	  $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
	  return strpos($ua, 'micromessenger') !== false;
	}

	function ip() {
		$ip = null;
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	  } elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
	    $ip = $_SERVER["HTTP_CLIENT_IP"];
		} elseif (isset($_SERVER["REMOTE_ADDR"])) {
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		return $ip;
	}

	function intranet($ip = null) {
		if (is_null($ip)) $ip = ip();
		if (empty($ip)) return false;
		if ($ip == '::1' || $ip == '127.0.0.1') return true;
		$ip = ip2long($ip);
		$net_a = ip2long('10.255.255.255') >> 24; //A类网预留ip的网络地址 
		$net_b = ip2long('172.31.255.255') >> 20; //B类网预留ip的网络地址 
		$net_c = ip2long('192.168.255.255') >> 16; //C类网预留ip的网络地址 
		return $ip >> 24 === $net_a || $ip >> 20 === $net_b || $ip >> 16 === $net_c; 
	}

	function curl_get_contents($url, $timeout = 60) { 
		$curlHandle = curl_init();
		curl_setopt($curlHandle , CURLOPT_URL, $url);
		if (strpos($url, 'https://') === 0) {
			curl_setopt($curlHandle , CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($curlHandle , CURLOPT_SSL_VERIFYPEER, 0);
		}
		curl_setopt($curlHandle , CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curlHandle , CURLOPT_TIMEOUT, $timeout);
		$result = curl_exec($curlHandle);
		curl_close($curlHandle);
		return $result;
	}

	function curl_get_file($url, $fp, $timeout = 600) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_exec($ch);
		curl_close($ch);
	}

  function copyfile($srcfile, $dstfile) {
    if ( ! file_exists($srcfile)) return;
    $dstdir = pathinfo($dstfile, PATHINFO_DIRNAME);
    if ( ! file_exists($dstdir)) {
      @mkdir($dstdir, 0777, true);
    }
    copy($srcfile, $dstfile);
  }

	function copydir($src, $dst) {  // 原目录，复制到的目录
		if ( ! file_exists($src)) return false;
		$dir = opendir($src);
		@mkdir($dst, 0777, true);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					copydir($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
		return true;
	}

	function deldir($dir) {
		if ( ! file_exists($dir)) return false;
	  $dh = opendir($dir);
	  while ($file = readdir($dh)) {
	    if($file != "." && $file != "..") {
	      $fullpath = $dir."/".$file;
	      if( ! is_dir($fullpath)) {
	        unlink($fullpath);
	      } else {
	        deldir($fullpath);
	      }
	    }
	  }
	  closedir($dh);
	  if (rmdir($dir)) {
	    return true;
	  } else {
	    return false;
	  }
	}

}
