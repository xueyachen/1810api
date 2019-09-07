<?php
$data = explode('?',$_REQUEST['swfurl']);
//file_put_contents(__DIR__ .'/publish.log',print_r($data,true),8);die;
$str = implode('&',$data);
$login_data = explode('&',$str);

$user_name = explode('=',$login_data[1]??[])[1]??null;
$pwd = explode('=',$login_data[2]??[])[1]??null;
//var_dump($pwd);die;
if(empty($user_name) || empty($pwd)){
  header("HTTP/1.0 404 Not Found");die;
}
$mysql = mysqli_connect('192.168.254.1','root','root','1810shop');
//var_dump($mysql);die;
//#验证用户信息
$sql = 'select * from user where username="'.$user_name.'" and pass="'.$pwd.'"';
$res = mysqli_query($mysql,$sql);
//var_dump($res);die;
$data = mysqli_fetch_assoc($res);
//var_dump($data);die;

if(empty($data)){
  header("HTTP/1.0 404 Not Found");die;
}





//if($name == 'test')
//{
//  header('HTTP/1.0 200 ok');
//}else{
//  header('HTTP/1.0 404 Not Found');
//}


?>
