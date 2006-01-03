<?php
/*
 * Created on 03.01.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/**
 * Provides the on-site Navigation.
 * 
 * @author Eni Kao, Paraphil
 * @version $LastChangedRevision$ 
 */
interface Navigation {
	
	/**
	 * Sets Navigation Structure
	 * 
	 * @param $structure array the Navigationstructure in the format described above
	 * @return void
	 */
	public function setStructure($structure);
	
	/**
	 * Returns the required HTML header values to include
	 * 
	 * @return string All necessary HTML header tags
	 */
	public function getHeader();
	
	/**
	 * Returns the Navigation as HTML Fragment
	 * 
	 * @return string the Navigation HTML
	 */
	public function getHTML();
}
?>