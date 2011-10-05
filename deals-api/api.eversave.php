<?php
require_once('api.php');
class eversave extends api {
	public function __construct() {
		parent::__construct();
	}
	
	public function getNewOffers() {
		$ret = array();
		$uniqueFieldsArr = array();
		$varConversion = array(
			//'deal_id'          => 'deal_id',
			'title'            => 'title',
			'subtitle'         => 'highlight[0]',
			'description'      => 'description',
			'short_desc'       => 'shortDescription',
			'image'            => 'saveImage[0]',
			'worth'            => 'offerValue',
			'price'            => 'purchasePrice',
			'start'            => 'date',
			'expiration'       => 'endDate',
			'link'             => 'link'
		);
		
		// initial xml call
		$call = $this->curlIt('http://www.eversave.com/rss/all?sid=fake_key');
		$xml = simplexml_load_string($call);
		
		foreach ($xml->channel->item as $offer) {
			$index = count($ret);
			$ret[$index]['deal_provider_id'] = 8;
			
			$namespaces = $offer->getNameSpaces(true);
			$dc = $offer->children($namespaces['dc']);
			$essave = $offer->children($namespaces['essave']);
			
			foreach ($offer->children() as $fieldName => $field) {
				if (in_array($fieldName, $varConversion))
					$ret[$index][array_search($fieldName, $varConversion)] = mysql_real_escape_string(strval($field));
				else
					$uniqueFieldsArr[$fieldName] = strval($field);
			}
			
			foreach ($dc as $fieldName => $field) {
				if (in_array($fieldName, $varConversion))
					$ret[$index][array_search($fieldName, $varConversion)] = mysql_real_escape_string(strval($field));
				else
					$uniqueFieldsArr[$fieldName] = strval($field);
			}
			
			foreach ($essave as $fieldName => $field) {
				if (in_array($fieldName, $varConversion))
					$ret[$index][array_search($fieldName, $varConversion)] = mysql_real_escape_string(strval($field));
				else
					$uniqueFieldsArr[$fieldName] = strval($field);
			}
			
			$ret[$index]['unique_var_str'] = mysql_real_escape_string(json_encode($uniqueFieldsArr));
			$ret[$index]['start'] = str_replace(array('T', 'Z'), array(' ', ''), $ret[$index]['start']);
			$ret[$index]['expiration'] = str_replace(array('T', '-0400'), array(' ', ''), $ret[$index]['expiration']);
		}
		
		return $ret;
	}
	
	public function fireTrackingPixel() {
		// taken care of by the link field
	}
}
?>