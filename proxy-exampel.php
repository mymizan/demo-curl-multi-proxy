<?php
require __DIR__ . '/curlmulti.php';

$curl = new CurlMulti;

$curl->setProxy('127.0.0.1:8888');
//$curl->setCookie('c.txt');
$curl->addRequest('http://www.google.com/');
$curl->addRequest('http://time.jsontest.com');
$curl->addRequest('http://echo.jsontest.com/key/value/one/two');

$curl->executeAll('on_request_complete');

function on_request_complete($content) {
	echo $content;
}