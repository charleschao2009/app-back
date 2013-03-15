<?php
/**
 * Client for bus-api
 * 
 * @author Rox <rox@taomee.com> 
 */
class api {
	public static function query($s_method, $a_params = array(), $a_config = array(), $b_decode = true)
	{
		if (!$s_method || !$a_config) {
			return false;
		} 
		$ch = curl_init();
		$header_set = array('Accept: application/json', 'Connection: close', 'Expect:');
		if (!isset($a_config['host'])) {
			$url_info = parse_url($a_config['url']);
			if ($url_info && isset($url_info['host'])) {
				$ip = gethostbyname($url_info['host']);
				if ($ip) {
					$a_config['url'] = str_replace($url_info['host'], $ip, $a_config['url']);
					$a_config['host'] = $url_info['host'];
				} 
			} 
		} 
		if (isset($a_config['host']) && $a_config['host']) {
			$header_set[] = 'host: ' . $a_config['host'];
		} 
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header_set);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_URL, $a_config['url']);
		$a_params['method'] = $s_method;
		$s_params = array();
		foreach($a_params as $k => $v) {
			$s_params[] = rawurlencode($k) . '=' . rawurlencode($v);
		} 
		$s_params = implode('&', $s_params);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $s_params); 
		// setup defaults
		curl_setopt($ch, CURLOPT_USERAGENT, 'bus-client/0.1');
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		// send request
		$str_result = curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_status != 200) {
			Log :: write('Status:[' . $http_status . '], method:' . $s_method . ',url:' . $a_config['url'], 'api', 'error');
			return false;
		} 
		if (curl_errno($ch)) {
			throw new Exception(' Curl Error: ' . curl_error($ch));
		} 
		curl_close($ch); 
		// output
		if ($b_decode) {
			return json_decode($str_result, true);
		} else {
			return $str_result;
		} 
	} 
} 
