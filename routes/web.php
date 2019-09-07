<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::post('/user','LoginController@user');

//curl_get
Route::get('/curl_get','CurlController@curl_get');
//curl获取token
Route::get('/curl_token','CurlController@curl_token');
Route::post('/curl3','CurlController@curl3');

//表单测试
Route::get('/form1','CurlController@form1');

//自定义菜单
Route::get('/menu','CurlController@menu');

//上传文件
Route::get('/upload','CurlController@upload');
Route::get('/uploaddo','CurlController@uploaddo');
Route::post('/file','CurlController@file');


//测试
Route::get('/encry','CurlController@encryption');
//对称加密
Route::get('/symm','CurlController@symm');
//非对称加密
Route::get('/asymm','CurlController@asymm');
//非对称加密2
Route::get('/asymm2','CurlController@asymm2');
//将加密后的 数据与签名 发送给服务端
Route::get('/task','CurlController@task');
//接受服务端发送过来的 数据与签名
Route::post('/aa','CurlController@aa');


//支付宝手机端支付
Route::get('/alipay','CurlController@alipay');


//6.25完成一个天气查询功能的api服务
//注册
Route::get('/reg','login\LoginController@reg');
Route::post('/regdo','login\LoginController@regdo');     //注册执行
//登录
Route::get('/weather/login',function(){
    return view('login.login');
});
Route::post('/weather/logindo','login\LoginController@logindo');     //登录执行
Route::get('/weather/index','login\LoginController@index');         //个人中心
Route::post('/token','login\LoginController@token');                 //生成token
Route::post('/weather','login\LoginController@weather');                 //调用天气接口
Route::get('/file',function(){                                          //测试上传文件
    return view('file.file');
});
Route::post('/file','login\LoginController@file');                     //上传文件执行






//phpinfo
Route::get('/phpinfo', function () {
    phpinfo();
});

//4月份 月考A技能
Route::get('/four',function(){
    return view('four.four');
});
Route::post('/fourdo','four\FourController@fourdo');    //申请执行
Route::get('/four/index','four\FourController@index')->middleware('protection');    //个人中心

//4月份 月考B技能
//pc端 登陆
Route::get('/pc',function(){
    return view('pc.pclogin');
});
//app端登陆
Route::get('/app',function(){
    return view('pc.applogin');
});



Route::post('/pcdo','four\FourController@pcdo');//pc端执行
Route::post('/appdo','four\FourController@appdo');//app端执行



Route::get('/pc/index','four\FourController@pcindex');//个人中心

//验签
Route::get('/sign','four\FourController@sign');
//单点登录
Route::get('/onelogin',function(){
    return view('onelogin.login');
});
//登录执行
Route::post('/onelogin/logindo','onelogin\LoginController@logindo');

Route::group(['middleware' => 'web'],function($route){
    //首页
    $route->get('/onelogin/index','onelogin\LoginController@loginindex')->middleware('onetokenroute');
});


//6月月考
Route::post('/test/reg','TestController@regg');      //解密客户端发过来的数据
Route::post('/test/login','TestController@login');      //解密客户端发过来的数据
//测试setcookie发送到shopping
Route::get('/test','TestController@test');



//6月月考B卷 客户端->服务端(passport)
Route::get('/category','TestController@category');




Route::get('/cate','TestController@cate');



















