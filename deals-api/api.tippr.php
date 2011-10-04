<?php
// an example of a 3rd party API implementation, namely tippr
require_once('api.php');
class tippr extends api {
	public function __construct() {
		parent::__construct();
	}
	
	public function getNewOffers() {
		$ret = array();
		$uniqueFieldsArr = array();
		$varConversion = array(
			'deal_id'          => 'id',
			'title'            => 'tagline',
			'subtitle'         => 'headline',
			'description'      => 'description',
			//'short_desc'       => '',
			'image'            => 'large_image_url',
			'worth'            => 'current_value',
			'price'            => 'price',
			'start'            => 'start_date',
			'expiration'       => 'end_date',
			//'link'             => 'url'
		);
		
		// getting cities
		require_once('/fakedir/db_new.php');
		$dbh = new db_new('new', array('server' => '**.**.**.**', 'user' => 'fake_user', 'pwd' => 'fake_pass', 'db' => 'fake_db'));
		$cityQuery = $dbh->sqlQuery("SELECT id, name FROM city;", true, true);
		$cities = array();
		foreach ($cityQuery as $row)
			$cities[$row['id']] = $row['name'];
		
		// initial xml call
		$call = $this->curlIt('http://tippr.com/api/v2/channels/?apikey=fakeapikey&format=xml&publisher=tippr');
		$xml = simplexml_load_string($call);
		
		// getting channel /city info
		$channels = array();
		foreach ($xml->resource as $resource)
			$channels[strval($resource->channel)] = strval($resource->name);
		
		
		// getting deals for every channel
		foreach ($channels as $channel => $city) {
			$call = $this->curlIt('http://tippr.com/api/v2/offers/?apikey=fakeapikey&format=xml&publisher=tippr&channel='. $channel);
			$areaXml = simplexml_load_string($call);
			
			foreach ($areaXml->resource as $areaResource) {
				$index = count($ret);
				$ret[$index]['deal_provider_id'] = 5;
			
				foreach ($areaResource->children() as $fieldName => $field) {
					if (in_array($fieldName, $varConversion))
						$ret[$index][array_search($fieldName, $varConversion)] = mysql_real_escape_string(strval($field));
					else
						$uniqueFieldsArr[$fieldName] = strval($field);
				}
				
				$ret[$index]['link'] = 'http://jump.tippr.com/aff_c?offer_id=fakeofferid&aff_id=fakeaffiliateid&params=%2526offer%253D' . rawurlencode(strval($areaResource->slug));
				$ret[$index]['unique_var_str'] = mysql_real_escape_string(json_encode($uniqueFieldsArr));
				$ret[$index]['start'] = str_replace('.0', '', $ret[$index]['start']);
				$ret[$index]['expiration'] .= str_replace('.0', '', $ret[$index]['expiration']);
				
				foreach ($areaResource->channels->resource as $channelResource)
					if (in_array(strval($channelResource->name), $cities)) {
						$foundCityIds = array_keys($cities, strval($channelResource->name));
						foreach ($foundCityIds as $cityId)
							$this->dealCities[$ret[$index]['deal_id']][] = $cityId;
					}
			}
		}
		
		return $ret;
	}
	
	public function fireTrackingPixel() {
		// taken care of by the link field
	}
}
?>