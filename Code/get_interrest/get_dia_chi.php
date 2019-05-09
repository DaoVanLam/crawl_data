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

define('BASE_URL','http://vietbao.vn/');

// Initialize
$database = new medoo([
    'database_type' => 'mysql',
    'database_name' => 'db_web_homestay',
    'server' => 'localhost',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8'
]);

$root = 'http://vietbao.vn/vn/diem-dat-atm/';

if(get_data($root,$content)){
    save_all_post($content);
}else{
    echo 'Cannot get data for this page ' . PHP_EOL;
}

function save_all_post($html){
    $dom = new Document();
    $dom->load($html);
    
    $block = $dom->find('table[id=default2]')[0];
    
    $newsItems = $block->find('tr');
    
    if(isset($newsItems) && count($newsItems) > 1){
            for($i = 1; $i < count($newsItems); ++$i){
                //echo $newsItems[$i];
                $newsItem = $newsItems[$i];
                
                $address = $newsItem->find('a')[0]->text();
                $names = $newsItem->find('strong');
               foreach($names as $name) {
                if(($name->text() =='ACB')OR($name->text() =='BIDV') OR
                ($name->text() =='Agribank')OR($name->text() =='Vietcombank') OR ($name->text() =='VietinBank') OR  ($name->text() =='VPBank')) {
                    $name_bank = $name->text();
                } else {
                    break;
                }
            }
             $post = array();  
             $post['dia_chi'] = $address;
             $post['ten_ngan_hang']  = $name_bank;        
             insert_post($post); 
            
            //echo ' title: '.$title.'<br />';
            //echo 'content: '. htmlspecialchars($content);
        }
    }
    
    
    
}

function get_content($link){
    if(get_data($link, $content)){
         $dom = new Document();
         $dom->load($content);
         
         $html = $dom->find('div[id=default1]')[0]->html();
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
    $names = $post['ten_ngan_hang'];
    $address = $post['dia_chi'];
    
    $sql = "INSERT INTO tbl_dia_chi (ten_ngan_hang, dia_chi)".
     " SELECT '$names', '$address' FROM DUAL";
     
    echo $sql;
    
    global $database;
    
    $database->query($sql);
    
    $data = $database->query("SELECT * FROM tbl_dia_chi")->fetch();
    
    return $data;
}


?>