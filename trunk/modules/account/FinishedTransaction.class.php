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

require_once (BADGER_ROOT . '/core/Date.php');
require_once (BADGER_ROOT . '/core/Amount.class.php');
require_once (BADGER_ROOT . '/modules/account/Category.class.php');

class FinishedTransaction {
	private $badgerDb;
	private $account;
	
	private $id;
	private $title;
	private $description;
	private $valutaDate;
	private $amount;
	private $outsideCapital;
	private $transactionPartner;
	private $category;
	
    function __construct(&$badgerDb, $account, $data, $title = null, $amount = null, $description = null, $valutaDate = null, $transactionPartner = null, $category = null, $outsideCapital = false) {
    	global $CategoryManager;
    	
    	$this->badgerDb = $badgerDb;
    	$this->account = $account;
    	
    	if (is_array($data)) {
    		$this->title = $data['title'];
    		$this->description = $data['description'];
    		$this->amount = new Amount($data['amount']);
    		$this->outsideCapital = $data['outside_capital'];
    		$this->transactionPartner =  $data['transaction_partner'];
    		if ($data['category_id']) {
    			$this->category = $CategoryManager->getCategoryById($data['category_id']);
    		}

    		if (isset($data['finished_transaction_id'])) {
	    		$this->id = isset($data['finished_transaction_id']);
	    		if ($data['valuta_date']) {
	    			$this->valutaDate = new Date($data['valuta_date']);
	    		}
    		} else {
    			$this->id = 'x-$title';
    			$this->valutaDate = new Date($amount);
    		}
    	} else {
    		$this->id = $data;
    		$this->title = $title;
    		$this->description = $description;
    		$this->valutaDate = $valutaDate;
    		$this->amount = $amount;
    		$this->outsideCapital = $outsideCapital;
    		$this->transactionPartner = $transactionPartner;
    		$this->category = $category;
    	}
    }
    
    public function getId() {
    	return $this->id;
    }
    
    public function getTitle() {
    	return $this->title;
    }
    
 	public function setTitle($title) {
		$this->title = $title;
		
		$sql = "UPDATE finished_transaction
			SET title = '" . $this->badgerDb->escapeSimple($title) . "'
			WHERE finished_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('FinishedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
    public function getDescription() {
    	return $this->description;
    }
    
 	public function setDescription($description) {
		$this->description = $description;
		
		$sql = "UPDATE finished_transaction
			SET description = '" . $this->badgerDb->escapeSimple($description) . "'
			WHERE finished_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('FinishedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
    public function getValutaDate() {
    	return $this->valutaDate;
    }
    
 	public function setValutaDate($valutaDate) {
		$this->valutaDate = $valutaDate;
		
		$sql = "UPDATE finished_transaction
			SET valuta_date = '" . $valutaDate->getDate() . "'
			WHERE finished_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('FinishedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
    public function getAmount() {
    	return $this->amount;
    }
    
 	public function setAmount($amount) {
		$this->amount = $amount;
		
		$sql = "UPDATE finished_transaction
			SET amount = '" . $amount->get() . "'
			WHERE finished_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('FinishedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
    public function getOutsideCapital() {
    	return $this->outsideCapital;
    }
    
 	public function setOutsideCapital($outsideCapital) {
		$this->outsideCapital = $outsideCapital;
		
		$sql = "UPDATE finished_transaction
			SET outside_capital = " . $this->badgerDb->quoteSmart($outsideCapital) . "
			WHERE finished_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('FinishedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
    public function getTransactionPartner() {
    	return $this->transactionPartner;
    }
    
 	public function setTransactionPartner($transactionPartner) {
		$this->transactionPartner = $transactionPartner;
		
		$sql = "UPDATE finished_transaction
			SET transaction_partner = '" . $this->badgerDb->escapeSimple($transactionPartner) . "'
			WHERE finished_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('FinishedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
    public function getCategory() {
    	return $this->category;
    }
 
 	public function setCategory($category) {
		$this->category = $category;
		
		$sql = "UPDATE finished_transaction
			SET category_id = " . $category->getId() . "
			WHERE finished_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('FinishedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
}
?>