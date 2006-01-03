<?php

require_once 'Navigation.iface.php';

class StandardNavigation implements Navigation {
	private $structure;
	
	public function __construct() {
		//need to register initjsDOMenu() for body.onload
	}
	
	public function setStructure($structure) {
		$this->structure = $structure;
	}
	
	public function getHeader() {
		return '<link rel="stylesheet" type="text/css" href="js/jsDOMenuBar/themes/office_xp/office_xp.css" />
			<script type="text/javascript" src="js/jsDOMenuBar/jsdomenu.js"></script>
			<script type="text/javascript" src="js/jsDOMenuBar/jsdomenubar.js"></script>
		';
			//<script type="text/javascript" src="core/navi/StandardNavigation.js.php"></script>
	}
	
	public function getHTML() {
		$result = '<script type="text/javascript">';
		
		$menuNum = 0;
		
		foreach ($this->structure as $mainElement) {
			switch ($mainElement['type']) {
				case 'item':
					$result .= "mainMenu$menuNum = new jsDOMenu(" . $this->calcMenuWidth($mainElement['name']) . ', "fixed");
						
					';
					break;
			}
		}
	}
	
	private function calcMenuWith($numChars) {
		return $numChars * 3;
	}					
}
?>