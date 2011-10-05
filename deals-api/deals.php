<?php
// deals class where we would pull new daily leads from various 3rd party APIs into our database for one of our sites.
// called with $deals->getNewOffers();
class deals {
	public $apiList;
	public $dealCities;
	
	public function __construct() {
		$this->apiList = array(
			'tippr',
			'buyWithMe',
			'kgbDeals',
			'cityWide365',
			'craveASave',
			'dealon',
			'dealster',
			'eversave',
			'bizdeals'
		);
		
		$this->dealCities = array(); // deal_provider's deal_id (NOT "primary" key deal id) => city id
	}
	
	// calls different APIs and puts new offers into db
	public function getNewOffers() {
		$dbh = new db_new('new', array('server' => '**.**.***.**', 'user' => 'fake_user', 'pwd' => 'fake_password', 'db' => 'fake_db'));
		
		$query = "SELECT id, deal_provider_id, deal_id, title, active, approved FROM deals;";
		$existingDealsQuery = $dbh->sqlQuery($query, true, true);
		$existingDealsArr = $titles = array();
		$countUpdate = $countInsert = $countIgnore = 0;
		
		foreach ($existingDealsQuery as $row) {
			$existingDealsArr[$row['deal_provider_id']][$row['deal_id']] = array(
				'title'    => $row['title'],
				'approved' => $row['approved'],
				'active'   => $row['active'],
				'id'       => $row['id']
			);
			$existingTitles[$row['title']] = array(
				'id' => $row['id'],
				'approved' => $row['approved'],
				'active'   => $row['active']
			);
		}
		
		foreach ($this->apiList as $api) {
			require_once("api.$api.php");
			$class = new $api();
			$ignoreFields = array('deal_provider_id', 'deal_id');
			
			$newOffersArr = $class->getNewOffers();
			$insertIds = array();
			
			foreach ($newOffersArr as $offer) {
				// edit expiration time to 23:59:59 if no time set
				if (!empty($offer['expiration'])) {
					$tempExpire = explode(' ', date('Y-m-d H:i:s', strtotime($offer['expiration'])));
					$time = ($tempExpire[1] == '00:00:00') ? '23:59:59' : $tempExpire[1];
					$offer['expiration'] = $tempExpire[0] .' '. $time;
				}
				
				if (!(isset($offer['deal_id']) && array_key_exists($offer['deal_id'], $existingDealsArr[$offer['deal_provider_id']])) && !array_key_exists($offer['title'], $existingTitles)) {
					$insertStr = implode(',', array_keys($offer));
					$valuesStr = "'". implode("','", $offer) ."'";
					
					$query = "INSERT INTO deals ($insertStr) VALUES ($valuesStr);";
					$insert = $dbh->sqlQuery($query, false);
					
					if (mysql_error() == '') {
						$countInsert++;
						$insertId = mysql_insert_id($dbh->getConnection());
						$insertIds[$insertId] = $offer['deal_id'];
					} else echo mysql_error() ."\n";
					
					// adding new deal to existingDealsArr
					if (isset($offer['deal_id']))
						$existingDealsArr[$offer['deal_provider_id']][$offer['deal_id']] = 1;
					$existingTitles[$offer['title']][] = 1;
				}
			}
			
			// inserting into deal_city
			if (!empty($insertIds) && !empty($class->dealCities)) {
				foreach ($class->dealCities as $dealId => $cityArr)
					foreach ($cityArr as $cityId) {
						if (in_array($dealId, $insertIds)) {
							$getInsertIdArr = array_keys($insertIds, $dealId);
							$insertId = $getInsertIdArr[0];
							$dbh->sqlQuery("INSERT INTO deal_city (id, city_id) VALUES ($insertId, '". $cityId ."');", false);
						}
					}
			}
		}
		
		echo "$countInsert deals inserted.";
	}
	
	public function firePixel($api) {
		require_once("/fakedir/deals/api.$api.php");
		$class = new $api();
		
		$class->fireTrackingPixel();
	}
}
?>