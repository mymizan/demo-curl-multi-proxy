<?php
require __DIR__ . '/curlmulti.php';

$curl = new CurlMulti;
$curl->addRequest('http://ip.jsontest.com/');
$curl->addRequest('http://time.jsontest.com');
$curl->addRequest('http://echo.jsontest.com/key/value/one/two');

$curl->executeAll('on_request_complete');

function on_request_complete($content) {
	echo $content;
}