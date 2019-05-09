<?php

set_time_limit(0);

$url = 'http://mp3.zing.vn/';


$ch = curl_init();


curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0');
curl_setopt($ch, CURLOPT_REFERER, 'https://www.google.com/');
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
 
$headers = array();
$headers[] = 'Host: mp3.zing.vn';
$headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0';
$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
$headers[] = 'Accept-Language: vi-VN,vi;q=0.8,en-US;q=0.5,en;q=0.3';
$headers[] = 'Accept-Encoding: gzip, deflate';
//$headers[] = 'Cookie: __mp3sessid=8B96A0773EA9; SRVID=s22169; _ga=GA1.2.203473192.1462298595; _gat=1; atmpv=1; _zploc=A246533205; __zi=2000.5c5faa059b34726a2b25.1462298597536.9ce1826e; adtimaUserId=2000.5c5faa059b34726a2b25.1462298597536.9ce1826e; fuid=7ec3df6cb4e4ac983d68ea276484de1f';
$headers[] = 'Connection: keep-alive';

//curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$data = curl_exec($ch);

curl_close($ch);

echo htmlspecialchars($data);