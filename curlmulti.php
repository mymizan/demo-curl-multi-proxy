<?php

/**
 * CURL MULTI - Demo
 *
 * Demo for PHP curl asynchronous mode. You can significantly reduce the HTTP request time
 * by using curl_multi.
 */
class CurlMulti {

	public $multi_init;
	public $curl_init;

	public $proxy_address;
	public $proxy_auth;

	public $cookie_file;

	public function __construct() {

		$this->multi_init = curl_multi_init();
		$this->curl_init = array();
	}

	/**
	 * Queue an URL
	 * @param string $url         Valid URL
	 * @param string $type        GET or POST
	 * @param array  $curl_params Extra CURL paramenter to add new or overwrite the defaults
	 * @param array  $post_params Post parameters usind in POST request
	 */
	public function addRequest($url, $type = 'GET', $curl_params = array(), $post_params = array()) {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt_array($ch, $curl_params); //overwride default parameters or add new

		if ($type == 'POST') {

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
		}

		if (!empty($this->proxy_address)) {

			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_setopt($ch, CURLOPT_PROXY, $this->proxy_address);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy_auth);

		}

		if (!empty($this->cookie_file)) {

			curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file); //store changes
			curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file); //cookie file to read

		}

		curl_multi_add_handle($this->multi_init, $ch);
	}

	/**
	 * Execute all request in the queue and return the result
	 *
	 * @param  string $callback  callback function to reutn the result
	 * @return string            return the results
	 */
	public function executeAll($callback) {

		do {

			$mrc = curl_multi_exec($this->multi_init, $active);

		} while ($mrc == CURLM_CALL_MULTI_PERFORM);

		while ($active && $mrc == CURLM_OK) {

			if (curl_multi_select($this->multi_init) != -1) {

				do {
					$mrc = curl_multi_exec($this->multi_init, $active);

				} while ($mrc == CURLM_CALL_MULTI_PERFORM);

			} else {

				return;
			}

		}

		while ($info = curl_multi_info_read($this->multi_init)) {

			$content = curl_multi_getcontent($info['handle']);
			call_user_func($callback, $content);

		}

	}

	/**
	 * Proxy configurations
	 * @param string $address  proxy server address
	 * @param string $user     proxy server user
	 * @param string $password proxy server password
	 */
	public function setProxy($address, $user = null, $password = null) {

		$this->proxy_address = $address;

		if (!is_null($user)) {

			$this->proxy_auth = $user . ":" . $password;

		}
	}

	/**
	 * Set cookie file name
	 * @param string $cookie_file cookie file name
	 */
	public function setCookie($cookie_file) {

		$this->cookie_file = $cookie_file;

	}

	public function __destruct() {

	}
}
