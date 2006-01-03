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

require_once 'Navigation.iface.php';

/**
 * Renders the Navigation with the @link http://www.tohzhiqiang.per.sg/projects/jsdomenubar/ jsDOMenuBar by Toh Zhiqiang.
 *
 * @author Eni Kao
 * @version $LastChangedRevision$
 */
class StandardNavigation implements Navigation {
	
	/**
	 * Copy of the $structure parameter of @link setStructure setStructure,
	 * augmented by @link parseIcons parseIcons.
	 * 
	 * @var array
	 */
	private $structure;
	
	/**
	 * Registers initjsDOMenu for body.onload.
	 */
	public function __construct() {
		//need to register initjsDOMenu() for body.onload
	}
	
	public function setStructure($structure) {
		$this->structure = $structure;
	}
	
	public function getHeader() {
		$staticLinks = '<link rel="stylesheet" type="text/css" href="js/jsDOMenuBar/themes/office_xp/office_xp.css" />
			<script type="text/javascript" src="js/jsDOMenuBar/jsdomenu.js"></script>
			<script type="text/javascript" src="js/jsDOMenuBar/jsdomenubar.js"></script>
		';
		//<script type="text/javascript" src="core/navi/StandardNavigation.js.php"></script>
		
		//Create CSS classes for icons
		$result = $staticLinks . '<style type="text/css">';
		
		$result .= $this->parseIcons('icons', $this->structure);
		
		$result .= '</style>';
		
		return $result;
	}
	
	public function getHTML() {
		$result = '<script type="text/javascript">
			menuBar = new jsDOMenuBar("fixed");
		';
		
		$menuNum = 0;
		
		foreach ($this->structure as $mainElement) {
			switch ($mainElement['type']) {
				case 'item':
					$action = $this->calcCommand($mainElement['command']);
						
					//Add MenuItem
					$result .= "menuBar.addMenuBarItem(new menuBarItem('$mainElement[name]', '', 'mainMenu$menuNum', '', '$action'));\n";
					
					//Show icon, if available
					if (isset($mainElement['iconId'])) {
						$result .= "menuBar.items.mainMenu$menuNum.showIcon('$mainElement[iconId]');\n";
					}
					
					$menuNum++;
					break;
					
				case 'menu':
					$result .= $this->renderSubMenu("mainMenu$menuNum", $mainElement['menu']);
					
					//Add SubMenu
					$result .= "\nmenuBar.addMenuBarItem(new menuBarItem('$mainElement[name]', mainMenu$menuNum, 'mainMenu$menuNum'));\n";

					//Show icon, if available
					if (isset($mainElement['iconId'])) {
						$result .= "menuBar.items.mainMenu$menuNum.showIcon('$mainElement[iconId]');\n";
					}
					
					$menuNum++;
				break;
				
				//jsDOMenuBar does not support separators on top level
			}
		}
		
		$result .= '</script>';
		
		return $result;
	}
	
	/**
	 * Walks recursively through $structure, creates CSS classes in result 
	 * and iconId properties in $this->structure
	 * 
	 * @param array $name The name of this level
	 * @param array $structure The unprocessed sub-treee of $this->structure
	 * 
	 * @return string A string with all requiered CSS classes
	 */
	private function parseIcons($name, &$structure) {
		$result = '';
		
		$numElement = 0;
		foreach ($structure as $key => $currentElement) {
			$iconId = "{$name}_{$numElement}";
			
			if (isset($currentElement['icon'])) {
				$result .=  ".$iconId {
					background-image: url('$currentElement[icon]');
					background-repeat: no-repeat; /* Do not alter this line! */
					height: 16px;
					left: 2px;
					position: absolute; /* Do not alter this line! */
					width: 16px;
				 }\n";
				
				$structure[$key]['iconId'] = $iconId; 
				
				$numElement++;
			}

			//walk through recursively
			if ($currentElement['type'] == 'menu') {
				$result .= $this->parseIcons($iconId, $currentElement['menu']);
			}
		}
		
		return $result;
	} 

	/**
	 * Calculates menu width.
	 * 
	 * This is essentially a hack, as we guess the relationship of small vs. wide characters.
	 * 
	 * @param string $longestName The longest name in the sub-Menu.
	 * @return string A correct value for CSS property width
	 */
	private function calcMenuWidth($longestName) {
		return ((int) ((strlen($longestName) * 1.3) + 3)) . 'ex';
	}
	
	/**
	 * Translates internal javascript command to the one used by jsDOMenuBar.
	 * 
	 * @param string $command A command in the format of $structure
	 * @return string The command translated to jsDOMenuBar format.
	 */
	private function calcCommand($command) {
		if (substr($command, 0, 11) != 'javascript:') {
			return $command;
		} else {
			return 'code:' . substr($command, 11);
		}
	}
	
	/**
	 * Recursively translates the internal $structure to JavaScript calls suited to jsDOMenuBar.
	 * 
	 * @param string $menuName The name of this sub-menu
	 * @param array $structure The sub-structure inside this sub-menu
	 * @return string The JavaScript calls for jsDOMMenuBar
	 */
	private function renderSubMenu($menuName, $structure) {

		$longestName = '';
		$menuNum = 0;
		
		$result = '';
		
		foreach ($structure as $currentElement) {
			$currentId = "{$menuName}_{$menuNum}";
			
			switch($currentElement['type']) {
				case 'separator':
					//add separator
					$result .= "$menuName.addMenuItem(new menuItem('-'));\n";
					break;
				
				case 'item':
					//add MenuItem
					$result .= "$menuName.addMenuItem(new menuItem('$currentElement[name]', '$currentId', '" . $this->calcCommand($currentElement['command']) . "'));\n";
					
					//add icon, if defined
					if (isset($currentElement['iconId'])) {
						$result .= "$menuName.items.$currentId.showIcon('$currentElement[iconId]');\n";
					}

					//calculate longest name
					if (strlen($longestName) < strlen($currentElement['name'])) {
						$longestName = $currentElement['name'];
					}

					$menuNum++;
					break;
					
				case 'menu':
					//add sub-menu
					$result .= "$menuName.addMenuItem(new menuItem('$currentElement[name]', '$currentId'));\n";
					$result .= $this->RenderSubMenu($currentId, $currentElement['menu']);;
					$result .= "$menuName.items.$currentId.setSubMenu($currentId)\n";

					//add icon, if defined
					if (isset($currentElement['iconId'])) {
						$result .= "$menuName.items.$currentId.showIcon('$currentElement[iconId]');\n";
					}
					
					//calculate longest name
					if (strlen($longestName) < strlen($currentElement['name'])) {
						$longestName = $currentElement['name'];
					}

					$menuNum++;
					break;
			}
		}
		
		//we know only now how wide the menu should be, but the calls above refer to this JS object.
		//Therefore we prepend the Menu creation call to $result
		$result = "$menuName = new jsDOMenu('" . $this->calcMenuWidth($longestName) . "', 'fixed');\n" . $result;
		
		return $result;
	}
}
?>