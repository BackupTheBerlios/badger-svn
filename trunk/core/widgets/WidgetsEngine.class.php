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
 * Widget-Engine Class
 * Insert
 * 	- ToolTip
 *  - Calendar
 *  - AutoComlepte aka Suggest
 * 
 * @author Sepp, Tom
 */
class WidgetEngine {
	private $ToolTipJSAdded = false;
	private $ToolTipLayerAdded = false;
	private $AutoCompleteJSAdded = false;
	private $CalendarJSAdded = false;	
	private $TplEngine;
	private $settings;
	
	public function __construct($TplEngine) {
		$this->TplEngine = $TplEngine;
		$this->settings = $this->TplEngine->getSettingsObj();
	}
	
	private function getFormatedDateToday($format) {
		// convert dateformat to php date format
		$format = str_replace("dd","d", $format);
		$format = str_replace("mm","m", $format);
		$format = str_replace("yyyy","Y", $format);
		return date($format, time());
	}
	public function addToolTipJS() {
		$this->TplEngine->addJavaScript("js/overlib_mini.js");
		$this->TplEngine->addJavaScript("js/overlib_cssw3c.js");
		$this->ToolTipJSAdded = true;
	}
	public function addCalendarJS() {
		$this->TplEngine->addJavaScript("js/calendar.js.php?badgerRoot=".$this->TplEngine->getBadgerRoot());
		$this->TplEngine->addOnLoadEvent("initCalendar();");
		$this->CalendarJSAdded = true;
	}
	public function addAutoCompleteJS() {
		$this->TplEngine->addJavaScript("js/SuggestFramework.js");
		$this->TplEngine->addOnLoadEvent("initializeSuggestFramework();");
		$this->AutoCompleteJSAdded = true;
	}
	
	public function addToolTipLayer() {
		$this->ToolTipLayerAdded = true;
		return "<div id=\"overDiv\" style=\"position:absolute; visibility:hidden; z-index:1000;\"></div>\n";	
	}
	
	public function addToolTipLink($link, $text, $linkname) {
		if($this->ToolTipJSAdded) {
			if ($this->ToolTipLayerAdded) {
				return "<a href=\"".$link."\" class=\"ToolTip\" onmouseover=\"return overlib('".$text."', DELAY, 700, CSSW3C, DIVCLASS, 'TTDiv', BODYCLASS, 'TTbodyText');\" onmouseout=\"return nd();\">".$linkname."</a>\n";
			} else 	{
				throw new badgerException('widgetsEngine.ToolTipLayerNotAdded', '');
			}
		} else {
			throw new badgerException('widgetsEngine.ToolTipJSNotAdded', ''); 
		}
		
	}
	public function addDateField($fieldname, $startdate="") {
		$format = $this->settings->getProperty("badgerDateFormat");
		if($startdate=="") {$startdate=$this->getFormatedDateToday($format);}
		
		$strDateField = ""; 
		if($this->CalendarJSAdded) {
			$strDateField = "<input type=\"text\" name=\"".$fieldname."\" size=\"10\" maxlength=\"10\" value=\"".$startdate."\" />\n"; 
			$strDateField .= "<a href=\"javascript:void(0)\" onclick='showCalendar(this, mainform.".$fieldname.", \"".$format."\",1,-1,-1)'><img src=\"".BADGER_ROOT."/tpl/".$this->TplEngine->getThemeName()."/Widgets/calendar/calendar.jpg\" border=\"0\"/></a>\n";
			return $strDateField;
		} else {
			throw new badgerException('widgetsEngine.CalendarJSNotAdded', ''); 
		}
	}
	
	public function addAutoCompleteField($fieldname) {
		if($this->AutoCompleteJSAdded) {
			return "<input id=\"".$fieldname."\" name=\"".$fieldname."\" type=\"text\" action=\"autocomplete.html\" />";
		} else {
			throw new badgerException('widgetsEngine.AutoCompleteJSNotAdded', ''); 
		}
	}
	
	public function createLabel($field, $name, $mandatory=false) {
		if($mandatory) {
			return "<label for='$field' class='mandatory'>$name</label>";
		} else {
			return "<label for='$field'>$name</label>";
		}
	}
	
	public function createField($fieldname, $size, $value="", $description="", $mandatory=false){
		$output = "<input type='text' id='$fieldname' name='$fieldname' size='$size' value='$value' />";
		if($description) {
			$helpImg = "<img src='".$this->TplEngine->getBadgerRoot()."/tpl/".$this->TplEngine->getThemeName()."/Widgets/help.png' border='0' />";
			$output .= "&nbsp;" . $this->addToolTipLink("javascript:void(0)", $description, $helpImg);
		}
		return $output;
	}
	public function createButton($name, $action, $img){
		//ToDo
			
	}
	
}