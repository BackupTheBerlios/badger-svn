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
 * A currency.
 * 
 * @author Eni Kao, Mampfred
 * @version $LastChangedRevision$
 */
class Currency {

	/**
	 * The ID of the currency in the database.
	 * 
	 * @var integer
	 */
	private $id;
	
	/**
	 * The symbol of the currency (e. g. EUR, USD).
	 * 
	 * @var string
	 */
	private $symbol;
	
	/**
	 * The long name of the currency (e. g. Euro United States Dollar).
	 * 
	 * @var string
	 */
	private $longName;

	/**
	 * Creates a currency.
	 * 
	 * @param $id integer The ID of the currency in the database.
	 * @param $symbol string The symbol of the currency.
	 * @param $longName string The long name of the currency.
	 */
	public function Currency($id, $symbol, $longName) {
		$this->id = $id;
		$this->symbol = $symbol;
		$this->longName = $longName;
	}
	
	/**
	 * Returns the ID.
	 * 
	 * @return integer The ID of the currency.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Returns the symbol.
	 * 
	 * @return string The symbol of the currency.
	 */
	public function getSymbol() {
		return $this->symbol;
	}
	
	/**
	 * Returns the long name.
	 * 
	 * @return string The long name of the currency.
	 */
	public function getLongName() {
		return $this->longName;
	}
}
?>