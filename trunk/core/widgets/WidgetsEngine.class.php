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
 * @author Sepp, Tom
 */
class WidgetEngine {
	
	public function addToolTipLayer() {
		echo "<div id=\"overDiv\" style=\"position:absolute; visibility:hidden; z-index:1000;\"></div>\n";	
	}
	
	public function addToolTipLink($link, $text, $linkname) {
		echo "<a href=\"".$link."\" class=\"ToolTip\" onmouseover=\"return overlib('".$text."', DELAY, 700);\" onmouseout=\"return nd();\">".$linkname."</a>\n";
	}
}