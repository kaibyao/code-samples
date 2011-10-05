<?php
require_once('api.php');
class bizdeals extends api {
	public function __construct() {
		parent::__construct();
	}
	
	public function getNewOffers() {
		$ret = array();
		$uniqueFieldsArr = array();
		$varConversion = array(
			'deal_id'          => 'dealid',
			'title'            => 'title',
			//'subtitle'         => '',
			'description'      => 'description',
			//'short_desc'       => '',
			'image'            => 'bigimage',
			//'worth'            => 'deal_value',
			//'price'            => 'deal_price',
			'start'            => 'startdate',
			'expiration'       => 'enddate',
			'link'             => 'trackingurl'
		);
		
		// initial xml call
		$call = $this->curlIt('https://shareasale.com/x.cfm?action=couponDeals&affiliateId=fake_key&token=fake_key&current=1&version=1.3&XMLFormat=1');
		$xml = simplexml_load_string($call);
		
		foreach ($xml->dealcouponlistreportrecord as $offer) {
			$index = count($ret);
			
			$ret[$index]['deal_provider_id'] = 11;
			
			foreach ($offer->children() as $fieldName => $field) {
				if (in_array($fieldName, $varConversion))
					$ret[$index][array_search($fieldName, $varConversion)] = mysql_real_escape_string(strval($field));
				else
					$uniqueFieldsArr[$fieldName] = strval($field);
			}
			
			$ret[$index]['unique_var_str'] = mysql_real_escape_string(json_encode($uniqueFieldsArr));
			$ret[$index]['start'] = str_replace('.0', '', $ret[$index]['start']);
			$ret[$index]['expiration'] .= str_replace('.0', '', $ret[$index]['expiration']);
		}
		
		return $ret;
	}
	
	public function fireTrackingPixel() {
		// taken care of by the link field
	}
}
?>