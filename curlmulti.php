<?php

/**
 * CURL MULTI - Demo
 *
 * Demo for PHP curl asynchronous mode. You can significantly reduce the HTTP request time
 * by using curl_multi.
 */
class CurlMulti {

	public $multi_init;
	public $curl_init = array();

	public function __construct() {

		$this->multi_init = curl_multi_init();
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

	public function __destruct() {

	}
}
