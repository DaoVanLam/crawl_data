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

define('BASE_URL','https://www.vietinbank.vn');

// Initialize
$database = new medoo([
    'database_type' => 'mysql',
    'database_name' => 'db_web_homestay',
    'server' => 'localhost',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8'
]);

$root = 'https://www.vietinbank.vn/web/home/vn/index.html';

if(get_data($root,$content)){
    save_all_post($content);
}else{
    echo 'Cannot get data for this page ' . PHP_EOL;
}

function save_all_post($html){
    $dom = new Document();
    $dom->load($html);
    
    $block = $dom->find('div[class=tygiaTbl]')[0];
    
    $newsItems = $block->find('div[class^=exline]');
    
    if(isset($newsItems) && count($newsItems) > 1){
    for($i = 0; $i < count($newsItems); ++$i){
        $newsItem = $newsItems[$i];

        $title = $newsItem->find('span[class=extext]')[0]->text();
        $thumb = $newsItem->find('span[class=exnumber]')[0]->text();
        $post = array();  
        $post['ten_lai_suat'] = $title;
        $post['gia_tri']  = $thumb;  
    
        insert_post($post); 
       
 }
}
}

function get_content($link){
    if(get_data($link, $content)){
         $dom = new Document();
         $dom->load($content);
         
         $html = $dom->find('div[id=laixuat]')[0]->html();
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