<?php
require_once('api.php');
class dealster extends api {
	public function __construct() {
		parent::__construct();
	}
	
	public function getNewOffers() {
		$ret = array();
		$uniqueFieldsArr = array();
		$varConversion = array(
			//'deal_id'          => 'deal_id',
			'title'            => 'title',
			//'subtitle'         => '',
			'description'      => 'fullDescription',
			'short_desc'       => 'shortDescription',
			'image'            => 'image',
			'worth'            => 'value',
			'price'            => 'price',
			'start'            => 'pubDate',
			'expiration'       => 'expires',
			'link'             => 'detailsUrl'
		);
		
		// initial xml call
		$call = $this->curlIt('http://www.dealster.com/live-feed/fake_key');
		$xml = simplexml_load_string($call);
		
		//header('Content-Type: text/xml');
		//echo $xml->asXML(); exit;
		
		foreach ($xml->channel->deal as $offer) {
			if (isset($offer->deal)) $offer = $offer->deal;
			$index = count($ret);
			
			$ret[$index]['deal_provider_id'] = 13;
			
			foreach ($offer->children() as $fieldName => $field) {
				if (in_array($fieldName, $varConversion))
					$ret[$index][array_search($fieldName, $varConversion)] = mysql_real_escape_string(strval($field));
				else
					$uniqueFieldsArr[$fieldName] = strval($field);
			}
			
			$ret[$index]['unique_var_str'] = mysql_real_escape_string(json_encode($uniqueFieldsArr));
			
			$search = array('T', '-05:00');
			$replace = array(' ', '');
			
			$ret[$index]['start'] = str_replace($search, $replace, $ret[$index]['start']);
			$ret[$index]['expiration'] = str_replace($search, $replace, $ret[$index]['expiration']);
		}
		//var_dump($ret); exit;
		return $ret;
	}
	
	public function fireTrackingPixel() {
		// taken care of by the link field
	}
}
?>