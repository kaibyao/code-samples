<?php
require_once("../include/config.php"); 
require_once($GLOBALS['includePath'] . "db.php");
require_once($GLOBALS['includePath'] . "class.coregFunctions.php");
require_once($GLOBALS['commonRoot'] . "class.LeadRouter.php");
require_once($GLOBALS['includePath'] . 'class.ccReg.php');

$ccReg = new CCReg();
$empty = false; // determines whether the "please fill in all fields" error message appears
$invalids = array();
$sixweeksago = date("Y-m-d", mktime(0, 0, 0, date('m'), date('d')-42, date('Y')));
$coregFunctions = new CoregFunctions();

$fieldsToCheck = array('firstName', 'lastName', 'email', 'phone11', 'phone12', 'phone13', 'address', 'city', 'state', 'zip', 'fieldOfInterest', 'hsOrGED', 'citizen');
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

if(!$email_val) {
	$errors[] = "Please enter a valid email address.";	
}

if(!$phone_val) {
	$errors[] = "Please enter a valid primary phone number";	
}

if(empty($citizen)) {
	$errors[] = "Please check if you are a citizen";	
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
	unset($phone2);

$post_phone = substr($phone1, 0, 3) .'-'. substr($phone1, 3, 3) .'-'. substr($phone1, 6, 4);
$checkduplicate = "select count(email_address) thecount from new_edu where email_address = '$email' and date(date_created) >= '$sixweeksago'";
$fcheckduplicate = mysql_query($checkduplicate);
$row_fcheckduplicate = mysql_fetch_assoc($fcheckduplicate);
$dupcheck = $row_fcheckduplicate['thecount'];

$insertId = $ccReg->pixelJoin(3);
if (empty($midn)) $midn = 231 . $insertId;

if ($dupcheck > 0) {
	echo "DUPLICATE";
} else {
	if (empty($ip)) $ip = $_SERVER['REMOTE_ADDR'];
	$insert = mysql_query("INSERT INTO new_edu (first_name , last_name , email_address , phone1, phone2, date_created, ip, address, city, state, zip, field_of_interest, citizen) VALUES ('$first_name', '$last_name', '$email', '$phone1', '$phone2', '".date("Y-m-d H:i:s")."', '".$ip."', '$address', '$city', '$state', '$zip', '$fieldOfInterest', '$citizen')");
	$newid = mysql_insert_id();		
	
	$capturetime = date("Y-m-d H:i:s");
	
	$idquery = "select id from new_edu ORDER BY id DESC LIMIT 0,1";
	$fidquery = mysql_query($idquery);
	$row = mysql_fetch_assoc($fidquery);
	$id = $row['id'];								
	
	//leadrouting mechanism***************************
	$leadRouter = new LeadRouter();

	$insert = array(); 
	$insert['firstname'] 		= $first_name; 
	$insert['lastname']        = $last_name; 
	$insert['email']			   = $email; 
	$insert['address']         = $address; 
	$insert['state']		      = $state; 
	$insert['city']            = $city; 
	$insert['zip']             = $zip;
	$insert['phone1']          = $phone1; 
	$insert['phone1wdash']     = $post_phone; 
	$insert['phone2']          = $phone2;  
	$insert['phonearea']       = $phone11; 
	$insert['phoneprefix']     = $phone12; 
	$insert['phoneline']       = $phone13; 
	$insert['ip']              = $ip;
	$insert['fieldofinterest'] = $fieldOfInterest;
	$insert['hashsorged']      = $hsOrGED;
	//$insert['educationlevel']  = $education_level;
	//$insert['gradyear']        = $gradyr; 
	//$insert['startdate']       = $start_date; 
	//$insert['birthday']        = $dob; 
	//$insert['over18']          = $over_18; 
	$insert['citizen'] 			= $citizen;
	$insert['mid_n']           = $midn;
	$insert['mid_an']          = $midan;
	$insert['tsid'] 		      = $tsid;
	$insert['afid']            = $site;
	$insert['ofid']            = $ofid;
	$insert['opid']            = $opid;
	$insert['opid2']           = $opid2;
	$insert['lead_source_id']  = 19;
	$insert['chreg_id']        = $cid;
	$insert['url_referer']     = rawurldecode($refererUrl);
	$response = $leadRouter->insert($insert, 'edu', $site); 
	
	//
	//************************************************************
	// InParallel Coreg cURL Request
	$cctts_params = "t=1&mid_an=".$midan."&mid_n=".$midn."&coid=edu*22&tsid=".$tsid."&afid=".$site."&ofid=".$ofid."&opid=".mysql_escape_string($opid)."&opid2=".mysql_escape_string($opid2)."&site=$site";
	
	$cctts_curl = curl_init();
	curl_setopt($cctts_curl, CURLOPT_URL, "http://www.cctts.com/collect/");
	curl_setopt($cctts_curl, CURLOPT_POST, TRUE);
	curl_setopt($cctts_curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($cctts_curl, CURLOPT_POSTFIELDS, $cctts_params);
	$cctts_result = curl_exec ($cctts_curl);
	curl_close ($cctts_curl);
	
	echo "SUCCESS";
}
?>