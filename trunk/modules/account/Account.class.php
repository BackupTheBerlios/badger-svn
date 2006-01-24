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

require_once (BADGER_ROOT . '/core/Amount.class.php');
require_once (BADGER_ROOT . '/modules/account/Currency.class.php');

class Account {
	
	private $id;
	private $title;
	private $description;
	private $lowerLimit;
	private $upperLimit;
	private $balance;
	private $currency;
	
	private $accountManager;
	
	function Account($accountManager, $data) {
		$this->accountManager = $accountManager;
		
		$this->id = $data['account_id'];
		$this->title = $data['title'];
		$this->description = $data['description'];
		$this->lowerLimit = $data['lower_limit'];
		$this->upperLimit = $data['upper_limit'];
		$this->balance = new Amount($data['balance']);
		$this->currency = new Currency($data['currency_id'], $data['currency_symbol'], $data['currency_long_name']);
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getLowerLimit() {
		return $this->lowerLimit;
	}
	
	public function getUpperLimit() {
		return $this->upperLimit;
	}
	
	public function getBalance() {
		return $this->balance;
	}
	
	public function getCurrency() {
		return $this->currency;
	}
}
?>