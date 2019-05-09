<?php

error_reporting(E_ERROR);

set_time_limit(0); 

$url = 'http://mp3.zing.vn/';

$proxy = trim(file_get_contents('proxy.txt'));

//echo get_data($url,$proxy);

$r = download_file('http://kingofwallpapers.com/girl/girl-015.jpg','data/abc.jpg');

if(empty($r)){
    echo 'download sucess';
}else{
    echo 'download fail: ' . $r;
}


 function download_file($url , $path){
     $f = fopen($path, 'w');
     
     $ch = curl_init($url);
     
     curl_setopt($ch, CURLOPT_FILE, $f);
     curl_setopt($ch, CURLOPT_TIMEOUT, 28800);
     
     curl_exec($ch);
     
     $e = curl_error($ch);// '' sucess $ '' fail
     
     curl_close($ch);
     
     fclose($f);
     
     return $e;
 }


function get_data($link, $proxy = null, $proxy_type = null){
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
        
        if(isset($proxy) && check_proxy_live($proxy)){
            //proxy
            curl_setopt($ch, CURLOPT_PROXY, $proxy);

            if(isset($proxy_type))
                curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy_type);
        }
        
        
        
	$data = curl_exec($ch); 
	
	curl_close($ch);
	
	return $data;
}   




function check_proxy_live($proxy){
    $waitTimeoutInSeconds = 1; 
    
    $proxy_split = explode(':', $proxy);
    
    $ip = $proxy_split[0];
    $port = $proxy_split[1];
    
    $result = false;
         
    if($fp = fsockopen($ip,$port,$errCode,$errStr,$waitTimeoutInSeconds)){   
       $result = true;
       fclose($fp);
    }  
     
    return $result;
}

function check_proxy_lives($proxys){
    $proxy_lives = array();
    
    for($i = 0; $i < count($proxys); ++$i){
        $p = $proxys[$i];
        if(check_proxy_live($p)){
           $proxy_lives[] = $p;
        }
    }
    
    return $proxy_lives;
}


//$proxy_txt = file_get_contents('proxy_list.txt');
//$lines = explode("\r\n", $proxy_txt); 
//
//var_dump(check_proxy_lives($lines));

