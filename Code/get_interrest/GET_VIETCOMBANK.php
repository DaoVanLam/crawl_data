<?php
set_time_limit(0);

include 'libs/Curl/CaseInsensitiveArray.php'; 
include 'libs/Curl/Curl.php'; 
include 'libs/Curl/MultiCurl.php';

include 'libs/DiDom/Document.php';
include 'libs/DiDom/Query.php';
include 'libs/DiDom/Element.php';

include 'libs/medoo.php';

use \Curl\Curl;
use \DiDom\Document;
use \DiDom\Query;
use \DiDom\Element;

define('BASE_URL','https://webgia.com');
define('Name','Vietcombank');
// Initialize
$database = new medoo([
    'database_type' => 'mysql',
    'database_name' => 'db_web_homestay',
    'server' => 'localhost',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8'
]);

$root = 'https://webgia.com/lai-suat/vietcombank/';

if(get_data($root,$content)){
    save_all_post($content);
}else{
    echo 'Cannot get data for this page ' . PHP_EOL;
}

function save_all_post($html){
    $dom = new Document();
    $dom->load($html);
    
    $block = $dom->find('table')[0];
    
    $newsItems = $block->find('tr');
    
    if(isset($newsItems) && count($newsItems) > 1){
    for($i = 0; $i < count($newsItems); ++$i){
        $newsItem = $newsItems[$i];
        //echo $newsItems[$i];
          $name_bank = Name;
          $titles = $newsItem->find('td[class=text-left]');
          $values = $newsItem->find('td');
          foreach($titles as $title) {
                if($title->text() =='') {
                    echo 'title: '.$title.'<br />';
                } else {
                    break;
                }
            }

            foreach($values as $value) {
                if($value->text() !== '1 tháng' OR $value->text() !== '2 tháng'  
                 ) {
                    echo 'value: '.$value.'<br />';
                } else {
                    break;
                }
            }
    //     $thumb = $newsItem->find('span[class=exnumber]')[0]->text();
    //     $post = array();  
    //     $post['ten_lai_suat'] = $title;
    //     $post['gia_tri']  = $thumb;  
    
    //     insert_post($post); 
         //echo 'title: '.$values.'<br />';
       
 }
}
}

function get_content($link){
    if(get_data($link, $content)){
         $dom = new Document();
         $dom->load($content);
         
         $html = $dom->find('div[class=table-responsive]')[0]->html();
         return $html; 
    }
    return '';
}



function get_data($link , &$content){
    $curl = new Curl();
    
    echo 'Start craw: ' .$link.PHP_EOL;
    
    $curl->setTimeout(60);
    $curl->setConnectTimeout(60);
    
    $curl->get($link);
    
    $error = $curl->error;
    
    if(!$error){
        $content = $curl->response;
        echo 'End craw: ' .$link.' Sucess !!!'.PHP_EOL;
    }else{
        echo 'End craw: ' .$link.' Failt !!!'.PHP_EOL;
    }
    
    $curl->close();
    
    return !$error;
}
function insert_post($post){
    $title = $post['ten_lai_suat'];
    $thumb = $post['gia_tri'];

    $sql = "INSERT INTO tbl_lai_suat_tiet_kiem (ten_lai_suat, gia_tri)".
     " SELECT '$title', '$thumb'FROM DUAL.";
     
    //echo $sql;
    
    global $database;
    
    $database->query($sql);
    
    $data = $database->query("SELECT * FROM tbl_lai_suat_tiet_kiem")->fetch();
    
    return $data;
}
?>