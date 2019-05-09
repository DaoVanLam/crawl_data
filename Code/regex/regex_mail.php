<?php
$string = file_get_contents("example_mail.txt");
 
if(preg_match_all('/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}/i', $string, $found_mails)){
	print_r($found_mails);
}else{
	echo 'not found';
}

