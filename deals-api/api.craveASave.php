<?php
require_once('api.php');
class craveASave extends api {
	public function __construct() {
		parent::__construct();
	}
	
	public function getNewOffers() {
		$ret = array();
		$uniqueFieldsArr = array();
		$varConversion = array(
			'deal_id'          => 'dealID',
			'title'            => 'title',
			'subtitle'         => '',
			'description'      => 'description',
			'short_desc'       => 'highlights',
			'image'            => 'deal_image',
			'worth'            => 'regPrice',
			'price'            => 'salePrice',
			'start'            => date('Y-m-d') .' 00:00:00',
			'expiration'      => 'expire_time',
			'link'             => 'guid'
		);
		
		$call = $this->curlIt('http://www.dealcurrent.com/feeds/index.php?affiliateID=fake_key');
		$xml = simplexml_load_string($call);
		
		foreach ($xml->channel->item as $offer) {
			$index = count($ret);
			
			$ret[$index]['deal_provider_id'] = 7;
			
			foreach ($offer->children() as $fieldName => $field) {
				if (in_array($fieldName, $varConversion))
					$ret[$index][array_search($fieldName, $varConversion)] = mysql_real_escape_string(strval($field));
				else
					$uniqueFieldsArr[$fieldName] = strval($field);
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