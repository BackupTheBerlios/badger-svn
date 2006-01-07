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
class TemplateEngine {
	private $templatecache;
	private $theme;
	private $badgerRoot;

	function __construct($themename, $badgerRoot) {
		$this->theme = $themename; 
		$this->badgerRoot = $badgerRoot;
		
	}
	
	public function getTemplate($template) {		
	
		if(!isset($templatecache[$template])) {
			$filename = $this->badgerRoot.'tpl/'.$this->theme.'/'.$template.'.tpl';
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
	
	public function getThemeName() {
		return $this->theme;
	}
}
?>