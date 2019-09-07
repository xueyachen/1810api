<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;//引用Guzzle
class CurlController extends Controller
{
    public function curl_get()
    {
        //访问百度
        $url = "http://www.baidu.com";
        //1.初始化
        $ch = curl_init($url);
        //2.设置参数    TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//0是false  1是true    用于控制浏览器输出  get用false
        //3.执行会话
        curl_exec($ch);
        //4.关闭会话
        curl_close($ch);
    }

    //获取token
    public function curl_token()
    {
        $access_token = cache('access_token');
        if (!empty($access_token)) {
            echo 1;
            return $access_token;
        } else {
            $appid = 'wx69b81371703b53cb';
            $secret = '7943e9fb73c9e07ffb4ca1b9c279f634';
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
            //1.初始化
            $ch = curl_init($url);
            //2.设置参数
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //3.执行会话
            $data = curl_exec($ch);
            //4.关闭会话
            curl_close($ch);
            //5.处理数据
            $data = json_decode($data, true);
            cache(['access_token' => $data['access_token']],60 * 60 * 24 * 1);
            $access_token = cache('access_token');
            echo 2;
        }
    }

    public function curl3()
    {
//        echo 1;die;
//        print_r($_POST);

    }

    //表单测试
    public function form1()
    {
        return view('form.form1');
    }


    //自定义菜单
    public function menu()
    {
        $access_token = $this->curl_token();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
        //组装data数据
        $post_data = [
            "button"=>[
                [
                    "type"=>"view",
                    "name"=>"菜单",
                    "url"=>"https://www.baidu.com/"
                ]
            ]
        ];
        //将数组转换成json
        $post_data=json_encode($post_data,JSON_UNESCAPED_UNICODE);

        //初始化
        $ch=curl_init($url);
        //设置参数
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,false);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
        //执行会话
        $data=curl_exec($ch);
        //检测错误
        $errno=curl_errno($ch);
        $errmsg=curl_error($ch);

        //关闭会话
        curl_close($ch);

        dd($data);
    }


    //上传文件 视图
    public function upload(){
        return view('form.upload');
    }
    //上传执行
    public function uploaddo(Request $request,$name){
        if ($request->file($name)->isValid()) {
            $photo = $request->file($name);
//            $extension = $request->$name->extension();
            $Extension=$request->image->getClientOriginalExtension();  //获取未处理的上传文件后缀
            $store_result = $photo->storeAs(date('Ymd'),date('YmdHis').rand(10,99).'.'.$Extension);
            return $store_result;
        }
        exit('未获取到上传文件或上传过程出错');
    }
    //curl处理
    public function file(Request $request){
        if($request->hasFile('image')){
            $path=$this->uploaddo($request,'image');
        }

//        print_r(public_path()."/".$path);die;
//        $img="public/image/465ca76366bf2833f9aad0e8521637ad.jpg";
        $img=public_path()."/".$path;
        $url="http://www.1810lumen.com/upload";
        //1初始化
        $ch = curl_init();
        $post_data = array(
            'a'=>'Post',
            'c'=>'Api_Review',
            'file' => $img
        );
        //2设置参数
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_BINARYTRANSFER,true);//设为 TRUE ，将在启用 CURLOPT_RETURNTRANSFER 时，返回原生的（Raw）输出
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post_data);//数据
        curl_setopt($ch, CURLOPT_URL,$url);
        //3执行会话
        $info= curl_exec($ch);


        //检测错误
        $errno=curl_errno($ch);
        $errmsg=curl_error($ch);
//        var_dump($errno);
//        var_dump($errmsg);

        //4结束会话
        curl_close($ch);
