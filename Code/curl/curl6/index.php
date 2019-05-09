<?php
include './libs/Curl/CaseInsensitiveArray.php';
include './libs/Curl/Curl.php';
include './libs/Curl/MultiCurl.php';

use \Curl\Curl;

$curl = new Curl();
$curl->setOpt(CURLOPT_ENCODING,'');
$curl->get('http://mp3.zing.vn/');


if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage;
}
else {
    echo $curl->response;
}

$curl->close();

?>