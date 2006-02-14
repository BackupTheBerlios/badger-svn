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
require_once BADGER_ROOT . '/modules/account/CategoryManager.class.php';

/**
 * A finished transaction.
 * 
 * @author Eni Kao, Mampfred
 * @version $LastChangedRevision$
*/
class PlannedTransaction {
	/**
	 * The DB object.
	 * 
	 * @var object DB
	 */
	private $badgerDb;

	/**
	 * The Account this category belongs to.
	 * 
	 * @var object Account
	 */
	private $account;
	
	/**
	 * The id of this transaction.
	 * 
	 * @var integer
	 */
	private $id;

	/**
	 * The title of this transaction.
	 * 
	 * @var string
	 */
	private $title;

	/**
	 * The description of this transaction.
	 * 
	 * @var string
	 */
	private $description;

	/**
	 * The amount of this transaction.
	 * 
	 * @var object Amount
	 */
	private $amount;

	/**
	 * The origin of this transaction.
	 * 
	 * @var boolean
	 */
	private $outsideCapital;

	/**
	 * The transaction partner of this transaction.
	 * 
	 * @var string
	 */
	private $transactionPartner;

	/**
	 * The category of this transaction.
	 * 
	 * @var object Category
	 */
	private $category;

	/**
	 * The begin date of this transaction.
	 * 
	 * @var object Date
	 */
	private $beginDate;

	/**
	 * The end date of this transaction.
	 * 
	 * @var object Date
	 */
	private $endDate;

	/**
	 * The repeat unit of this transaction.
	 * 
	 * day, week, month or year
	 * 
	 * @var string
	 */
	private $repeatUnit;

	/**
	 * The repeat frequency of this transaction.
	 * 
	 * @var integer
	 */
	private $repeatFrequency;
	
