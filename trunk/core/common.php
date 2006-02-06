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
 * Gets the escaped $key value out of $array.
 * 
 * Example use: escaped($_GET, 'query');
 * Considers magic_quotes_gpc.
 * 
 * @param array $array The array of $key, typically a superglobal.
 * @param string $key The key of the requested value. 
 * @return mixed The value of key $key in $array in escaped form.
 */
function escaped($array, $key) {
	if (get_magic_quotes_gpc()) {
		return $array[$key];
	} else {
		return addslashes($array[$key]);
	}
}

/**
 * Gets the unescaped $key value out of $array.
 * 
 * Example use: unescaped($_GET, 'query');
 * Considers magic_quotes_gpc.
 * 
 * @param array $array The array of $key, typically a superglobal.
 * @param string $key The key of the requested value. 
 * @return mixed The value of key $key in $array in unescaped form.
 */
function unescaped($array, $key) {
	if (!get_magic_quotes_gpc()) {
		return $array[$key];
	} else {
		return stripslashes($array[$key]);
	}
}

/**
 * Gets the element of $array following the key $key.
 * 
 * @param $array array The array to traverse.
 * @param $key mixed The key to advance by one.
 * @return mixed The next array element or false if at the end of the array.
 */
function nextByKey(&$array, &$key) {
	if (is_null(key($array))) {
		return false;
	}
	
	if (!is_null($key)) {
		if (key($array) != $key) {
			reset($array);
			$currentKey = key($array);
			while (!is_null($currentKey) && ($currentKey != $key)) {
				next($array);
				$currentKey = key($array);
				
			}
		}

		$result = next($array);
	} else {
		$result = current($array);
	}
	
	$key = key($array);
	
	return $result;
}
?>