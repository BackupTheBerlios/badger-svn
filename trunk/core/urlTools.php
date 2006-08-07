<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://badger.berlios.org 
*
**/
function getCurrentURL() {
	$result = array();
	
	if (!isset($_SERVER['HTTPS'])) {
		$result['scheme'] = 'http';
	} else {
		$result['scheme'] = 'https';
	}
	
	$result['host'] = $_SERVER['HTTP_HOST'];
	
	$result['port'] = $_SERVER['SERVER_PORT'];
	
	if (isset($_SERVER['PHP_AUTH_USER'])) {
		$result['user'] = $_SERVER['PHP_AUTH_USER'];
	}
	
	if (isset($_SERVER['PHP_AUTH_PW'])) {
		$result['pass'] = $_SERVER['PHP_AUTH_PW'];
	}
	
	$parsed = parse_url($_SERVER['REQUEST_URI']);
	
	$result['path'] = $parsed['path'];
	
	if (isset($parsed['query'])) {
		$result['query'] = $parsed['query'];		
	}
	
	return $result;
}

function buildURL($urlParts, $includeUser = false) {
	$result = $urlParts['scheme'];
	$result .= '://';
	if ($includeUser) {
		if (isset($urlParts['user'])) {
			$result .= $urlParts['user'];
			if (isset($urlParts['pass'])) {
				$result .= ':';
				$result .= $urlParts['pass'];
			}
		}
	}
	$result .= $urlParts['host'];
	$result .= ':';
	$result .= $urlParts['port'];
	$result .= '/';
	$result .= htmlpath($urlParts['path']);
	if (isset($urlParts['query'])) {
		$result .= '?';
		$result .= $urlParts['query'];
	}
	if (isset($urlParts['fragment'])) {
		$result .= '#';
		$result .= $urlParts['fragment'];
	}
	
	return $result;
}

function htmlpath($relativePath) {
	$realpath = realpath($relativePath);
	$realpath = str_replace(DIRECTORY_SEPARATOR, '/', $realpath);
	$docroot = $_SERVER['DOCUMENT_ROOT'];
	$htmlpath = str_replace($docroot, '', $realpath);

	return $htmlpath;
}
?>