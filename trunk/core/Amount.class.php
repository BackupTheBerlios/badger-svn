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

class Amount {
	
	private $amount;

	public function Amount($amount) {
		bcscale(2);
		
		$this->set($amount);
	}
	
	public function get() {
		return $this->amount;
	}
	
	function getFormatted() {
		$decPoint = getBadgerTranslation2('formats', 'decimalPoint');
		$thousandsSep = getBadgerTranslation2('formats', 'thousandsSeparator');
		
		$str = $this->amount;
		
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

	public function set($amount) {
		$this->amount = $amount;
	}
	
	public function add($summand) {
		if ($summand instanceof Amount) {
			$this->amount = bcadd($this->amount, $summand->get());
		} else {
			$this->amount = bcadd($this->amount, $summand);
		}
	}

	public function sub($subtrahend) {
		if ($subtrahend instanceof Amount) {
			$this->amount = bcsub($this->amount, $subtrahend->get());
		} else {
			$this->amount = bcsub($this->amount, $subtrahend);
		}
	}

	public function mul($factor) {
		if ($factor instanceof Amount) {
			$this->amount = bcmul($this->amount, $factor->get());
		} else {
			$this->amount = bcmul($this->amount, $factor);
		}
	}

	public function div($divisor) {
		if ($divisor instanceof Amount) {
			$this->amount = bcdiv($this->amount, $divisor->get());
		} else {
			$this->amount = bcdiv($this->amount, $divisor);
		}
	}
}
?>