<?php 
set_time_limit(0);
 

$url = $_GET['url']; 
 
if(get_data($url, $html)){
    //Get xml link
    preg_match('#data-xml="(.+?)" class="#is', $html, $matches);
    $xml_link = $matches[1];
    
    if(get_data($xml_link, $xml)){
        //TODO
        preg_match('#<source><!\[CDATA\[(.+?)\]\]></source>#is', $xml, $matches);
        echo $matches[1];
    }else{
        echo 'Khong the lay duoc xml';
    }
}else{
    echo 'Khong the lay du lieu trang n';
}
 

function get_data($link, &$data = ''){
	$ch = curl_init(); 
	
	curl_setopt($ch, CURLOPT_URL, $link);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0');
	curl_setopt($ch, CURLOPT_REFERER, 'https://www.google.com/');
	curl_setopt($ch, CURLOPT_ENCODING, '');
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
         
	$data = curl_exec($ch); 
        
        $error = curl_error($ch);
        curl_close($ch);
        
        if(empty($error)){
            return true;
        } 
	 
	return false;
}   
