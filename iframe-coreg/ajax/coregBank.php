<?php
require_once("../include/config.php"); 
require_once($GLOBALS['includePath'] . "db.php");
require_once($GLOBALS['includePath'] . "class.coregFunctions.php");
require_once($GLOBALS['includePath'] . 'class.ccReg.php');

$ccReg = new CCReg();
$site = 231;
$empty = false; // determines whether the "please fill in all fields" error message appears
$invalids = array();
$sixweeksago = date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-42, date('Y')));
$coregFunctions = new CoregFunctions();

$fieldsToCheck = array('firstName', 'lastName', 'email', 'phone11', 'phone12', 'phone13', 'state', 'zip', 'ccdebt', 'paymentstatus');
foreach($_REQUEST as $key => $val) {
	if(in_array($key, $fieldsToCheck)) {
		if ($val == '')
			$empty = true;
	}
	$$key = $val;
}

/*/ for testing purposes
if ($cid == 1) {
	var_dump($_REQUEST);
	exit;
}
//*/

$phone1 = $phone11 . $phone12 . $phone13;
$phone2 = $phone21 . $phone22 . $phone23;

// checking for incompletions / errors
$first_name = $coregFunctions->cleanUp($firstName, FALSE, TRUE);
$last_name = $coregFunctions->cleanUp($lastName, FALSE, TRUE);		
$email_val = $coregFunctions->check_email_address($email);
$phone_val = $coregFunctions->check_phone($phone1);
$zip_val = $coregFunctions->check_length($zip, 5);
if (empty($ip)) $ip = $_SERVER['REMOTE_ADDR'];

if(!$email_val) {
	$errors[] = "Please enter a valid email address.";	
}

if(!$phone_val) {
	$errors[] = "Please enter a valid primary phone number.";	
}

if(!$zip_val) {
	$errors[] = "Please enter a valid zip code.";	
}

if($empty === true)
	exit("Please make sure all fields are filled in.\n\n");
	
if(!empty($errors)) {
	foreach($errors as $error)
		echo $error."\n";
	
	exit;
}

// error checking done, proceeding w/ the inserts
if (empty($phone2))
	$phone2 = '';

$checkduplicate = "select count(email_address) as thecount from bank_leads where email_address = '$email' and date(date_created) >= '$sixweeksago'";
$row_fcheckduplicates = mysql_query($checkduplicate);
$row_fcheckduplicate = mysql_fetch_assoc($row_fcheckduplicates);
$dupcheck = $row_fcheckduplicate['thecount'];

$insertId = $ccReg->pixelJoin(1);
if (empty($midn)) $midn = 231 . $insertId;

if ($dupcheck > 0) {
	$insert = mysql_query("INSERT INTO bank_leads_dup (first_name, last_name, email_address, phone_1, phone_2, zip, date_created, ip) VALUES ('$first_name', '$last_name', '$email', '$phone1', '$phone2', '$zip', '".date("Y-m-d H:i:s")."', '".$ip."')");
	
	echo "DUPLICATE";
	
	$capturetime = date("Y-m-d H:i:s");
} else {
	$insert = mysql_query("INSERT INTO bank_leads (first_name, last_name, email_address, phone_1, phone_2, zip, date_created, ip) VALUES ('$first_name', '$last_name', '$email', '$phone1', '$phone2', '$zip', '".date("Y-m-d H:i:s")."', '".$ip."')");
	
	echo "SUCCESS";
	
	$capturetime = date("Y-m-d H:i:s");
	
	// Total Attorneys Coreg cURL Request
	$cctts_params = "t=1&mid_an=".$midan."&mid_n=".$midn."&coid=bank*1&tsid=".$tsid."&afid=".$site."&ofid=".$ofid."&opid=".$opid."&opid2=".$opid2."&site=$site";
	
	$cctts_curl = curl_init();
	curl_setopt($cctts_curl, CURLOPT_URL, "http://www.cctts.com/collect/");
	curl_setopt($cctts_curl, CURLOPT_POST, TRUE);
	curl_setopt($cctts_curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($cctts_curl, CURLOPT_POSTFIELDS, $cctts_params);
	$cctts_result = curl_exec ($cctts_curl);
	curl_close ($cctts_curl);
	
	// Post To CB
	$params = 'from=chr&lead_source_id=19&ip='.$ip.'&capturetime='.$capturetime.'&firstname='.$first_name.'&lastname='.$last_name.'&zip='.$zip.'&email='.$email.'&primaryphone='.$phone1.'&secondaryphone='.$phone2.'&state='.$state.'&site='. $site .'&tsid='.$tsid.'&mid_an='.$midan.'&mid_n='.$midn.'&ofid='.$ofid.'&opid='.$opid.'&opid2='.$opid2.'&chreg_id='.$cid.'&url_referer='.$refererUrl;	

	// create a new cURL resource
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://www.consideringbankruptcy.net/DB.php");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	$result= curl_exec ($ch);
	curl_close ($ch);
}
?>