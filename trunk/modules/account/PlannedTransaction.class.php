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

require_once BADGER_ROOT . '/core/Date.php';
require_once BADGER_ROOT . '/core/Amount.class.php';
require_once BADGER_ROOT . '/modules/account/Category.class.php';

class PlannedTransaction {
	private $badgerDb;
	private $account;
	
	private $id;
	private $title;
	private $description;
	private $amount;
	private $outsideCapital;
	private $transactionPartner;
	private $category;
	private $beginDate;
	private $endDate;
	private $repeatUnit;
	private $repeatFrequency;
	
    function __construct(&$badgerDb, $account, $data, $repeatUnit = null, $repeatFrequency = null, $beginDate = null, $endDate = null, $title = null, $amount = null, $description = null, $transactionPartner = null, $category = null, $outsideCapital = null) {
    	global $CategoryManager;
    	
    	$this->badgerDb = $badgerDb;
    	$this->account = $account;
    	
    	if (is_array($data)) {
			$this->id = $data['planned_transaction_id'];
    		$this->title = $data['title'];
    		$this->description = $data['description'];
    		$this->amount = new Amount($data['amount']);
    		if (is_bool($data['outside_capital'])) {
	    		$this->outsideCapital = $data['outside_capital'];
    		}
    		$this->transactionPartner =  $data['transaction_partner'];
    		if ($data['category_id']) {
    			$this->category = $CategoryManager->getCategoryById($data['category_id']);
    		}
    		$this->beginDate = new Date($data['begin_date']);
    		if ($data['end_date']) {
    			$this->endDate = new Date($data['end_date']);
    		}
    		$this->repeatUnit = $data['repeat_unit'];
    		$this->repeatFrequency = $data['repeat_frequency'];
    	} else {
    		$this->id = $data;
    		$this->title = $title;
    		$this->description = $description;
    		$this->amount = $amount;
    		$this->outsideCapital = $outsideCapital;
    		$this->transactionPartner = $transactionPartner;
    		$this->category = $category;
    		$this->beginDate = $beginDate;
    		$this->endDate = $endDate;
    		$this->repeatUnit = $repeatUnit;
    		$this->repeatFrequency = $repeatFrequency;
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
		
		$sql = "UPDATE planned_transaction
			SET title = '" . $this->badgerDb->escapeSimple($title) . "'
			WHERE planned_transaction_id = " . $this->id;
	
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
		
		$sql = "UPDATE planned_transaction
			SET description = '" . $this->badgerDb->escapeSimple($description) . "'
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('FinishedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
    public function getBeginDate() {
    	return $this->beginDate;
    }
    
 	public function setBeginDate($beginDate) {
		$this->beginDate = $beginDate;
		
		$sql = "UPDATE planned_transaction
			SET begin_date = '" . $beginDate->getDate() . "'
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('FinishedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
    public function getEndDate() {
    	return $this->endDate;
    }
    
 	public function setEndDate($endDate) {
		$this->endDate = $endDate;
		
		$sql = "UPDATE planned_transaction
			SET end_date = '" . $endDate->getDate() . "'
			WHERE planned_transaction_id = " . $this->id;
	
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
		
		$sql = "UPDATE planned_transaction
			SET amount = '" . $amount->get() . "'
			WHERE planned_transaction_id = " . $this->id;
	
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
		
		$sql = "UPDATE planned_transaction
			SET outside_capital = " . $this->badgerDb->quoteSmart($outsideCapital) . "
			WHERE planned_transaction_id = " . $this->id;
	
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
		
		$sql = "UPDATE planned_transaction
			SET transaction_partner = '" . $this->badgerDb->escapeSimple($transactionPartner) . "'
			WHERE planned_transaction_id = " . $this->id;
	
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
		
		$sql = "UPDATE planned_transaction
			SET category_id = " . $category->getId() . "
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('FinishedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}

    public function getRepeatUnit() {
    	return $this->repeatUnit;
    }
 
 	public function setRepeatUnit($repeatUnit) {
		$this->repeatUnit = $repeatUnit;
		
		$sql = "UPDATE planned_transaction
			SET repeat_unit = '" . $repeatUnit . "'
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('FinishedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}

    public function getRepeatFrequency() {
    	return $this->repeatFrequency;
    }
 
 	public function setRepeatFrequency($repeatFrequency) {
		$this->repeatFrequency = $repeatFrequency;
		
		$sql = "UPDATE planned_transaction
			SET repeat_frequency = " . $repeatFrequency . "
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('FinishedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
}
?>