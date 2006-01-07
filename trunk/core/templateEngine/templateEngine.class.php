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
	private $css;
	private $javascripts;

	function __construct($themename, $badgerRoot) {
		$this->theme = $themename; 
		$this->badgerRoot = $badgerRoot;		
	}
	
	public function getTemplate($template) {		
	
		if(!isset($templatecache[$template])) {
			$filename = $this->badgerRoot.'/tpl/'.$this->theme.'/'.$template.'.tpl';
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
	
	public function getHeader($pageTitle) {		
		$template = "badgerHeader";
		//leider kann ich das nicht in das template kopieren, da es probleme mit den ? gibt
		echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
		$css = $this->css;
		$javascripts = $this->javascripts;
		return eval("echo \"".$this->getTemplate($template)."\";");
		
	}
	
	public function addCSS($cssFile) {
		$this->css = $this->css."<link href=\"".$this->badgerRoot.'/tpl/'.$this->theme . $cssFile."\" rel=\"stylesheet\" type=\"text/css\" />";
	}
	public function addJavaScript($JSFile) {
		$this->javascripts = $this->javascripts."<script type=\"text/javascript\" src=\"".$this->badgerRoot.$JSFile."\"></script>";
	}
	
	
	public function getThemeName() {
		return $this->theme;
	}
}
?>