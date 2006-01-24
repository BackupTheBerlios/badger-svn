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

class Currency {

	private $id;
	private $symbol;
	private $longName;

	public function Currency($id, $symbol, $longName) {
		$this->id = $id;
		$this->symbol = $symbol;
		$this->longName = $longName;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getSymbol() {
		return $this->symbol;
	}
	
	public function getLongName() {
		return $this->longName;
	}
}
?>