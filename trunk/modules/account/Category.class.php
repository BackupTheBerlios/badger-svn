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

class Category {
	private $badgerDb;
	private $categoryManager;
	
	private $id;
	private $title;
	private $description;
	private $outsideCapital;
	private $parent;
	private $children;
	
	public function __construct(&$badgerDb, $categoryManager, $data, $title = null, $description = null, $outsideCapital = null, $parent = null) {
		$this->badgerDb = $badgerDb;
		$this->categoryManager = $categoryManager;
		
    	if (is_array($data)) {
			$this->id = $data['category_id'];
			$this->title = $data['title'];
			$this->description = $data['description'];
			$this->outsideCapital = $data['outside_capital'];
			if ($data['parent_id']) {
				$this->parent = $categoryManager->getCategoryById($data['parent_id']);
			}
    	} else {
    		$this->id = $data;
    		$this->title = $title;
    		$this->description = $description;
    		$this->outsideCapital = $outsideCapital;
    		$this->parent = $parent;
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
		
		$sql = "UPDATE category
			SET title = '" . $this->badgerDb->escapeSimple($title) . "'
			WHERE category_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('Category', 'SQLError', $dbResult->getMessage());
		}
	}

	public function getDescription() {
		return $this->description;
	}
	
 	public function setDescription($description) {
		$this->description = $description;
		
		$sql = "UPDATE category
			SET description = '" . $this->badgerDb->escapeSimple($description) . "'
			WHERE category_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('Category', 'SQLError', $dbResult->getMessage());
		}
	}

	public function getOutsideCapital() {
		return $this->outsideCapital;
	}
	
 	public function setOutsideCapital($outsideCapital) {
		$this->outsideCapital = $outsideCapital;
		
		$sql = "UPDATE category
			SET outside_capital = " . $this->badgerDb->quoteSmart($outsideCapital) . "
			WHERE category_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('Category', 'SQLError', $dbResult->getMessage());
		}
	}

	public function getParent() {
		if (
			!($this->parent instanceof Category)
			&& $this->parent
		) {
			$this->parent = $this->categoryManager->getCategoryById($this->parent);
		}
		
		return $this->parent;
	}
	
 	public function setParent($parent) {
		$this->parent = $parent;
		$parent->addChild($this);
		
		$sql = "UPDATE category
			SET parent = " . $parent->getId() . "
			WHERE category_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('Category', 'SQLError', $dbResult->getMessage());
		}
	}

	public function getChildren() {
		if (!is_array($this->children)) {
			$sql = "SELECT category_id
				FROM category
				WHERE parent_id = " . $this->id;
	
			$dbResult =& $this->badgerDb->query($sql);
			
			if (PEAR::isError($dbResult)) {
				echo "SQL Error: " . $dbResult->getMessage();
				throw new BadgerException('Category', 'SQLError', $dbResult->getMessage());
			}
			
			$row = false;
			
			while($dbResult->fetchInto($row, DB_FETCHMODE_ASSOC)) {
				$children[$row['category_id']] = $this->categoryManager->getCategoryByid($row['category_id']);
			}
		}

		return $this->children;
	}
	
	private function addChild($child) {
		$this->children[$child->getId()] = $child;
	}
}
?>