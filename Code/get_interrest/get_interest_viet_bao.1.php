<?php
include './libs/medoo.php';

include './libs/Curl/CaseInsensitiveArray.php';
include './libs/Curl/Curl.php';
include './libs/Curl/MultiCurl.php';

include './libs/DiDom/Document.php';
include './libs/DiDom/Element.php';
include './libs/DiDom/Query.php';


use Curl\Curl;

use DiDom\Document;
use DiDom\Element;

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

$url = 'http://vietbao.vn/vn/lai-suat-tiet-kiem/';

if(get_data($url, $content)){
    //echo 'Lấy dữ liệu thành công';
    //get_name_bank($content);
    $name = get_name_bank($content);
    $link = get_link_bank($content);

    $bank = array();
    $bank['ten_ngan_hang'] = $name;
    $bank['link'] = $link;
     
    $data = insert_bank($bank);
    $id = $data['ten_ngan_hang'];
    save_interest($id,$content);
}else{
    echo 'Cannot get data for this page !!!!'.PHP_EOL;;
}


function get_name_bank($content){
    $name_bank = '';
    
    $dom = new Document();
    $dom->load($content);
    $item_chapters = $dom->find('table[class=ruler]');
    if(isset($item_chapters) && count($item_chapters) > 0){
        for($i = 0; $i < count($item_chapters);++$i){
            //echo $item_chapters[$i];

            $item_chapter = $item_chapters[$i];
            $names = $item_chapter->find('img');
            if (is_array($names) || is_object($names))
            {
            foreach($names as $name) {
                $name_bank = $name->alt;
                //echo $name_bank;
            }
            }
            // $links = $item_chapter->find('img');
            // foreach($links as $link) {
            //         $link_bank = BASE_URL .$link->src;
            //     }            
            // $bank = array();
            // $bank['ten_ngan_hang'] = $name_bank;
            // $bank['link'] = $link_bank; 
            // insert_bank($bank);
        }
    }
    return $name_bank;
}
function get_link_bank($content){
    $link_bank = '';
    
    $dom = new Document();
    $dom->load($content);
    $item_chapters = $dom->find('table[class=ruler]');
    if(isset($item_chapters) && count($item_chapters) > 0){
        for($i = 0; $i < count($item_chapters);++$i){
            $item_chapter = $item_chapters[$i];
            $links = $item_chapter->find('img');
            if (is_array($links) || is_object($links))
            {
            foreach($links as $link) {
                    $link_bank = BASE_URL .$link->src;
                }            
            }
            // $bank = array();
            // $bank['ten_ngan_hang'] = $name_bank;
            // $bank['link'] = $link_bank; 
            //insert_bank($bank);
        }
    }
    return $link_bank;
}
function insert_bank($bank){
    $name = $bank['ten_ngan_hang'];
    $link = $bank['link'];
    
     $sql = "INSERT INTO tbl_ngan_hang(ten_ngan_hang,link)".
      " SELECT '$name', '$link' FROM DUAL".
      " WHERE NOT EXISTS (SELECT * FROM tbl_ngan_hang".
      " WHERE link = '$link') LIMIT 1";
    //echo $sql;
    global $database;
    
    $database->query($sql);
    
    $data = $database->query("SELECT * FROM tbl_ngan_hang WHERE link = '$link'")->fetch();
    
    return $data;
}

function save_all_image($chapter_id, $url){
    $folder_name = bin2hex(openssl_random_pseudo_bytes(16));
    $folder_path = 'data/'.$folder_name;
    
    echo 'Create folder: '.$folder_name.PHP_EOL;
    
    
    mkdir($folder_path, 0777, true);
    
    if(get_data($url, $content)){
        $dom = new Document();
        $dom->load($content);
        
        $images = $dom->find('div[id=divImage]')[0]->find('img');
        
        if(isset($images) && count($images) > 1){
            for($i = 0; $i < count($images);++$i){
                $image = $images[$i]; 
                $link_image = $image->getAttribute('src');
                
                echo 'Look: '.$link_image.PHP_EOL;
                
                $ext = pathinfo($link_image, PATHINFO_EXTENSION); 
                $file = $folder_path.'/'.$i.'.'.$ext;
                
                download_file($link_image, $file);
                
                $img = array();
                
                $img['image_link'] = $link_image;
                $img['image_path'] = $file;  
                
                insert_image($chapter_id, $img);
            }
        }
        
    }
    
}
// Interest

