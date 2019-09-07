<?php

    //第一种方式 form-data
    $post_data=[
        'username'=>'xyc',
        'pass'=>'123'
    ];
    //第二种方式 x-www-form-urlencoded
//    $post_data="name=xyc&pass=456";

    //第三种方式 raw
//    $post_data=[
//        'name'=>'lisi',
//        'age'=>11
//    ];
//    $post_data=json_encode($post_data);
    //1.初始化
    $url="http://www.1810lumen.com/form1";
    $ch=curl_init($url);

    //2.设置参数
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,false);
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);

    //3.执行会话发来的请求
    curl_exec($ch);

    //获取错误信息
    $errno=curl_errno($ch);
    $errmsg=curl_error($ch);

//    var_dump($errno);echo "<hr>";
//    var_dump($errmsg);

    //4.关闭会话
    curl_close($ch);
?>
