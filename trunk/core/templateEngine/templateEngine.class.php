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
	private $additionalHeaderTags;
	private $settings;
	private $jsOnLoadEvents = array();

	function __construct($settings, $badgerRoot) {
		$this->settings = $settings;
		$this->theme = $this->settings->getProperty("badgerTemplate");
		$this->badgerRoot = $badgerRoot;		
	}	
	
	function getSettingsObj() {
		return $this->settings;
	}
	
	public function getTemplate($template) {	
		if(!isset($templatecache[$template])) {
			$filename = $this->badgerRoot.'/tpl/'.$this->theme.'/'.$template.'.tpl';
			if(file_exists($filename)) {
				$templatefile=str_replace("\"","\\\"",implode(file($filename),''));
				$templatefile=str_replace("{BADGER_ROOT}",$this->badgerRoot,$templatefile);
			} else 	{
				throw new badgerException('templateEngine.noTemplate', $this->badgerRoot.'/tpl/'.$this->theme.'/'.$template.'.tpl'); 
				//$templatefile='<!-- TEMPLATE NOT FOUND: '.$filename.' -->';
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
		
		// create Page Title
		$pageTitle .= " - ".$this->settings->getProperty("badgerSiteName");
		
		// write XHTML-Header
		// leider kann ich das nicht in das template kopieren, da es probleme mit den ? gibt
		echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
		
		// transfer additionalHeaderTags (JS, CSS) to $var ($var must be in template)
		$additionalHeaderTags = $this->additionalHeaderTags;
		
		// create onload-Event
		if($this->jsOnLoadEvents) {
			$JSOnLoadEvents = "\t<script type=\"text/javascript\">\n";
			$JSOnLoadEvents .=  "\twindow.onload = function () {\n";		
			foreach ($this->jsOnLoadEvents as $key => $value) {
	        	$JSOnLoadEvents .= "\t\t".$value."\n";
	        }
	        $JSOnLoadEvents .= "\t}\n";
	        $JSOnLoadEvents .= "\t</script>";
		}
		
		// write complete header
		return eval("echo \"".$this->getTemplate($template)."\";");		
	}
	
	public function addCSS($cssFile) {
		$this->additionalHeaderTags = $this->additionalHeaderTags."\t<link href=\"".$this->badgerRoot.'/tpl/'.$this->theme."/".$cssFile."\" rel=\"stylesheet\" type=\"text/css\" />\n";
	}
	public function addJavaScript($JSFile) {
		$this->additionalHeaderTags = $this->additionalHeaderTags."\t<script type=\"text/javascript\" src=\"".$this->badgerRoot."/".$JSFile."\"></script>\n";
	}
	public function addHeaderTag($HeaderTag) {
		$this->additionalHeaderTags = $this->additionalHeaderTags."\t".$HeaderTag."\n";
	}	
	public function addOnLoadEvent($eventFunction) {
		$this->jsOnLoadEvents[] = "$eventFunction";
	}
		
	public function getThemeName() {
		return $this->theme;
	}
	public function getBadgerRoot() {
		return $this->badgerRoot;
	}

}
?>