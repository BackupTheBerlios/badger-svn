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
	
	public function __construct($TplEngine) {
		$this->TplEngine = $TplEngine;
	}
	
	public function addToolTipJS() {
		$this->TplEngine->addJavaScript("js/overlib_mini.js");
		$this->TplEngine->addJavaScript("js/overlib_cssstyle_mini.js");
		$this->ToolTipJSAdded = true;
	}
	public function addCalendarJS() {
		$this->TplEngine->addJavaScript("js/calendar.js.php?badgerRoot=".$this->TplEngine->getBadgerRoot()."&badgerTemplate=".$this->TplEngine->getThemeName());
		$this->TplEngine->addHeaderTag("<script type=\"text/javascript\">initCalendar();</script>");
		$this->CalendarJSAdded = true;
	}
	public function addAutoCompleteJS() {
		$this->TplEngine->addJavaScript("js/SuggestFramework.js");
		$this->TplEngine->addHeaderTag("<script type=\"text/javascript\">window.onload = initializeSuggestFramework;</script>");
		$this->AutoCompleteJSAdded = true;
	}
	
	public function addToolTipLayer() {
		$this->ToolTipLayerAdded = true;
		return "<div id=\"overDiv\" style=\"position:absolute; visibility:hidden; z-index:1000;\"></div>\n";	
	}
	
	public function addToolTipLink($link, $text, $linkname) {
		if($this->ToolTipJSAdded) {
			if ($this->ToolTipLayerAdded) {
				return "<a href=\"".$link."\" class=\"ToolTip\" onmouseover=\"return overlib('".$text."', DELAY, 700);\" onmouseout=\"return nd();\">".$linkname."</a>\n";
			} else 	{
				throw new badgerException('widgetsEngine.ToolTipLayerNotAdded', '');
			}
		} else {
			throw new badgerException('widgetsEngine.ToolTipJSNotAdded', ''); 
		}
		
	}
	public function addDateField($fieldname, $startdate, $format) {
		$strDateField = ""; 
		if($this->CalendarJSAdded) {
			$strDateField = "<input type=\"text\" name=\"".$fieldname."\" size=\"10\" maxlength=\"10\" value=\"".$startdate."\" />\n"; 
			$strDateField .= "<input type='button' onclick='showCalendar(this, mainform.".$fieldname.", \"".$format."\",1,-1,-1)' value='select' />\n";
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
}