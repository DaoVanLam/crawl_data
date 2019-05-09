<?php

$url = 'http://blogtruyen.com/trangchu';


$ch = curl_init();

$proxy_ip = '101.26.38.162'; //proxy IP here
$proxy_port = 81; //proxy port from your proxy list

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0");
//curl_setopt($ch, CURLOPT_REFERER, "https://www.google.com/?gws_rd=ssl");
//curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
//curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
//curl_setopt($ch, CURLOPT_PROXY, $proxy_ip);


$data = curl_exec($ch);

curl_close($ch);

echo htmlspecialchars($data);