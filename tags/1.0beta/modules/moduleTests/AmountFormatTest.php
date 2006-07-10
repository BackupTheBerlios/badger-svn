<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://www.badger-finance.org 
*
**/

bcscale(2);

test('0.00');
test('1.00');
test('2.00');
test('0.01');
test('2.12');
test('12.04');
test('123.60');
test('123.12');
test('1234.12');
test('0.09');
test('1234567.01');
test('12345.12');
test('123456.12');
test('123456789012345678901234567.12');

test('-0.00');
test('-1.00');
test('-2.00');
test('-0.01');
test('-2.12');
test('-12.04');
test('-123.60');
test('-123.12');
test('-1234.12');
test('-0.09');
test('-1234567.01');
test('-12345.12');
test('-123456.12');
test('-123456789012345678901234567.12');

test(bcadd('-123456789012345678901234567.12', '123456789012345678901234568.12'));
test(bcsub('2.00', '3.23'));
test('12.1');

test('123.1');


function test($str) {
	echo "str: $str; format: " . format ($str, ',', '.') . "<br />";
}

function format($str, $decPoint, $thousandsSep) {
	$str = trim($str);
	
	if (substr($str, 0, 1) == '-') {
		$negative = true;
		$firstDigit = 1;
	} else {
		$negative = false;
		$firstDigit = 0;
	}
	
	$decPosition = strpos($str, '.');

	if ($decPosition != 0) {
		$start = $decPosition - 1;
		$result = $decPoint . substr($str, $decPosition + 1, 2);
	} else {
		$start = strlen($str);
		$result = $decPoint;
	}
	
	$result .= str_repeat('0', strlen($decPoint) + 2 - strlen($result));

	$count = 0;
	
	for ($i = $start; $i >= $firstDigit; $i--) {
		if ($count == 3) {
			$result = $thousandsSep . $result;
			
			$count = 0;
		}

		$result = substr($str, $i, 1) . $result;
		$count++;	
	}
	
	if ($negative) {
		$result = '-' . $result;
	}
	
	return $result;
}
?>