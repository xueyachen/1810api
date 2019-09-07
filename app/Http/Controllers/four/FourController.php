<?php

namespace App\Http\Controllers\four;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class FourController extends Controller
{
    //4月份 月考A技能
    //申请提交
    public function fourdo(Request $request){
        $username = $request->input('username');
        $res=DB::table('four_login')->where(['username'=>$username])->first();
        if($res){
            //处理上传的图片
            $file=$request->file('img');
            if($file==''){      //判断用户是否上传 身份证
                echo "<script>alert('请选择上传身份证照片');location.href='/four';</script>";exit;
            }else{
                $f_name=date('Ymd').mt_rand(1,9);//存放图片的文件名
                $img_name=time().mt_rand(11111,99999);//图片名
                $ext=$file->getClientOriginalExtension();//图片的后缀名
                $ImgName=$img_name.'.'.$ext;
                $file->storeAs($f_name,$ImgName);
                //生成 app_key 和 app_secret
                $app_key=date('Ymd').mt_rand(111,999);
                $app_secret='value'.mt_rand(111,999);
                //将 key 和 secret 进行对称加密
                $key='123';
                $iv='qwertyuiopasdfgh';
                $app_key_en=openssl_encrypt($app_key,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);
                $app_secret_en=openssl_encrypt($app_secret,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);
                Redis::set('key_name',$app_key_en);
                Redis::expire('key_name',300);
                Redis::set('secret_name',$app_secret_en);
                Redis::expire('secret_name',300);
                //申请 将用户id 存入redis中
                $token_id='token_id:'.$res->four_id;
                Redis::set($token_id,$res->four_id);
                $url="/four/index?four_id=".$res->four_id;
                echo '申请审核成功';
                header("location:".$url);
            }
        }else{
            echo "<script>alert('申请审核失败-->您可以重新审核');location.href='/four';</script>";
        }
    }
    //个人中心
    public function index(){
        //获取redis中 使用对称加密的数据
        $app_key_en=Redis::get('key_name');
        $app_secret_en=Redis::get('secret_name');
        //判断过期
        if($app_key_en==''&&$app_secret_en==''){
            echo "<script>alert('您的申请已过期->前往重新申请');location.href='/four';</script>";exit;
        }
        //解密 对称加密的数据
        $key='123';
        $iv='qwertyuiopasdfgh';
        $app_key_de=openssl_decrypt($app_key_en,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);
        $app_secret_de=openssl_decrypt($app_secret_en,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);
        return view('four.index',compact('app_key_de','app_secret_de'));
    }

    //4月份 月考B技能
    //pc端登陆执行
    public function pcdo(Request $request){
        $name=$request->name;
        $pass=$request->pass;
        $token=$this->token();      //获取 token
        $res=DB::table('pc_login')->where(['name'=>$name])->update(['type_status'=>1,'add_time'=>time()]);
        if($res){
            $pc_id=DB::table('pc_login')->orderBy('pc_id','desc')->value('pc_id');
            $tokenInfo=DB::table('token')->insert(['token'=>$token,'pc_id'=>$pc_id,'add_time'=>time()]);
            if($tokenInfo){
                setcookie('pc_id',$pc_id);
                setcookie('pc_token',$token);
                echo "<script>alert('登陆成功');location.href='/pc/index';</script>";
            }
        }
    }
    //app端登陆执行
    public function appdo(Request $request){
        $name=$request->name;
        $pass=$request->pass;
        $token=$this->token();      //获取 token
        $res=DB::table('pc_login')->where(['name'=>$name])->update(['type_status'=>2,'add_time'=>time()]);
        if($res){
            $pc_id=DB::table('pc_login')->orderBy('pc_id','desc')->value('pc_id');
            $tokenInfo=DB::table('token')->insert(['token'=>$token,'pc_id'=>$pc_id,'add_time'=>time()]);
            if($tokenInfo){
                setcookie('pc_id',$pc_id);
                setcookie('pc_token',$token);
                echo "<script>alert('登陆成功');location.href='/pc/index';</script>";
            }
        }
    }
    //个人中心
    public function pcindex(){
        $pc_id=$_COOKIE['pc_id'];
        $token=DB::table('token')->where(['pc_id'=>$pc_id])->orderBy('token_id','desc')->value('token');   //获取数据库中的 token
        $redis_token=$_COOKIE['pc_token'];
        //判断token是否相等   不相等 证明账号在别处登录
        if($token!=$redis_token){
            echo "<script>alert('您的账号已在别的地方登陆');location.href='/pc';</script>";exit;
        }
        //判断用户长时间未操作 强制下线
        $add_time=DB::table('token')->where(['pc_id'=>$pc_id])->orderBy('token_id','desc')->value('add_time');
        if(time()-$add_time >10){
            echo "<script>alert('由于您长时间未操作，系统强制下线');location.href='/pc';</script>";exit;
        }
        //查看账户状态
        $stype_status=DB::table('pc_login')->where('pc_id',$pc_id)->value('type_status');
        if($stype_status==1){
            return $status='pc端 在线';
        }else{
            return $status='app端 在线';
        }


        return view('pc.pcindex',compact('status'));
    }
    //生成token
    public function token(){
        $str='qwertyuiopasdfghjklzxcvbnm';
        $token=substr(str_shuffle($str.mt_rand(1111,9999)),6,10);
        return $token;
    }



    //验签
    public function sign(){
        $order_key='1810key';
        $order_id=time().mt_rand(111,999);
        $data=[
            'order_id'      =>$order_id,
            'order_name'    =>'测试订单'.$order_id,
            'add_time'      =>time(),
            'year'          =>date('Y'),
            'month'         =>date('md')
        ];
        //1. 排序
        ksort($data);
        //2. 拼装数据
        $str='';
        foreach($data as $k=>$v){
            $str.=$k.'='.$v.'&';
        }
        //将数据组成的 字符串 和 key连接在一起进行MD5加密 并变成大写 形成签名
        $string=$str.'key='.$order_key;
        $sign=strtoupper(md5($string));
        $data['sign']=$sign;
        $strr='';
        foreach($data as $k=>$v){
            $strr.=$k.'='.urlencode($v).'&';
        }
        $strr=rtrim($strr,'&');
        $url="http://www.1810lumen.com/sign?".$strr;

        echo $url;
        //CURL发送数据到  lumen框架
        //1.初始化
//        $ch = curl_init($url);
//        //2.设置参数    TRUE 将curl_exec()获取的信息以字符串返回，而不是直接输出
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);//0是false  1是true    用于控制浏览器输出  get用false
//        //3.执行会话
//        curl_exec($ch);
//        //4.关闭会话
//        curl_close($ch);

    }














}
