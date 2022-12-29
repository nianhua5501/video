<?php
header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Origin: http://y.laikai.net');//单域名授权
//$allow_origin = array(
    //'http://jx1.bskyblog.cn',
   // 'https://jx.bskyblog.cn',
   // 'https://ht.bskyblog.cn',
//);
//跨域访问的时候才会存在此字段
//$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';  
//if (in_array($origin, $allow_origin)) {
//    header('Access-Control-Allow-Origin:' . $origin);
//} else {
//    return;
//}
//error_reporting(0);
$t=time();
$path = "m3u8/".date("Ymd",$t).""; //保存目录为时间*/
$path = "m3u8"; //保存目录
$domain = "http://m3u8.6urls.cn/"; //你的域名 注意必须以 / 结尾
$token = "qWv0tc3OYrS1ksKqtBfrwaI7cEtKs7i8bjTFuY6u"; //后台获取的token
$name = $_GET['name'];
//----------------------------------------------
$type = $_GET['type'];
$llq = $_GET['llq'];
if (!isset($_GET['type'])) {
  exit("Error");
}

switch ($type) {
  case "test":
    if ($_GET['token'] != '') {
      exit("ok");
    } else {
      exit("error");
    }

    break;
  case "upload":
      if ($_FILES["file"]["error"] > 0) {
        $arr = array("code" => "100", "msg" => "error");
      } else
      {
        $path = !empty($path)?$path:"upload";
        // 判断当前目录下的 upload 目录是否存在该文件
        // 如果没有 upload 目录，你需要创建它，upload 目录权限为 777
  
        if (!file_exists($path)) {
          mkdir($path, 0777, true);
        }
  
        // 如果 upload 目录不存在该文件则将文件上传到 upload 目录下
        @unlink("$path/" . sha1($name.$token).".m3u8");
        move_uploaded_file($_FILES["file"]["tmp_name"], "$path/" . sha1(time().$token).".m3u8");
  
        $arr = array("code" => "200", "url" =>$domain."$path/".sha1(time().$token).".m3u8",'fileSha' => sha1($name.$token));
  
      }
      break;
    case "llq":
      $url = rc4b($llq,$token);
      header("Location:".$url);
}

exit(json_encode($arr));

function rc4($pwd, $data) 
{
  $key[] ="";
  $box[] ="1793D28CE8B6A3AD76EACCB2798C8FDD029D1576CDFDAE52672174B62C85C043697972A2CCABDCD09362330A2B617AA5CB1138742880258FB5588B4B46C32F5ABCA72588C8C63B6543DA4C7C52662CCDA5FFAB1D2CC51D8D1D62BAA3DC00889A5C99264F8494D8547C429169E1";
  $pwd_length = strlen($pwd);
  $data_length = strlen($data);
  for ($i = 0; $i < 256; $i++)
  {
    $key[$i] = ord($pwd[$i % $pwd_length]);
    $box[$i] = $i;
  }
  for ($j = $i = 0; $i < 256; $i++)
  {
    $j = ($j + $box[$i] + $key[$i]) % 256;
    $tmp = $box[$i];
    $box[$i] = $box[$j];
    $box[$j] = $tmp;
  }  

  for ($a = $j = $i = 0; $i < $data_length; $i++) 
  {
    $a = ($a + 1) % 256;
    $j = ($j + $box[$a]) % 256;
    $tmp = $box[$a];
    $box[$a] = $box[$j];
    $box[$j] = $tmp;
    $k = $box[(($box[$a] + $box[$j]) %256)];
    @$cipher .= chr(ord($data[$i]) ^ $k);
  }  
  return $cipher;
}
  
function hexToStr($hex)   
{   
  $string="";   
  for   ($i=0;$i<strlen($hex)-1;$i+=2)   
  $string.=chr(hexdec($hex[$i].$hex[$i+1]));   
  return   $string;   
}
function strToHex($string)   
{
  return substr(chunk_split(bin2hex($string)),0,-2);

}
function rc4a($string,$key)//加密
{    
  return strToHex(rc4($key,$string));    
}
function rc4b($string,$key)//rc4b解密 
{  
  return  @rc4($key,pack('H*',$string));
}
