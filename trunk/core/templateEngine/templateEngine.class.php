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
/**
 * Template-Engine Class
 * Get the Template (*.tpl) from the tpl-Folder
 * 
 * @author Sepp
 */
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
				$templatefile=str_replace("{BADGER_ROOT}",$this->badgerRoot,$templatefile);
			} else 	{
				$templatefile='<!-- TEMPLATE NOT FOUND: '.$filename.' -->';
			}
			$templatefile = preg_replace("'<if ([^>]*?)>(.*?)</if>'si", "\".( (\\1) ? \"\\2\" : \"\").\"", $templatefile);
			$templatecache[$template] = $templatefile;
		}
		return $templatecache[$template];
	}
	
	/**
	 * function getHeader ($pageTitle)
	 * @param string $pageTitle The name of the XHTML-Page
	 * @return string XHMTL-Header mit CSS, JS ...
	 */
	public function getHeader($pageTitle) {		
		$template = "badgerHeader";
		//leider kann ich das nicht in das template kopieren, da es probleme mit den ? gibt
		echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
		$css = $this->css;
		$javascripts = $this->javascripts;
		return eval("echo \"".$this->getTemplate($template)."\";");		
	}
	
	public function addCSS($cssFile) {
		$this->css = $this->css."\t<link href=\"".$this->badgerRoot.'/tpl/'.$this->theme."/".$cssFile."\" rel=\"stylesheet\" type=\"text/css\" />\n";
	}
	public function addJavaScript($JSFile) {
		$this->javascripts = $this->javascripts."\t<script type=\"text/javascript\" src=\"".$this->badgerRoot."/".$JSFile."\"></script>\n";
	}	
	
	public function getThemeName() {
		return $this->theme;
	}
	public function getBadgerRoot() {
		return $this->badgerRoot;
	}

}
?>