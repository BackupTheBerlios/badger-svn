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
 * Represents a financial amount (of money). Cares for non-rounding arithmetic.
 * 
 * @author Eni Kao, Mampfred
 * @version $LastChangedRevision$
 */
class Amount {
	
	/**
	 * The amount.
	 * 
	 * @var string
	 */
	private $amount;

	/**
	 * Creates an amount.
	 * 
	 * @param $amount string The amount.
	 */
	public function Amount($amount) {
		bcscale(2);
		
		$this->set($amount);
	}
	
	/**
	 * Returns the amount.
	 * 
	 * @return string The amount with no thousands separator, '.' as decimal separator.
	 */
	public function get() {
		return $this->amount;
	}
	
	/**
	 * Returns the formatted amount with two digits. No rounding but truncating occurs.
	 * 
	 * @return string The amount with thousands separator and decimal separator according to language settings. 
	 */
	function getFormatted() {
		$decPoint = getBadgerTranslation2('formats', 'decimalPoint');
		$thousandsSep = getBadgerTranslation2('formats', 'thousandsSeparator');
		
		$str = $this->amount;
		
		$str = trim($str);
		
		//Sort out negative numbers
		if (substr($str, 0, 1) == '-') {
			$negative = true;
			$firstDigit = 1;
		} else {
			$negative = false;
			$firstDigit = 0;
		}
		
		$decPosition = strpos($str, '.');
	
		//if there is a decimal point
		if ($decPosition != 0) {
			//copy at most two fraction digits
			$start = $decPosition - 1;
			$result = $decPoint . substr($str, $decPosition + 1, 2);
		} else {
			$start = strlen($str);
			$result = $decPoint;
		}
		
		//Pad up to two zeros
		$result .= str_repeat('0', strlen($decPoint) + 2 - strlen($result));
	
		$count = 0;
		
		//Insert thousands separators
		for ($i = $start; $i >= $firstDigit; $i--) {
			if ($count == 3) {
				$result = $thousandsSep . $result;
				
				$count = 0;
			}
	
			$result = substr($str, $i, 1) . $result;
			$count++;	
		}
		
		//Add negative sign
		if ($negative) {
			$result = '-' . $result;
		}
		
		return $result;
	}

	/**
	 * Sets the amount;
	 * 
	 * @param $amount string The new amount.
	 */
	public function set($amount) {
		$this->amount = $amount;
	}
	
	/**
	 * Adds $summand to this amount.
	 * 
	 * @param $summand mixed A number or Amount to add.
	 * @return Amount The new Amount.
	 */
	public function add($summand) {
		if ($summand instanceof Amount) {
			$this->amount = bcadd($this->amount, $summand->get());
		} else {
			$this->amount = bcadd($this->amount, $summand);
		}
		
		return $this;
	}

	/**
	 * Subtracts $subtrahend from this amount.
	 * 
	 * @param $subtrahend mixed A number or Amount to subtract.
	 * @return Amount The new Amount.
	 */
	public function sub($subtrahend) {
		if ($subtrahend instanceof Amount) {
			$this->amount = bcsub($this->amount, $subtrahend->get());
		} else {
			$this->amount = bcsub($this->amount, $subtrahend);
		}
		
		return $this;
	}

	/**
	 * Multiplys this amount by $factor.
	 * 
	 * @param $factor mixed A number or Amount to multiply by.
	 * @return Amount The new Amount.
	 */
	public function mul($factor) {
		if ($factor instanceof Amount) {
			$this->amount = bcmul($this->amount, $factor->get());
		} else {
			$this->amount = bcmul($this->amount, $factor);
		}
		
		return $this;
	}

	/**
	 * Divides this amount by $divisor.
	 * 
	 * @param $divisor mixed A number or Amount to divide by.
	 * @return Amount The new Amount.
	 */
	public function div($divisor) {
		if ($divisor instanceof Amount) {
			$this->amount = bcdiv($this->amount, $divisor->get());
		} else {
			$this->amount = bcdiv($this->amount, $divisor);
		}
		
		return $this;
	}
}
?>