//        print_r($info);

    }

    //测试
    public function encryption(){
        $a='qwertyuiop';
        $b=base64_encode(serialize($a));
        $url="http://www.1810lumen.com/encryption";

        //使用Guzzle传值
        $clinet = new Client();
        $response = $clinet ->request("POST",$url,[
            'body'=>$b
        ]);
        echo $response->getBody();




        //初始化
//        $ch=curl_init($url);
//        //设置参数
//        curl_setopt($ch,CURLOPT_RETURNTRANSFER,false);
//        curl_setopt($ch,CURLOPT_POST,true);
//        curl_setopt($ch,CURLOPT_POSTFIELDS,$b);
//        //执行会话
//        curl_exec($ch);
//        //检测错误
//        $errno=curl_errno($ch);
//        $errmsg=curl_error($ch);
//
//        //关闭会话
//        curl_close($ch);

    }

    //对称加密
    public function symm(){
        $str="xueyachen";               //待加密的数据
        $key="123";                //加密秘钥
        $iv="qqqqqqqqqqqqqqqq";         //初始向量(最少支持16位)
        $data=base64_encode(openssl_encrypt($str,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv));
        //                                  加密数据  method    秘钥  options          初始向量
        $url="http://www.1810lumen.com/symm";
        //使用Guzzle传值
        $clinet = new Client();
        $response = $clinet ->request("POST",$url,[
            'body'=>$data
        ]);
        echo $response->getBody();
    }

    //非对称加密
    public function asymm(){
        $data="qwertyuiop";
        //获取私钥
        $a=openssl_get_privatekey("file://".storage_path('rsa_private_key.pem'));
        //将原数据进行私钥加密  赋给 $cc
        openssl_sign($data,$cc,$a);
        $b=base64_encode($cc);
        $url="http://www.1810lumen.com/asymm?url=".urlencode($b);
        //使用Guzzle传值
        $clinet = new Client();
        $response = $clinet ->request("POST",$url,[
            'body'=>$data
        ]);
        echo $response->getBody();
    }

    //非对称加密2
    public function asymm2(){
        $data=[
            'name'=>'周红豹',
            'sex'=>'女'
        ];
        $data=json_encode($data,JSON_UNESCAPED_UNICODE);
        //获取私钥
        $private=openssl_get_privatekey("file://".storage_path('rsa_private_key.pem'));
        $url="http://www.1810lumen.com/asymm2";
        openssl_private_encrypt($data,$crypted,$private);
//        $crypted=base64_encode($crypted);
        //使用Guzzle传值
        $clinet = new Client();
        $response = $clinet ->request("POST",$url,[
            'body'=>$crypted
        ]);
        echo $response->getBody();
    }

    //将加密后的 数据与签名 发送给服务端
    public function task(Request $request){
            $data=[
                'name'=>'zhaotai',
                'sex'=>'nan',
                'age'=>19
            ];
            echo '原数据====>';
            print_r($data);
            echo '<hr>';
            $data=json_encode($data);
            $method='AES-128-CBC';
            $key='123456';
            $options=OPENSSL_RAW_DATA;
            $iv='adminadminadmin1';
            //使用对称加密数据
            $endata=base64_encode(openssl_encrypt($data,$method,$key,$options,$iv));
            echo '客户端将对称加密数据=>'.$endata.'发送给服务端'.'<hr>';
            //获取私钥
            $priva=openssl_get_privatekey("file://".storage_path('priva.pem'));
            //通过私钥生成签名
            openssl_sign($endata,$signature,$priva);
            echo '客户端将签名====>'.$signature.'发送给服务端'.'<hr>';
            $url="http://www.1810lumen.com/task?url=".urlencode($signature);
            //使用Guzzle传值
            $clinet = new Client();
            $response = $clinet ->request("POST",$url,[
                'body'=>$endata
            ]);
            echo $response->getBody();
    }
    //接受服务端发送过来的 数据与签名
    public function aa(){
        //接受签名
        $auto=$_GET['url'];
        //接受对称加密的数据
        $endata=file_get_contents('php://input');
        //获取公钥
        $pub=openssl_pkey_get_public("file://".storage_path('pub.key'));
        //验证签名
        $result=openssl_verify($endata,$auto,$pub);
        if($result==1){     //验证签名 并 解密对称加密 的数据
            echo '客户端验签成功☺'.'<hr>';
            $method='AES-128-CBC';
            $key='123456';
            $options=OPENSSL_RAW_DATA;
            $iv='adminadminadmin1';
            $dedata=json_decode(openssl_decrypt(base64_decode($endata),$method,$key,$options,$iv),true);
            echo '客户端解密  对称加密的数据↓↓↓↓↓';
            print_r($dedata);
        }
    }

    //支付宝手机端支付
    public function alipay(){
        //biz_content请求参数
        $biz_content=[
            'subject'       =>'测试订单'.mt_rand(11111,99999).time(),
            'out_trade_no' =>'1810'.mt_rand(11111,99999).time(),
            'total_amount' =>mt_rand(1,10),
            'charset'   =>'utf-8',
            'product_code' =>'QUICK_WAP_WAY'
        ];
        //数据
        $data=[
            'app_id'    =>'2016092500595837',
            'method'    =>'alipay.trade.wap.pay',
            'sign_type' =>'RSA2',
            'timestamp' =>date('Y-m-d H:i:s',time()),
            'version'   =>'1.0',
            'biz_content'=>json_encode($biz_content,JSON_UNESCAPED_UNICODE)
        ];
        // key 排序
        ksort($data);
        //拼接数据
        $str='';
        foreach($data as $k=>$v){
            $str.=$k.'='.$v.'&';
        }
        //取出拼接数据最后的&
        $strl=rtrim($str,'&');
        //获取私钥
        $priva=openssl_get_privatekey(file_get_contents("file://".storage_path('priva.pem')));
        //通过私钥生成签名
        openssl_sign($strl,$sign,$priva,OPENSSL_ALGO_SHA256);
        //在进行base64加密
        $ensign=base64_encode($sign);
        //将生成的签名放入数组中
        $data['sign']=$ensign;
        //urlencode
        $param_str='?';
        foreach($data as $k=>$v){
            $param_str.=$k.'='.urlencode($v).'&';
        }
        //请求地址
        $ali_gateway="https://openapi.alipaydev.com/gateway.do";
        $param_str=rtrim($param_str,'&');
        $url=$ali_gateway.$param_str;
        header("location:".$url);
    }


}
