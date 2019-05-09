<?php
$string = file_get_contents("dantri.html");
 
if(preg_match_all('/(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $string, $found_urls)){
	
	for($i = 0; $i < count($found_urls[0]);++$i){
		echo $found_urls[0][$i] . ", ";
	}
}else{
	echo 'not found';
}

