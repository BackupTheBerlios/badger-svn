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
?>