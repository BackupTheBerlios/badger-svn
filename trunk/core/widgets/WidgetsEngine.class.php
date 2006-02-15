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
	private $tpl;
	private $settings;
	private $writtenHeader = false;
	private $inputIds = array ();
	private $labelIds = array ();
	
	public function __construct($tpl) {
		$this->tpl = $tpl;
		$this->settings = $this->tpl->getSettingsObj();
	}
	
	private function getFormatedDateToday($format) {
		// convert dateformat to php date format
		$format = str_replace("dd","d", $format);
		$format = str_replace("mm","m", $format);
		$format = str_replace("yyyy","Y", $format);
		return date($format, time());
	}
	public function addToolTipJS() {
		$this->tpl->addJavaScript("js/overlib_mini.js");
		$this->tpl->addJavaScript("js/overlib_cssw3c.js");
		$this->ToolTipJSAdded = true;
	}
	public function addCalendarJS() {
		$this->tpl->addJavaScript("js/calendar.js.php?badgerRoot=".$this->tpl->getBadgerRoot());
		$this->tpl->addOnLoadEvent("initCalendar();");
		$this->CalendarJSAdded = true;
	}
	public function addAutoCompleteJS() {
		$this->tpl->addJavaScript("js/SuggestFramework.js");
		$this->tpl->addOnLoadEvent("initializeSuggestFramework();");
		$this->AutoCompleteJSAdded = true;
	}
	
	public function addToolTipLayer() {
		if ($this->tpl->isHeaderWritten()) {		
			$this->ToolTipLayerAdded = true;
			return "<div id=\"overDiv\" style=\"position:absolute; visibility:hidden; z-index:1000;\"></div>\n";	
		} else {
			throw new badgerException('widgetsEngine', 'HeaderIsNotWritten', 'Function: addToolTipLayer()'); 
		}
	}
	
	public function addToolTipLink($link, $text, $linkname="") {
		if($this->ToolTipJSAdded) {
			if ($this->ToolTipLayerAdded) {
				if($link=="") $link = "javascript:void(0)";
				if($linkname=="") $linkname = "<img src='".$this->tpl->getBadgerRoot()."/tpl/".$this->tpl->getThemeName()."/Widgets/help.gif' border='0' />";
				return "<a href=\"".$link."\" class=\"ToolTip\" tabindex=\"-999\" onmouseover=\"return overlib('".$text."', DELAY, 700, CSSW3C, DIVCLASS, 'TTDiv', BODYCLASS, 'TTbodyText');\" onmouseout=\"return nd();\">".$linkname."</a>\n";
			} else 	{
				throw new badgerException('widgetsEngine', 'ToolTipLayerNotAdded');
			}
		} else {
			throw new badgerException('widgetsEngine', 'ToolTipJSNotAdded'); 
		}
		
	}
	public function addDateField($fieldname, $startdate="") {
		$format = $this->settings->getProperty("badgerDateFormat");
		if($startdate=="") {$startdate=$this->getFormatedDateToday($format);}
		
		$strDateField = ""; 
		if($this->CalendarJSAdded) {
			$strDateField = "<input type=\"text\" name=\"".$fieldname."\" size=\"10\" maxlength=\"10\" value=\"".$startdate."\" />\n"; 
			$strDateField .= "<a href=\"javascript:void(0)\" onclick='showCalendar(this, mainform.".$fieldname.", \"".$format."\",1,-1,-1)'><img src=\"".BADGER_ROOT."/tpl/".$this->tpl->getThemeName()."/Widgets/calendar/calendar.jpg\" border=\"0\"/></a>\n";
			return $strDateField;
		} else {
			throw new badgerException('widgetsEngine', 'CalendarJSNotAdded'); 
		}
	}
	
	public function addAutoCompleteField($fieldname) {
		if($this->AutoCompleteJSAdded) {
			return "<input id=\"".$fieldname."\" name=\"".$fieldname."\" type=\"text\" action=\"autocomplete.html\" />";
		} else {
			throw new badgerException('widgetsEngine', 'AutoCompleteJSNotAdded'); 
		}
	}
	
	public function createLabel($field, $name, $mandatory=false) {
		if (!isset($this->labelIds[$field])) {
			$this->labelIds[$field] = 0;
			$id = $field;
		} else {
			$id = $field . '_' . $this->labelIds[$field];
			$this->labelIds[$field]++;
		}


		if($mandatory) {
			return "<label for='$id' class='mandatory'>$name</label>";
		} else {
			return "<label for='$id'>$name</label>";
		}
	}
	
	public function createField($fieldname, $size, $value="", $description="", $mandatory=false, $type="text", $valCondition=""){
		// 'required' and 'regexp' are no XHTML attributes to input field
		// -> we've to add an extended namespace
		
		// formatings of numbers
		$compValue = str_replace(".","", str_replace(",","", $value));
		if (is_numeric($compValue)) {
			if ($compValue<0) {
				$class = "inputNumberMinus";
			} else {
				$class = "inputNumber";
			}
		} else {
			$class = "inputString";
		}
		
		//$valCondition
		//example:
		//    minvalue="10" maxvalue="90" regexp="money"

		//required
		$mandatory = (($mandatory) ? "1" : "0");
		
		if (!isset($this->inputIds[$fieldname])) {
			$this->inputIds[$fieldname] = 0;
			$id = $fieldname;
		} else {
			$id = $fieldname . '_' . $this->inputIds[$fieldname];
			$this->inputIds[$fieldname]++;
		}
		
		$output = "<input type='$type' id='$id' name='$fieldname' size='$size' class='$class' value='$value' required='$mandatory' $valCondition />";
		if($description) {
			$helpImg = $this->addImage("Widgets/help.gif");
			$output .= "&nbsp;" . $this->addToolTipLink("javascript:void(0)", $description, $helpImg);
		}
		return $output;
	}
	
	public function createButton($name, $text, $action, $img="", $addTags=""){
		if ($action=="submit") $action = "this.form.submit()";
		if ($action=="") $action = "void(0);return false;"; 
		$output = "<button $addTags name='$name' id='$name' onclick=\"javascript:".$action."\">\n";
		$output .= "<table cellspacing='0' cellpadding='0'>\n";
		$output .= "\t<tr>\n";
		if ($img) {
			$output .= "\t\t<td>".$this->addImage($img)."</td>\n";
		}
		$output .= "\t\t<td nowrap='nowrap'>&nbsp;$text</td>\n";
		$output .= "\t</tr>\n";
		$output .= "</table>\n";
		$output .= "</button>";
			
		return $output;
	}
	
	public function addImage($file) {
		return "<img src='".$this->tpl->getBadgerRoot()."/tpl/".$this->tpl->getThemeName()."/$file' border='0'/>";
	}
	
	public function createSelectField($name, $options, $default="", $description="", $mandatory=false, $selectAdditional="") {	
		$selectField = "<select name='$name' id='$name' $selectAdditional >\n";
		if(isset($options)) {
			foreach( $options as $key=>$value ){
				//default value
				$selected = (($key==$default) ? "selected" : "");
				//options 
				$selectField .= "\t<option $selected value='$key'>$value</option>\n";
			};
		}
		$selectField .= "</select>\n";
		if($description) {
			$helpImg = $this->addImage("Widgets/help.gif");
			$selectField .= $this->addToolTipLink("", $description, $helpImg);
		}
		return $selectField;
	}
	function addNavigationHead() {
		$tplNavigationHead = "";
		//Navigation Head
		if (!$this->tpl->isHeaderWritten()) {
			eval("\$tplNavigationHead = \"".$this->tpl->getTemplate("Navigation/header")."\";");
			$this->tpl->addHeaderTag($tplNavigationHead);
		} else {
			throw new badgerException('widgetsEngine', 'HeaderIsAlreadyWritten', 'Function: addNavigationHead()'); 
		}
		
	}
	function getNavigationBody() {
		$tplNavigationBody = "";
		//Navigation Body
		if ($this->tpl->isHeaderWritten()) {
			eval("\$tplNavigationBody = \"".$this->tpl->getTemplate("Navigation/body")."\";");
			return $tplNavigationBody;
		} else {
			throw new badgerException('widgetsEngine', 'HeaderIsNotWritten', 'Function: getNavigationBody()'); 
		}		
	}
}