	/**
	 * Creates a Planned Transaction.
	 * 
	 * @param $badgerDb object The DB object.
	 * @param $account object The Account object who created this Transaction.
	 * @param $data mixed An associative array with the values out of the DB OR the id of the Transaction.
	 * @param $repeatUnit string The repeat unit of the Transaction.
	 * @param $repeatFrequency string The repeat frequency of the Transaction.
	 * @param $beginDate object The Date object with the begin date of the Transaction.
	 * @param $endDate object The Date object with the end date of the Transaction.
	 * @param $title string The title of the Transaction.
	 * @param $amount object The Amount object with the amount of this Transaction.
	 * @param $description string The description of the Transaction.
	 * @param $transactionPartner string The transaction partner of the Transaction
	 * @param $outsideCapital boolean The origin of the Transaction.
	 */
    function __construct(
    	&$badgerDb,
    	&$account,
    	$data,
    	$repeatUnit = null,
    	$repeatFrequency = null,
    	$beginDate = null,
    	$endDate = null,
    	$title = null,
    	$amount = null,
    	$description = null,
    	$transactionPartner = null,
    	$category = null,
    	$outsideCapital = null
    ) {
    	$CategoryManager = new CategoryManager($badgerDb);
    	
    	$this->badgerDb = $badgerDb;
    	$this->account = $account;
    	
    	if (is_array($data)) {
			$this->id = $data['planned_transaction_id'];
    		$this->title = $data['title'];
    		$this->description = $data['description'];
    		$this->amount = new Amount($data['amount']);
    		$this->outsideCapital = $data['outside_capital'];
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
    
	/**
	 * Returns the id.
	 * 
	 * @return integer The id of this transaction.
	 */
    public function getId() {
    	return $this->id;
    }
    
	/**
	 * Returns the title.
	 * 
	 * @return string The title of this transaction.
	 */
    public function getTitle() {
    	return $this->title;
    }
    
 	/**
 	 * Sets the title.
 	 * 
 	 * @param $title string The title of this transaction.
 	 */
 	public function setTitle($title) {
		$this->title = $title;
		
		$sql = "UPDATE planned_transaction
			SET title = '" . $this->badgerDb->escapeSimple($title) . "'
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			//echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
	/**
	 * Returns the description.
	 * 
	 * @return string The description of this transaction.
	 */
    public function getDescription() {
    	return $this->description;
    }
    
 	/**
 	 * Sets the description.
 	 * 
 	 * @param $description string The description of this transaction.
 	 */
 	public function setDescription($description) {
		$this->description = $description;
		
		$sql = "UPDATE planned_transaction
			SET description = '" . $this->badgerDb->escapeSimple($description) . "'
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			//echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
	/**
	 * Returns the begin date.
	 * 
	 * @return object The Date object with the begin date of this transaction.
	 */
    public function getBeginDate() {
    	return $this->beginDate;
    }
    
 	/**
 	 * Sets the begin date.
 	 * 
 	 * @param $beginDate object The Date object with the begin date of this transaction.
 	 */
 	public function setBeginDate($beginDate) {
		$this->beginDate = $beginDate;
		
		$sql = "UPDATE planned_transaction
			SET begin_date = '" . $beginDate->getDate() . "'
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			//echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
	/**
	 * Returns the end date.
	 * 
	 * @return object The Date object with the end date of this transaction.
	 */
    public function getEndDate() {
    	return $this->endDate;
    }
    
 	/**
 	 * Sets the end date.
 	 * 
 	 * @param $endDate object The Date object with the end date of this transaction.
 	 */
 	public function setEndDate($endDate) {
		$this->endDate = $endDate;
		
		$sql = "UPDATE planned_transaction
			SET end_date = '" . $endDate->getDate() . "'
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			//echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
	/**
	 * Returns the amount.
	 * 
	 * @return object The Amount object with the amount of this transaction.
	 */
    public function getAmount() {
    	return $this->amount;
    }
    
 	/**
 	 * Sets the amount.
 	 * 
 	 * @param $amount object The Amount object with the amount of this transaction.
 	 */
 	public function setAmount($amount) {
		$this->amount = $amount;
		
		$sql = "UPDATE planned_transaction
			SET amount = '" . $amount->get() . "'
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			//echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
	/**
	 * Returns the origin.
	 * 
	 * @return boolean true if this transaction is outside capital.
	 */
    public function getOutsideCapital() {
    	return $this->outsideCapital;
    }
    
 	/**
 	 * Sets the origin.
 	 * 
 	 * @param $outsideCapital boolean true if this transaction is outside capital.
 	 */
 	public function setOutsideCapital($outsideCapital) {
		$this->outsideCapital = $outsideCapital;
		
		$sql = "UPDATE planned_transaction
			SET outside_capital = " . $this->badgerDb->quoteSmart($outsideCapital) . "
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			//echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
	/**
	 * Returns the transaction partner.
	 * 
	 * @return string The transaction partner of this transaction.
	 */
    public function getTransactionPartner() {
    	return $this->transactionPartner;
    }
    
 	/**
 	 * Sets the transaction partner.
 	 * 
 	 * @param $transactionPartner string The transaction partner of this transaction.
 	 */
 	public function setTransactionPartner($transactionPartner) {
		$this->transactionPartner = $transactionPartner;
		
		$sql = "UPDATE planned_transaction
			SET transaction_partner = '" . $this->badgerDb->escapeSimple($transactionPartner) . "'
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			//echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
	
	/**
	 * Returns the category.
	 * 
	 * @return object The Category object with the category of this transaction.
	 */
    public function getCategory() {
    	return $this->category;
    }
 
 	/**
 	 * Sets the Category.
 	 * 
 	 * @param $category object The Category object with the category of this transaction.
 	 */
 	public function setCategory($category) {
		$this->category = $category;
		
		if (is_null($category)) {
			$catId = 'NULL';
		} else {
			$catId = $category->getId();
		}
		
		$sql = "UPDATE finished_transaction
			SET category_id = $catId
			WHERE finished_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			//echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}

	/**
	 * Returns the repeat unit.
	 * 
	 * @return string The repeat unit of this transaction.
	 */
    public function getRepeatUnit() {
    	return $this->repeatUnit;
    }
 
 	/**
 	 * Sets the repeat unit.
 	 * 
 	 * @param $repeatUnit string The repeat unit of this transaction.
 	 */
 	public function setRepeatUnit($repeatUnit) {
		$this->repeatUnit = $repeatUnit;
		
		$sql = "UPDATE planned_transaction
			SET repeat_unit = '" . $repeatUnit . "'
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			//echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}

	/**
	 * Returns the repeat frequency.
	 * 
	 * @return integer The repeat frequency of this transaction.
	 */
    public function getRepeatFrequency() {
    	return $this->repeatFrequency;
    }
 
 	/**
 	 * Sets the repeat frequency.
 	 * 
 	 * @param $repeatFrequency int The repeat frequency of this transaction.
 	 */
 	public function setRepeatFrequency($repeatFrequency) {
		$this->repeatFrequency = $repeatFrequency;
		
		$sql = "UPDATE planned_transaction
			SET repeat_frequency = " . $repeatFrequency . "
			WHERE planned_transaction_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			//echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
		}
	}
}
?>