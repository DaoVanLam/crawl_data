<?php

include 'Curl/CaseInsensitiveArray.php';
include 'Curl/Curl.php';
include 'Curl/MultiCurl.php';

include 'DiDom/Document.php';
include 'DiDom/Element.php';
include 'DiDom/Query.php';

use Curl\Curl;
use DiDom\Document;
use DiDom\Element;

$curl = new Curl();  
$curl->setOpt(CURLOPT_ENCODING , '');
$html = $curl->get('http://dantri.com.vn/xa-hoi.htm');

$doc = new Document();
$doc->loadHtml($html);
 

$elements = $doc->find('div[class=mr1]');

for($i = 0;$i < count($elements);++$i){
    $e =  $elements[$i];
    
    echo $e->find('h2')[0]->find('a')[0]->text() . '<br />';
}




?>