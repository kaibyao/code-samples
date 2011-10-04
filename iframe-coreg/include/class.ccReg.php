<?php
class CCReg {
	private $urlParams; // parameters that get passed in every link array[session parameter name => array[url parameter name, default value]]
	private $ccttsParams; // parameters that get sent to CCTTS for page view / join. array(cctts name => value).
	private $nextUrl; // when user clicks an offer or skip, go to next page URL
	private $pathOrderArr; // an array that contains information on the path order
	private $loadTime; // used for debugging, records the load times of certain processes
	private $active; // if the path is active or not
	private $trafficCap; // array of traffic caps for different coreg types
	private $numJoins; // array of # of joins per offer type (used for traffic caps)
	
	public $position;  // current path position
	public $templateFolder; // template folder of the path page
	public $pageTitle; // <title>
	public $pageFolder; // folder of the page location
	public $pageFile; // file name of the page
	public $userInfo; // user's info (first name, etc)
	public $redirectURL; // redirect URL
	
	// sets variables, initializes session/class variables, then calls loadProcess.
	public function __construct() {
		$this->loadTime = array();
		
		$sessionID = session_id();
		if (isset($_GET['PHPSESSID']) && empty($sessionID)) session_id($_GET['PHPSESSID']);
		session_start();
		
		$this->position = (isset($_GET['pos'])) ? $_GET['pos'] : 1;
		$this->urlParams = array( // $_SESSION field => array($_GET param field name, default value)
			'active'      => array('id' => 'active', 'default' => ''),
			'ccregID'     => array('id' => 'cid', 'default' => 1),
			'tsid'        => array('id' => 'tsid', 'default' => ''),
			'ofid'        => array('id' => 'ofid', 'default' => ''),
			'opid'        => array('id' => 'opid', 'default' => ''),
			'opid2'       => array('id' => 'opid2', 'default' => ''),
			'siteCode'    => array('id' => 'site', 'default' => 231),
			'mid_an'      => array('id' => 'midan', 'default' => ''),
			'mid_n'       => array('id' => 'midn', 'default' => ''),
			'position'    => array('id' => 'pos', 'default' => 1),
			'PHPSESSID'   => array('id' => 'PHPSESSID', 'default' => session_id()),
			'pathOrder'   => array('id' => 'path', 'default' => ''),
			'userInfo'    => array('id' => 'user', 'default' => ''),
			'ip'          => array('id' => 'ip', 'default' => $_SERVER['REMOTE_ADDR']),
			'redirectURL' => array('id' => 'redir', 'default' => ''),
			'trafficCap'  => array('id' => 'tcap', 'default' => ''),
			'numJoins'    => array('id' => 'joins', 'default' => ''),
			'referer'     => array('id' => 'refererUrl', 'default' => rawurlencode($_SERVER['HTTP_REFERER'])) // this line was added on 2011-07-05
		);
		
		if (isset($_REQUEST['debug']) && $_REQUEST['debug'] == '1')
			$this->urlParams['debug'] = array('id' => 'debug', 'default' => '');
		
		$this->init();
		
		$this->ccttsParams = array(
			'ccregID'    => $_SESSION['ccregID'],
			'pageID'     => $this->pathOrderArr[$this->position]['pageID'],
			'templateID' => $this->pathOrderArr[$this->position]['templateID'],
			'position'   => $this->position,
			'ip'         => $_SERVER['REMOTE_ADDR'],
			'sessionID'  => $_SESSION['PHPSESSID'],
			'tsid'       => $_SESSION['tsid'],
			'ofid'       => $_SESSION['ofid'],
			'opid'       => $_SESSION['opid'],
			'opid2'      => $_SESSION['opid2'],
			'ref'        => $_SESSION['referer']
		);
		
		$this->templateFolder = $this->pathOrderArr[$this->position]['templateFolder'];
		$this->pageTitle = $this->pathOrderArr[$this->position]['pageTitle'];
		$this->pageFolder = $this->pathOrderArr[$this->position]['folderName'];
		$this->pageFile = $this->pathOrderArr[$this->position]['fileName'];
	}
	
	// executes the pre-HTML processes.
	public function loadProcess() {
		// register page view
		$this->pixelPageView();
		
		// load next URL string
		$nextPosition = $this->position + 1;
		$lastPosition = array_pop(array_keys($this->pathOrderArr));
		
		for ($i = $nextPosition; $i <= $lastPosition + 1; $i++) {
			if (array_key_exists($i, $this->pathOrderArr)) {
				$nextPosition = $i;
				break;
			}	
		}
		
		// generate next page's URL
		if ($nextPosition > $lastPosition)
			$this->nextUrl = 'javascript: parent.location.href=\''. $this->redirectURL .'\''; // end of path, redirect to affiliate's URL
		else 
			$this->nextUrl = $this->getNextUrl($nextPosition);
		
		$this->loadView();
	}
	
