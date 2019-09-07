<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
class TestController extends Controller
{
    //解密客户端发过来的数据reg
    public function regg(){
        $endata=$_GET['url'];         //接受到的签名
        $data=file_get_contents("php://input");//原始数据
        //获取公钥
        $pub=openssl_pkey_get_public("file://".storage_path('test_public.pem'));
        //先验证签名
        $result=openssl_verify($data,$endata,$pub);
        if($result==1){
            echo '验证签名成功';
            //先将堆成加密解密
            $key='123';
            $iv='qwertyuiopasdfgh';
            $dedata=openssl_decrypt($data,'AES-128-CBC',$key, OPENSSL_RAW_DATA,$iv);
            $data=json_decode($dedata,true);
            //判断数据库中  是否已经注册
            $rs=DB::table('test_login')->where(['username'=>$data['username']])->first();
            if(empty($rs)){
                $data['pass']=password_hash($data['pass'],PASSWORD_BCRYPT);
                unset($data['code']);
                $res=DB::table('test_login')->insert($data);
                if($res){
                   echo "<script>alert('注册成功->正在前往登录页面');</script>";exit;
                }else{
                    echo "<script>alert('注册失败->请稍后重试');</script>";exit;
                }
            }else{
                echo "<script>alert('该账户已注册');</script>";exit;
            }
        }




    }
    //解密客户端发过来的数据login
    public function login(){
        $endata=file_get_contents("php://input");        //加密数据
        $signature=$_GET['url'];                          //签名
        //获取公钥
        $pub=openssl_pkey_get_public("file://".storage_path('test_public.pem'));
        //先验证签名
        $result=openssl_verify($endata,$signature,$pub);
        if($result==1){
            //在解密  对称加密的数据
            $key='123';
            $iv='qwertyuiopasdfgh';
            $dedata=openssl_decrypt($endata,'AES-128-CBC',$key, OPENSSL_RAW_DATA,$iv);
            $data=json_decode($dedata,true);
            $nameInfo=DB::table('test_login')->where(['username'=>$data['username']])->first();
            if(!$nameInfo==''){
                if(password_verify($data['pass'],$nameInfo->pass)){
                    $arr=[
                        'u_id'=>$nameInfo->test_id,
                        'msg'=>"登陆成功",
                        'code'=>1
                    ];
                    return json_encode($arr);
                }else{
                    echo "<script>alert('账号或密码有误');</script>";exit;
                }
            }
        }







    }
    //测试setcookie发送到shopping
    public function test(){
        $str='qwe';
        setcookie('test',$str,time()+3600,'/','1810api.com',false,true);
        echo 1;
    }



    //6月月考B卷 客户端->服务端(passport)
    public function category(Request $request){
        $catetype=$request->catetype;
        $is_show_level=$request->is_show_level;
        $arr=[
           'catetype' =>$catetype,
            'is_show_level'=>$is_show_level,
        ];
        //加密  验签
        $data=$this->encrypt($arr);
        $data=http_build_query($data);        //生成 URL-encode 之后的请求字符串
        $url="http://www.1810passport.com/category";
        $sh=curl_init();
        $a=[
            CURLOPT_URL=>$url,
            CURLOPT_POSTFIELDS=>$data,
            CURLOPT_RETURNTRANSFER=>true
        ];
        curl_setopt_array($sh,$a);  //为 cURL 传输会话批量设置选项
        $result=curl_exec($sh);
        print_r($result);
        curl_close($sh);

    }
    //对称加密  生成签名
    public function encrypt($arr){
        //对称加密
        $encrypt=base64_encode(openssl_encrypt(json_encode($arr),'AES-128-CBC',env('KEY'),OPENSSL_RAW_DATA,env('IV')));
        //生成签名
        openssl_sign($encrypt,$signature,openssl_get_privatekey("file://".storage_path('test_private.pem')));
        $signature=base64_encode($signature);
        //base64之后压入数组中
        $data=[
            'encrypt'=>$encrypt,
            'signature'=>$signature
        ];
        return $data;
    }



    public function cate(){
        $cateInfo=DB::table('test_cate')->get();
        $cateInfo=json_decode($cateInfo,true);
        $Info=$this->cateInfo($cateInfo);
        $Info=$this->SonInfo($Info,2);
    }
    //获取所有的层级数据
    public function cateInfo($cateInfo,$pid=0,$level=1){
        static $arr=[];
        foreach ($cateInfo as $k=>$v){
            if($v['pid']==$pid){
                $v['leve']=$level;
                $arr[]=$v;
                $this->cateInfo($cateInfo,$v['cate_id'],$level+1);
            }
        }
        return $arr;
    }
    //获取子级数据
    public function SonInfo($Info,$a,$pid=0,$level=1){
        $arr=[];
        foreach($Info as $k=>$v){
            if($v['pid']==$pid){
                if($level<=$a){
                    $v['son']=$this->SonInfo($Info,$a,$v['cate_id'],$level+1);
                    $arr[]=$v;
                }
            }
        }
        return $arr;
    }




    


}
