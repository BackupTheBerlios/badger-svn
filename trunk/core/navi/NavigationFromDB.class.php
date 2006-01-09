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
 * Creates the internal navigation structure described by @link Navigation::setStructure 
 * out of the badger database.
 * 
 * @author Eni Kao
 * @version $LastChangedRevision$ 
 */
class NavigationFromDB {
	/**
	 * Creates the internal navigation structure described by @link Navigation::setStructure 
	 * out of the badger database.
	 * 
	 * @return array The navigation structure.
	 */
	public static function getNavigation() {
		global $badgerDb;
		
		$itemTypes = array (
			'i' => 'item',
			'm' => 'menu',
			's' => 'separator'
		);

		$sql = 'SELECT navi_id, parent_id, menu_order, item_type, item_name, tooltip, icon_url, command
			FROM navi
			ORDER BY parent_id, menu_order';
		
		$res =& $badgerDb->query($sql);

		$menus = array();
		
		$row = array();
		
		while ($res->fetchInto($row, DB_FETCHMODE_ASSOC)) {
			$menuId = $row['parent_id'];
			
			//create containing menu if it does not exist
			if (!isset($menus[$menuId])) {
				$menus[$menuId] = array();
			}
			
			//fill most of the fields
			$menus[$menuId][] = array (
				'type' => $itemTypes[$row['item_type']],
				'name' => $row['item_name'],
				'tooltip' => $row['tooltip'],
				'icon' => NavigationFromDB::replaceBadgerRoot($row['icon_url']),
				'command' => NavigationFromDB::replaceBadgerRoot($row['command'])
			);
			
			//if current row is a menu
			if ($row['item_type'] == 'm') {
				//create sub-menu if it does not exist
				if (!isset($menus[$row['navi_id']])) {
					$menus[$row['navi_id']] = array();
				}
				
				//add menu field to the previously created item and assign a reference to the proper
				//sub-menu to it
				$menus[$menuId][count($menus[$menuId]) - 1]['menu'] =& $menus[$row['navi_id']];
			}
		}
		
		//All sub-menus are within element 0 as references
		return $menus[0];
	}
	
	/**
	 * Replaces the string '{BADGER_ROOT}' by the value of the constant of the same name.
	 * 
	 * @param string $str The string to search for {BADGER_ROOT}
	 * @return string The input string with replaced {BADGER_ROOT}
	 */
	private static function replaceBadgerRoot($str) {
		return str_replace('{BADGER_ROOT}', BADGER_ROOT, $str);
	}
}
?>