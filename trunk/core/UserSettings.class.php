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
 * Handle storage of user settings.
 * 
 * @author Eni Kao, Paraphil
 * @version $LastChangedRevision$ 
 */
class UserSettings {
	/**
	 * list of all properties
	 * 
	 * @var array
	 */
	private $properties;
	
	/**
	 * database Object
	 * 
	 * @var object
	 */
	private $badgerDb;
	
	/**
	 * reads out all properties from Database
	 * 
	 * @param object $badgerDb the database object
	 */
    public function UserSettings($badgerDb) {
    	$this->badgerDb = $badgerDb;
    	
    	$sql = 'SELECT prop_key, prop_value
			FROM user_settings';
		
		$res =& $badgerDb->query($sql);

		$this->properties = array();
		
		$row = array();
		
		while ($res->fetchInto($row, DB_FETCHMODE_ASSOC)) {
			$this->properties[$row['prop_key']] = unserialize($row['prop_value']);
		}
    }
    
    /**
     * reads out the property defined by $key
     * 
     * @param string $key key of the requested value
     * @throws BadgerException if unknown key is passed
     * @return mixed the value referenced by $key
     */
    public function getProperty($key) {
    	if (isset($this->properties[$key])) {
    		return $this->properties[$key];
    	} else {
    		throw new BadgerException('UserSettings', 'illegalKey', $key);
    	}
    }
    
    /**
     * sets property $key to $value
     * 
     * @param string $key key of the target value
     * @param mixed value the value referneced by $key can be every serializable php data
     * @return void
     */
    public function setProperty($key, $value) {
       	if (isset($this->properties[$key])) {
    		$sql = 'UPDATE user_settings
				SET prop_value = \'' . addslashes(serialize($value)) . '\'
				WHERE prop_key = \'' . addslashes($key) . '\'';
    		
    		$this->badgerDb->query($sql);
       	} else {
       		$sql = 'INSERT INTO user_settings (prop_key, prop_value)
				VALUES (\'' . addslashes($key) . '\',
				\'' . addslashes(serialize($value)) . '\')';
				
			$this->badgerDb->query($sql);	
    		
       	}

       	$this->properties[$key] = $value;
    }

	/**
	 * deletes property $key
	 * 
	 * @param string $key key of the target value
	 * @throws BadgerException if unknown key is passed
	 * @return void 
	 */
 	public function delProperty($key) {
		if (isset($this->properties[$key])) {
    		$sql = 'DELETE FROM user_settings
				WHERE prop_key = \'' . addslashes($key) . '\'';
				
    		
    		$this->badgerDb->query($sql);
			  		
    		unset ($this->properties[$key]);
    	} else {
    		throw new BadgerException('UserSettings', 'illegalKey', $key);
    	}
    }
}
?>