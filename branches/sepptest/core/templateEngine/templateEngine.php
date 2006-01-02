<?php
/*
 * Created on 02.01.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function getTemplate($template) {
	global $templatecache;

	if(!isset($templatecache[$template])) {
		$filename = './tpl/'.$template.'.tpl';
		if(file_exists($filename)) {
			$templatefile=str_replace("\"","\\\"",implode(file($filename),''));
		} else 	{
			$templatefile='<!-- TEMPLATE NOT FOUND: '.$filename.' -->';
		}
		$templatefile = preg_replace("'<if ([^>]*?)>(.*?)</if>'si", "\".( (\\1) ? \"\\2\" : \"\").\"", $templatefile);
		$templatecache[$template] = $templatefile;
	}
	return $templatecache[$template];
}
?>