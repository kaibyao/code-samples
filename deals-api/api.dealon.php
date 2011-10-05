<?php
require_once('api.php');
class dealon extends api {
	public function __construct() {
		parent::__construct();
	}
	
	public function getNewOffers() {
		$ret = array();
		$uniqueFieldsArr = array();
		$varConversion = array(
			'deal_id'          => 'deal_id',
			'title'            => 'title',
			//'subtitle'         => '',
			'description'      => 'deal_additional_info',
			//'short_desc'       => '',
			'image'            => 'deal_image',
			'worth'            => 'deal_value',
			'price'            => 'deal_price',
			'start'            => 'deal_start_date',
			'expiration'       => 'deal_expiration',
			'link'             => 'landing_page_url'
		);
		
		// initial xml call
		$call = $this->curlIt('https://www.dealon.com/rssbrand?api_key=fake_key');
		$xml = simplexml_load_string($call);
		
		foreach ($xml->channel->item as $offer) {
			$index = count($ret);
			
			$ret[$index]['deal_provider_id'] = 10;
			
			foreach ($offer->children() as $fieldName => $field) {
				if (in_array($fieldName, $varConversion))
					$ret[$index][array_search($fieldName, $varConversion)] = mysql_real_escape_string(strval($field));
				else
					$uniqueFieldsArr[$fieldName] = strval($field);
			}
			
			$ret[$index]['unique_var_str'] = mysql_real_escape_string(json_encode($uniqueFieldsArr));
			$ret[$index]['start'] .= ' 00:00:00';
			$ret[$index]['expiration'] .= ' 23:59:59';
		}
		
		return $ret;
	}
	
	public function fireTrackingPixel() {
		// taken care of by the link field
	}
}
?>