	// load the view
	protected function loadView() {
		if ($this->active == 0) {
			require_once($GLOBALS['serverPath'] .'templates/header.php');
			require_once($GLOBALS['serverPath'] . 'pages/redirect.php');
			require_once($GLOBALS['serverPath'] .'templates/footer.php');
			exit;
		}
		
		require_once($GLOBALS['serverPath'] . 'templates/header.php');
		require_once($GLOBALS['serverPath'] . 'pages/'. $this->pageFolder .'/'. $this->pageFile);
		require_once($GLOBALS['serverPath'] . 'templates/footer.php');
		
		if (isset($_GET['debug'])) {
			echo "<!--\n";
			echo "SESSION: ". var_export($_SESSION, true);
			echo "\nthis: ". var_export($this, true);
			echo "\nPOST: ". var_export($_POST, true);
			echo "\nGET: ". var_export($_GET, true);
			echo "\nREQUEST: ". var_export($_REQUEST, true);
			echo "\nCOOKIE: ". var_export($_COOKIE, true);
			echo "\n-->";
		}
	}
	
	// initialize session variables
	protected function init() {
		$urlEncode = array('redirectURL', 'referer');
		
		foreach ($this->urlParams as $sessionParamName => $paramArr) {
			if (!in_array($sessionParamName, $urlEncode)) $_SESSION[$sessionParamName] = (isset($_GET[$paramArr['id']])) ? $_GET[$paramArr['id']] : $paramArr['default'];
			else $_SESSION[$sessionParamName] = (isset($_GET[$paramArr['id']])) ? rawurlencode($_GET[$paramArr['id']]) : rawurlencode($paramArr['default']);
		}
		
		if ($_SESSION['pathOrder'] == '' || $_SESSION['redirectURL'] == '' || $_SESSION['active'] == '' || $_SESSION['trafficCap'] == '' || $_SESSION['numJoins'] == '') {
			$url = $GLOBALS['ccttsUrl'] .'cctts_inc/ajax/ccReg.php';
			$params = 'method=initPath&ccregID='. $_SESSION['ccregID'];
			$result = $this->curlIt($url, $params);
			if (strpos('ERROR', $result) === false) {
				$resultArr = unserialize($result);
				
				if ($_SESSION['active'] == '') $_SESSION['active'] = $resultArr['active'];
				if ($_SESSION['pathOrder'] == '') $_SESSION['pathOrder'] = $resultArr['pathOrder'];
				if ($_SESSION['redirectURL'] == '') $_SESSION['redirectURL'] = $resultArr['redirectURL'];
				if ($_SESSION['trafficCap'] == '') $_SESSION['trafficCap'] = $resultArr['trafficCap'];
				if ($_SESSION['numJoins'] == '') $_SESSION['numJoins'] = $resultArr['numJoins'];
			} else {
				foreach ($_SESSION as $key => $val)
					unset($_SESSION[$key]);
				exit('<!-- Error happened while loading path. '. $result .' -->');
			}
		}
		
		$this->active = $_SESSION['active'];
		$this->pathOrderArr = unserialize(base64_decode($_SESSION['pathOrder']));
		
		$this->userInfo = unserialize(base64_decode($_SESSION['userInfo']));
		foreach ($this->userInfo as $field => $val)
			$this->userInfo[strtolower($field)] = $val;
		
		$this->redirectURL = rawurldecode($_SESSION['redirectURL']);
		$this->trafficCap = unserialize(base64_decode($_SESSION['trafficCap']));
		$this->numJoins = unserialize(base64_decode($_SESSION['numJoins']));
	}
	
	// curl calls the pageview pixel
	protected function pixelPageView() {
		$url = $GLOBALS['ccttsUrl'] .'cctts_inc/ajax/ccReg.php';
		$paramsArr = array('method=pixelPageView');
		foreach ($this->ccttsParams as $key => $val)
			$paramsArr[] = "$key=$val";
		
		$paramsStr = implode('&', $paramsArr);
		$result = $this->curlIt($url, $paramsStr);
	}
	
