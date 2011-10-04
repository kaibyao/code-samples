<?php
// API parent class for the actual 3rd party APIs
class api {
	public $overlord;
	
	public function __construct() {
		$this->overlord = new Overlord();
	}
	
	public function curlIt( $url, $postData = '', $httpHeaders = 0, $proxyHost = '', $proxyPort = '', $options = '', $timeout = 60 ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		if (!empty($httpHeaders) && is_array($httpHeaders))
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $httpHeaders );
		
		curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.0.4) Gecko/20060508 Firefox/1.5.0.4' );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
		//curl_setopt( $ch, CURLOPT_COOKIEJAR, '/tmp/'.$cookie.'.txt' );
		//curl_setopt( $ch, CURLOPT_COOKIEFILE, '/tmp/'.$cookie.'.txt' );
		
		if( isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '' )
			curl_setopt( $ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER'] );
		
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