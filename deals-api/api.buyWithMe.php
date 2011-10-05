<?php
require_once('api.php');
class buyWithMe extends api {
	public function __construct() {
		parent::__construct();
	}
	
	public function getNewOffers() {
		$ret = array();
		$uniqueFieldsArr = array();
		$varConversion = array(
			'deal_id'          => 'sku',
			'title'            => 'name',
			//'subtitle'         => '',
			'description'      => 'description',
			//'short_desc'       => '',
			'image'            => 'image-url',
			'worth'            => 'retail-price',
			'price'            => 'price',
			'start'            => date('Y-m-d') .' 00:00:00',
			//'expiration'       => '',
			'link'             => 'buy-url'
		);
		
		$pageNumber = 1;
		$totalMatched = 0;
		
		// initial xml call
		$call = $this->curlIt('https://product-search.api.cj.com/v2/product-search?website-id=fake_key&advertiser-ids=fake_key&page-number='. $pageNumber .'&records-per-page=100', '', array('authorization: fake_key/fake_key'));
		$xml = simplexml_load_string($call);
		
		foreach ($xml->products->product as $offer) {
			$index = count($ret);
			
			$ret[$index]['deal_provider_id'] = 9;
			
			foreach ($offer->children() as $fieldName => $field) {
				if (in_array($fieldName, $varConversion))
					$ret[$index][array_search($fieldName, $varConversion)] = mysql_real_escape_string(strval($field));
				else
					$uniqueFieldsArr[$fieldName] = strval($field);
			}
			
			$ret[$index]['unique_var_str'] = mysql_real_escape_string(json_encode($uniqueFieldsArr));
			$ret[$index]['expiration'] = '2250-12-31 23:59:59';
		}
		
		// start looping through the other pages
		if ($totalMatched == 0)
			$totalMatched = $xml->products['total-matched'];
		
		for ($i = $pageNumber; $i <= ceil($totalMatched/100); $i++) {
			$call = $this->curlIt('https://product-search.api.cj.com/v2/product-search?website-id=fake_key&advertiser-ids=fake_key&page-number='. $i .'&records-per-page=100', '', array('authorization: fake_key/fake_key'));
			$xml = simplexml_load_string($call);
			
			foreach ($xml->products->product as $offer) { // a copy and paste of the above code
				$index = count($ret);
				
				$ret[$index]['deal_provider_id'] = 9;
				
				foreach ($offer->children() as $fieldName => $field) {
					if (in_array($fieldName, $varConversion))
						$ret[$index][array_search($fieldName, $varConversion)] = mysql_real_escape_string(strval($field));
					else
						$uniqueFieldsArr[$fieldName] = strval($field);
				}
				
				$ret[$index]['unique_var_str'] = mysql_real_escape_string(json_encode($uniqueFieldsArr));
				$ret[$index]['expiration'] = '2250-12-31 23:59:59';
			}
		}
		
		return $ret;
	}
	
	public function fireTrackingPixel() {
		// taken care of by the link field
	}
}
?>