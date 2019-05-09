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

define('BASE_URL','https://www.laisuatnganhang.com.vn/');

//define('Date',getdate()->text);
// Initialize
$database = new medoo([
    'database_type' => 'mysql',
    'database_name' => 'db_web_homestay',
    'server' => 'localhost',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8'
]);

$url = 'https://www.laisuatnganhang.com.vn/bieu-do-lai-suat/ngan-hang-tmcp-dau-tu-va-phat-trien-viet-nam-bidv/';

if(get_data($url, $content)){
    get_interest_bank($content);
}else{
    echo 'Cannot get data for this page !!!!'.PHP_EOL;;
}

function get_interest_bank($content){
    $name ="";

    $array_name = array(10);
    $array_value_interest = array(10);

    $dom = new Document();
    $dom->load($content);
    $item_chapters = $dom->find('div[id=bang1]')[0]->find('table[id=guibank]')[0]->find('tr');
    if(isset($item_chapters) && count($item_chapters) > 0){
        for($i = 0; $i < count($item_chapters);++$i){
            //echo $item_chapters[$i];
            $item_chapter = $item_chapters[$i];

            $name_banks = $item_chapter->find('img');
            $name_interest_banks = $item_chapter->find('th');
            $values = $item_chapter->find('td');
            //var_dump(values);


            $date = getdate();

            foreach($name_banks as $name_bank) {
                $name = $name_bank->alt;
                //$array_name =$name;
            }
            //echo $name;
            foreach($name_interest_banks as $name_interest_bank) {
                $name_interest = $name_interest_bank->text();
                $array_name =  $name_interest;
                //echo $name_interest->text();
                //echo $array_name;
            }
            foreach($values as $value) {
                $value_interest = $value ->text();
                $array_value_interest = $value_interest;
                //echo $value_interest;
            }

            $bank = array();
            $bank['ten_ngan_hang'] = $name;
            $bank['ten_lai_suat'] = $array_name; 
            $bank['gia_tri'] = $array_value_interest; 
            $bank['ngay_nhap_lai']= $date;
            insert_interest_bank($bank);
        }
    }
}
function insert_interest_bank($bank){
    $name_bank = $bank['ten_ngan_hang'];
    $name_interest = $bank['ten_lai_suat'];
    $value_interest = $bank['gia_tri']; 
    $date_import_interest = $bank['ngay_nhap_lai'];
    var_dump($bank);
     $sql = "INSERT INTO tbl_lai_suat_tiet_kiem(ten_ngan_hang,ten_lai_suat,gia_tri,ngay_nhap_lai)".
      " SELECT '$name_bank', '$name_interest','$value_interest','$date_import_interest' FROM DUAL";

    //   $sql = "INSERT INTO tbl_lai_suat_tiet_kiem(ten_ngan_hang,ten_lai_suat,gia_tri,ngay_nhap_lai)".
    //   " SELECT '$name__bank', '$name_interest','$value_interest','$date_import_interest' FROM DUAL".
    //   " WHERE NOT EXISTS (SELECT * FROM tbl_ngan_hang".
    //   " WHERE link = '$link') LIMIT 1";

    //   global $database;
    
    //   $database->query($sql);
      
    //   $data = $database->query("SELECT * FROM tbl_ngan_hang WHERE link = '$link'")->fetch();
        
    //   return $data;
    //save_interest($id,$content);
      
}
function get_interest($content){
    $dom = new Document();
    $dom->load($content);
    
    $item_chapters = $dom->find('table[class=ruler]')[0]->find('tr');

    if(isset($item_chapters) && count($item_chapters) > 0){
        for($i = 0; $i < count($item_chapters);++$i){
            //echo $item_chapters[$i];
            $item_chapter = $item_chapters[$i];

             $names = $item_chapter->find('td[class $ =block-1]');

             foreach($names as $name) {
                if($name->text() !== 'See also') {
                    echo $name;
                } else {
                    break;
                }
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

function insert_interest($store_id, $chapter){ 
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