// interest_id
// interest_name
// interest_value
// interrest_date
// bank(id)
function save_interest($bank_id,$content){
    $dom = new Document();
    $dom->load($content);
    
    $item_chapters = $dom->find('table[class=ruler]')[0]->find('tr');

    if(isset($item_chapters) && count($item_chapters) > 0){
        for($i = 0; $i < count($item_chapters);++$i){
            $item_chapter = $item_chapters[$i];
            $names = $item_chapter->find('td[class $ =block-1]');
            foreach($names as $name) {
                    echo $name->html();
                }
            // $date = $item_chapter->find('span')[0]->text();
            // $chapter_name = $item_chapter->find('a')[0]->text();
            // $href = BASE_URL . $item_chapter->find('a')[0]->getAttribute('href');
            
            // $chapter = array();
            // $chapter['chapter_name'] = $chapter_name;
            // $chapter['chapter_date'] = $date; 
            // $chapter['chapter_link'] = $href;
            
            // $data = insert_chapter($store_id, $chapter);
            
            
            // $chapter_id = $data['chapter_id'];
            
            // save_all_image($chapter_id,$href);
              
            //echo "Date: $date - $chapter_name - $href" . PHP_EOL;
        }
    }
}


function download_file($url, $path){
    $curl = new Curl();
    
    echo 'Start download: ' .$url.PHP_EOL;
    
    $curl->setConnectTimeout(60);
    $curl->setTimeout(60);
    
    $re = $curl->download($url, $path);
    
    if($re){
        echo 'End download: ' .$url . ' Sucess !!!' .PHP_EOL;
    }else{
        echo 'End download: ' .$url . ' Failt !!!' .PHP_EOL;
    }
    
    $curl->close();
}

function get_data($url, &$content){
    $curl = new Curl();
    
    echo 'Start craw: ' .$url.PHP_EOL;
    
    $curl->setConnectTimeout(60);
    $curl->setTimeout(60);
    
    $curl->get($url);
    
    if(!$curl->error){
        $content = $curl->response;
        echo 'End craw: ' .$url.' Success !!!'.PHP_EOL;
    }else{
        echo 'End craw: ' .$url.' Failt!!!'.PHP_EOL;
    }
    
    $curl->close();
    
    return !$curl->error;
}


function insert_chapter($store_id, $chapter){ 
                $chapter_name = $chapter['chapter_name'];
                $chapter_date = $chapter['chapter_date']; 
                $chapter_link = $chapter['chapter_link'];
                
                
     $sql = "INSERT INTO chapter (chapter_name, chapter_date,chapter_link,store_id)".
     " SELECT '$chapter_name', '$chapter_date','$chapter_link',$store_id FROM DUAL".
     " WHERE NOT EXISTS (SELECT * FROM chapter".
     " WHERE chapter_link = '$chapter_link') LIMIT 1";
     
     global $database;
    
     $database->query($sql);
     
     $data = $database->query("SELECT * FROM chapter WHERE chapter_link = '$chapter_link'")->fetch();
       
     return $data;
}


function insert_image($chapter_id, $image){ 
                $image_link = $image['image_link'];
                $image_path = $image['image_path'];  
                
                
     $sql = "INSERT INTO image (image_link, image_path,chapter_id)".
     " SELECT '$image_link', '$image_path',$chapter_id FROM DUAL".
     " WHERE NOT EXISTS (SELECT * FROM image".
     " WHERE image_link = '$image_link') LIMIT 1";
     
     global $database;
    
     $database->query($sql);
     
     $data = $database->query("SELECT * FROM image WHERE image_link = '$image_link'")->fetch();
       
     return $data;
}
//Bank 

// bank_id(AI,INT)
// bank_name
// bank_image

// Interest

// interest_id
// interest_name
// interest_value
// interrest_date
// bank(id)




//- image
//	+ image_id (AI,P, INT)
//	+ image_link (TEXT)
//	+ image_path (TEXT)
//	+ chapter_id (INT)

//- chapter
//	+ chapter_id (AI,P)
//	+ chapter_name (TEXT)
//	+ chapter_date (TEXT)
//	+ chapter_link (TEXT)
//	+ store_id (INT)
//- store

//	+ store_id (AI,P,INT)
//	+ store_name (TEXT)
//	+ store_link (TEXT) 
//	+ store_desc (TEXT)
//	+ store_release (INT)



?>