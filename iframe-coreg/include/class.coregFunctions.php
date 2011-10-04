<?php
class CoregFunctions {
	public function __construct() {
	
	}
	
	public function cleanUp($string, $html = FALSE, $punctuation = FALSE) {
		$cleanedUp = ltrim($string);
		$cleanedUp = rtrim($cleanedUp);
		
		if(!$html)
			$cleanedUp = strip_tags($cleanedUp);
		
		if(!$punctuation)
			$cleanedUp = preg_replace('/\W/', ' ', $cleanedUp);
		
		$cleanedUp = htmlentities($cleanedUp, ENT_QUOTES);
		
		return $cleanedUp;
	}

	public function check_email_address($email) {
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
			return false;
		
		return true;
	}

	public function check_length($var, $max) {
		if(strlen($var) < $max)
			return false;
		
		if(!is_numeric($var))
			return false;
		
		return true;
	}
	
	// checks for valid phone number, phone must be 10 digits and all numbers
	public function check_phone($phone, $phoneCanBeEmpty = false) {
		if(!$phoneCanBeEmpty && (!preg_match("/^[0-9]{10}$/s", $phone) || $phone == ''))
			return false;
		
		$pieces = array(
			substr($phone, 0, 3),
			substr($phone, 3, 3),
			substr($phone, 6, 4)
		);
		
		if($pieces[0]=='000' || $pieces[0]=='111' || $pieces[0]=='555' || $pieces[0]=='999' || $pieces[0]=='123') {
			return false;	
		}
		
		if($pieces[1]=='000' || $pieces[1]=='111' || $pieces[1]=='555' || $pieces[1]=='999' || $pieces[1]=='123') {
			return false;	
		}
		
		return true;
	}
}
?>