	// returns the next page's url
	protected function getNextUrl($pos, $paramsOnly = false) {
		$tempArr = array();
		$tempStr = '';
		$tempUrl = $GLOBALS['chregUrl'];
		
		$exceptionArr = array( // array to overwrite session values with
			'pos' => $pos
		);
		
		foreach($this->urlParams as $sessionParamName => $paramArr) {
			if (array_key_exists($paramArr['id'], $exceptionArr))
				$tempArr[] = $paramArr['id'] .'='. $exceptionArr[$paramArr['id']];
			else
				$tempArr[] = $paramArr['id'] .'='. $_SESSION[$sessionParamName];
		}
		
		$params = implode('&', $tempArr);
		
		if ($paramsOnly)
			return $params;
		else
			return $tempUrl .'?'. $params;
	}
	
	// returns the link URL on a path page
	protected function getPathLinkUrl($paramsOnly = false) {
		$tempArr = array();
		$tempStr = '';
		$tempUrl = $GLOBALS['chregUrl'] . 'link.php';
		
		foreach($this->ccttsParams as $sessionParamName => $value)
			$tempArr[$sessionParamName] = $value;
		
		$tempArr['linkID'] = $this->pathOrderArr[$this->position]['linkID'];
		$tempArr['linkURL'] = $this->pathOrderArr[$this->position]['linkURL'];
		
		$params = base64_encode(json_encode($tempArr));
		
		if ($paramsOnly)
			return $params;
		else
			return $tempUrl .'?params='. $params;
	}
	
	// curl calls the submit pixel
	public function pixelJoin($type='12') {
		$url = $GLOBALS['ccttsUrl'] .'cctts_inc/ajax/ccReg.php';
		$paramsArr = array('method=pixelJoin', 'type='.$type);
		foreach ($this->ccttsParams as $key => $val)
			$paramsArr[] = "$key=$val";
		
		$paramsStr = implode('&', $paramsArr);
		$result = $this->curlIt($url, $paramsStr);
		
		return $result;
	}
	
	// curl calls the path click pixel
	public function pixelPathClick($clickParamsStr) {
		$url = $GLOBALS['ccttsUrl'] .'cctts_inc/ajax/ccReg.php';
		$paramsArr = array('method=pixelPathClick', 'clickParams='.$clickParamsStr);
		foreach ($this->ccttsParams as $key => $val)
			$paramsArr[] = "$key=$val";
		
		$paramsStr = implode('&', $paramsArr);
		$result = $this->curlIt($url, $paramsStr);
		
		return $result;
	}
	
	// grabs state list for select boxes
	public function getStateList($state='') {
		$states = array("AL", "AK", "AZ", "AR", "CA", "CO", "CT", "DC", "DE", "FL", "GA", "HI", "ID", "IL", "IN", "IA", "KS", "KY", "LA", "ME", "MD", "MA", "MI", "MN", "MS", "MO", "MT", "NE", "NV", "NH", "NJ", "NM", "NY", "NC", "ND", "OH", "OK", "OR", "PA", "RI", "SC", "SD", "TN", "TX", "UT", "VT", "VA", "WA", "WV", "WI", "WY");
		
		$options = '';
		foreach ($states as $eachState) {
			$selected = ($state == $eachState) ? ' selected="selected"' : '';
			
			$options .= '<option value="'. $eachState .'"'. $selected .'>'. $eachState ."</option>\n";
		}
		return $options;
	}
	
	protected function recordLoadTimes($key, $record = true) {
		if ($record) $this->loadTime[$key] = microtime(true);
		else {
			$startTime = $endTime = false;
			$display = array();
			foreach ($this->loadTime as $marker => $time) {
				if (empty($startTime)) $startTime = array(0 => $marker, 1 => $time);
				else {
					$endTime = array(0 => $marker, 1 => $time);
					$display['time between '. $startTime[0] .' and '. $endTime[0]] = $endTime[1] - $startTime[1];
					$startTime = $endTime;
				}
			}
			
			return $display;
		}
	}
	
	public function curlIt( $url, $postData = '', $proxyHost = '', $proxyPort = '', $options = '', $timeout = 60 ) {
		global $referer, $cookie;
		
		if( $cookie == '' )
			$cookie = uniqid( 'COOKIE' );
		
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.4) Gecko/20060508 Firefox/1.5.0.4' );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, '/tmp/'.$cookie.'.txt' );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, '/tmp/'.$cookie.'.txt' );
		
		if( $referer != '' )
			curl_setopt( $ch, CURLOPT_REFERER, $referer );
		
		$referer = $url;
		
		if( $postData != '' ) {
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $postData );
		}
		
		if( $proxyHost != '' && $proxyPort != '' ) {
			curl_setopt( $ch, CURLOPT_HTTPPROXYTUNNEL, 1 );
			curl_setopt( $ch, CURLOPT_PROXY, "$proxyHost:$proxyPort" );
		}
		
		if( $options != '' )
			curl_setopt_array( $ch, $options );
		
		return curl_exec( $ch );
	}
}
?>