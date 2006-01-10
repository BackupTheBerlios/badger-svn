<?php

class UserSettings {
	private $properties;
	private $badgerDb;
	
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
    
    public function getProperty($key) {
    	if (isset($this->properties[$key])) {
    		return $this->properties[$key];
    	} else {
    		throw new BadgerException('UserSettings.illegalKey', $key);
    	}
    }
    
    public function setProperty($key, $value) {
       	if (isset($this->properties[$key])) {
    		$sql = 'UPDATE user_settings
				SET prop_value = \'' . addslashes(serialize($value)) . '\'
				WHERE prop_key = \'' . addslashes($this->properties[$key]) . '\'';
    		
    		$this->badgerDb->query($sql);
       	} else {
       		$sql = 'INSERT INTO user_settings (prop_key, prop_value)
				VALUES (\'' . addslashes($this->properties[$key]) . '\',
				\'' . addslashes(serialize($value)) . '\)';
				
    		$this->badgerDb->query($sql);
       	}

       	$this->properties[$key]['val'] = $value;
    }
    
      public function delProperty($key) {
    	if (isset($this->properties[$key])) {
    		$sql = 'DELETE FROM user_settings
				WHERE prop_key = \'' . addslashes($this->properties[$key]) . '\'';
				
    		
    		$this->badgerDb->query($sql);
			  		
    		unset ($this->properties[$key]);
    	} else {
    		throw new BadgerException('UserSettings.illegalKey', $key);
    	}
    }
}
?>