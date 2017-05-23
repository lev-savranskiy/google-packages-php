<?php
/***
 * google play fetch parser
 * @author Lev Savranskiy <lev.savranskiy@gmail.com>
 */


set_time_limit(0);
define('PREFIX', 'https://play.google.com/store/apps/details?id=');

error_reporting(E_ERROR);


function mysql_get_con($db_name)
{

    $db = "google-packages.amazonaws.com";
    $username = "username";
    $password = "password";
    $con = mysql_pconnect($db, $username, $password);
    if (!$con) {
        $subj =  "cannot connect to management db " . $db;
        $msg =  "hostname: " . gethostname() . PHP_EOL ;
        $msg .=  "db: " . $db . PHP_EOL ;
        $msg .=  "username: " . $username . PHP_EOL;
        die($msg);
    }
    //echo "$db_name CONNECT OK!";
    mysql_select_db($db_name, $con);
    return $con;
}

$manage_db_name = 'google_packages';
$manage_table_name = 'google-packages';
$mysql_conn = mysql_get_con($manage_db_name);

if(isset($argv[1]) && isset($argv[2]) ){
    $argument1 = $argv[1];
    $argument2 = $argv[2];
}else{
    die("no arguments provided");
}

//sleep prevents google ban
sleep ( rand ( 0, 30));
try {

    $query = "SELECT * FROM `$manage_table_name`  WHERE `id` BETWEEN $argument1 AND $argument2";
    $result = mysql_query($query);

    if (!$result) {
        echo "Could not  run query ($query) " . PHP_EOL . mysql_error();
        exit;
    }

    while ($row = mysql_fetch_assoc($result)) {
        sleep ( rand ( 3, 7));
        $id= $row["id"];
        $package= $row["package"];
        $data = get_data($package);
        //echo $data;
        $glue = ',';
        //$data = mb_convert_encoding($data, 'HTML-ENTITIES', "UTF-8");
        $dom = new DOMDocument();
        $dom->loadHTML($data);


       $banned =  $dom->getElementById('infoDiv0');
       $prefix  = $id . $glue . $package . $glue;
       if($banned){
           $category = 'banned';
           $downloads = 'banned';
       }else{
           $cat = getElementsByClass($dom , 'a' , 'category' , false);
           $category = 'none';
           $downloads = 'none';

           if($cat && $cat[0]){
               $metainfoholder = getElementsByClass($dom , 'div' , 'meta-info' );
               $category = $cat[0]->nodeValue;
               if($metainfoholder && $metainfoholder[1]){
                   $numDownloads = getElementsByClass($metainfoholder[1] , 'div' , 'content' );
                   if($numDownloads && $numDownloads[0]){
                       $downloads = $numDownloads[0]->nodeValue;
                       $category = trim($category);
                       $category = str_replace(',', ';', $category);
                       $downloads = trim($downloads);
                       $downloads = str_replace(',', '', $downloads);
                       $downloads = str_replace(' ', '', $downloads);
                   }
               }
           }
       }



        echo $prefix . $category . $glue . $downloads;
        echo PHP_EOL;

    }

} catch (Exception $e) {
    # 500 Internal Server Error
    die('error 500');
}


function getElementsByClass(&$parentNode, $tagName, $className, $strict = true)
{
    $nodes = array();

    $childNodeList = $parentNode->getElementsByTagName($tagName);
    for ($i = 0; $i < $childNodeList->length; $i++) {
        $temp = $childNodeList->item($i);
        $cls = $temp->getAttribute('class');
        if ($strict) {
            if (($cls == $className)) {
                $nodes[] = $temp;
            }
        } else {
            if ((stripos($cls, $className) !== false)) {
                $nodes[] = $temp;
            }
        }

    }

    return $nodes;
}
function get_data($suffix = "")
{
    $data = null;
    $url = PREFIX . $suffix;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;

}



