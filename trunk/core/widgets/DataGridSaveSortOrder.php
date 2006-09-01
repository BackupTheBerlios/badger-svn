<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://www.badger-finance.org 
*
**/
/**
 * DataGridSaveSortOrder
 * - save sort column and order to user settings
 * - called by dataGrid.js via background http request
 *  
 * @author Sepp
 */
define ('BADGER_ROOT', '../..');
require_once BADGER_ROOT . '/includes/fileHeaderBackEnd.inc.php';

$us->setProperty('dgSortColumn'.$_POST['id'], $_POST['column']);
$us->setProperty('dgSortOrder'.$_POST['id'], $_POST['order']);

?>
