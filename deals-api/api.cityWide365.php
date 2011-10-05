<?php
require_once('api.php');
class cityWide365 extends api {
	public function __construct() {
		parent::__construct();
	}
	
	public function getNewOffers() {
		$ret = array();
		$uniqueFieldsArr = array();
		$varConversion = array(
			'deal_id'          => 'id',
			'title'            => 'name',
			'subtitle'         => null,
			'description'      => 'description',
			'short_desc'       => null,
			'image'            => null,
			'worth'            => null,
			'price'            => null,
			'start'            => date('Y-m-d') .' 00:00:00',
			'expiration'      => 'expiration_date',
			'link'             => 'tracking_url'
		);
		
		$call = $this->curlIt('http://citywide365.hasoffers.com/offers/offers.xml?api_key=fake_key');
		$xml = simplexml_load_string($call);
		
		foreach ($xml->offer as $offer) {
			$index = count($ret);
			
			$ret[$index]['deal_provider_id'] = 6;
			
			foreach ($offer->children() as $fieldName => $field) {
				if (in_array($fieldName, $varConversion))
					$ret[$index][array_search($fieldName, $varConversion)] = mysql_real_escape_string(strval($field));
				else
					$uniqueFieldsArr[$fieldName] = (strval($field));
			}
			
			$ret[$index]['unique_var_str'] = mysql_real_escape_string(json_encode($uniqueFieldsArr));
		}
		
		return $ret;
	}
	
	public function fireTrackingPixel() {
		// taken care of by the link field
	}
}
?>