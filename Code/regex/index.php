<?php


//abc…	Letters
//123…	Digits
//\d	Any Digit
//\D	Any Non-digit character
//.	Any Character
//\.	Period
//[abc]	Only a, b, or c
//[^abc]	Not a, b, nor c
//[a-z]	Characters a to z
//[0-9]	Numbers 0 to 9
//\w	Any Alphanumeric character
//\W	Any Non-alphanumeric character
//{m}	m Repetitions
//{m,n}	m to n Repetitions
//*	Zero or more repetitions
//+	One or more repetitions
//?	Optional character
//\s	Any Whitespace
//\S	Any Non-whitespace character
//^…$	Starts and ends
//(…)	Capture Group
//(a(bc))	Capture Sub-group
//(.*)	Capture all
//(abc|def)	Matches abc or def

$data = "123 dasdasdadsasd 4434 adadasdas 6666";

$partern = "/[0-9]{3}/";

if(preg_match_all($partern, $data,$match)){
	var_dump($match);
	echo 'OK';
}else{
	echo 'NO';
}