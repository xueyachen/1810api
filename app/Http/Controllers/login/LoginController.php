<?php

namespace App\Http\Controllers\login;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\model\w_login;
class LoginController extends Controller
{
    //6.25完成一个天气查询功能的api服务
    //注册
    public function reg(){
        return view('login.reg');
    }
    //注册执行
    public function regdo(Request $request){
        $data=request()->all();
        //hasFile判断文件在请求中是否存在
        if($request->hasFile('card')){
            $data['card']=$this->upload($request,'card');
        }
        $data['pass']=password_hash($data['pass'],PASSWORD_BCRYPT);
        //邮箱唯一
        $emailInfo=DB::table('w_login')->where(['email'=>$data['email']])->first();
        if(!empty($emailInfo)){
            echo "<script>alert('该邮箱以注册');location.href='/reg';</script>";exit;
        }
        //用户名唯一
        $usernameInfo=DB::table('w_login')->where(['username'=>$data['username']])->first();
        if(!empty($usernameInfo)){
            echo "<script>alert('该用户名以注册');location.href='/reg';</script>";exit;
        }
        //手机号唯一
        $telInfo=DB::table('w_login')->where(['tel'=>$data['tel']])->first();
        if(!empty($telInfo)){
            echo "<script>alert('该手机号以注册');location.href='/reg';</script>";exit;
        }
        //入库
        $res=DB::table('w_login')->insert($data);
        if($res){
            //注册成功  生成APPID和APPSecret
            $appid='xyc'.time().mt_rand(111,999);
            $appsecret=base64_encode(date('Y-m-d H:i:s').mt_rand(111,999));
            DB::table('w_login')->where(['username'=>$data['username']])->update(['appid'=>$appid,'appsecret'=>$appsecret]);
            echo "<script>alert('注册成功-->正在前往登录页面');location.href='/weather/login';</script>";
        }else{
            echo "<script>alert('注册失败');location.href='/weather/login';</script>";
        }
    }

    //登录执行
    public function logindo(){
        $username=request()->username;
        $pass=request()->pass;
        $res=w_login::where(['username'=>$username])->Orwhere(['email'=>$username])->Orwhere(['tel'=>$username])->first();
        if($res){     //存在
            if(password_verify($pass,$res->pass)){   //判断密码
                Redis::set('w_u_id',$res->id,60*60*24*3);
                $arr=[
                    'code'=>003,
                    'font'=>'登陆成功-->正在前往个人中心页面'
                ];
            }else{
                $arr=[
                    'code'=>002,
                    'font'=>'用户名或密码有误'
                ];
            }
        }else{                                      //不存在
            $arr=[
                'code'=>001,
                'font'=>'该用户名|邮箱|手机号不存在'
            ];
        }
        return json_encode($arr);
    }
    //个人中心
    public function index(){
        $id=Redis::get('w_u_id');
        $res=DB::table('w_login')->where(['id'=>$id])->first();
        return view('index.index',compact('res'));
    }
    //用户通过 appid 和 appsecret 生成token
    public function token(){
        $appid=request()->appid;
        $appsecret=request()->appsecret;
        $u_id=Redis::get('w_u_id');     //获取用户id
        $token_key='token:'.$u_id;      //拼装token 名
        $redis_token=Redis::get($token_key);
        if(empty($redis_token)){
            $token='http://www.xueyachen.com?appid='.$appid.'&?appsecret='.$appsecret.'&?rand='.rand(111111,999999);
            Redis::set($token_key,$token);
            Redis::expire($token_key,30);
        }
        return $redis_token;
    }
    //用户通过token 调用  天气接口
    public function weather(Request $request){
        $token=$request->token;
        $u_id=Redis::get('w_u_id');     //获取用户id
        $token_key='token:'.$u_id;      //拼装token 名
        $redis_token=Redis::get($token_key);
        if($token!=$redis_token){
            echo 'token有误';
        }else{
            //        获取查询天气的城市
            $city=request()->input('city');
//        调用天气接口   K780
            $url="http://api.k780.com:88/?app=weather.future&weaid={$city}&&appkey=10003&sign=b59bc3ef6191eb9f747dd4e83c99f2a4&format=json&?access_token=".$token;
//        get请求
            $data=file_get_contents($url);
//        对象转数组
            $ppl=json_decode($data ,true);
//        如果success为0
            if($ppl['success']==0){
                var_dump('请输入要查询天气的城市');
//            success不为0
            }else{
//            定义一个空的变量
                $msg='';
//            foreach get请求返回回来的数组
                foreach($ppl['result'] as $k=>$v){
//                想要的数据 拼接
                    $msg.='日期：'.$v['days'].'，星期：'.$v['week'].'，城市：'.$v['citynm'].'，当日温度区间：'.$v['temperature'].'，天气：'.$v['weather'].'，风向：'.$v['wind'].'，风力:'.$v['winp']."<br>";
                }
                return $msg;
            }
        }
    }

    //测试上传文件
    public function file(Request $request){
        $file=$request->file('img');             //接受上传的文件
        $save_path=date('Ymd');                 //图片存放的文件名
        $img_name=time().mt_rand(1,999);       //图片的名字
        $ext=$file->getClientOriginalExtension();//图片的后缀名
        $f_name=$img_name.'.'.$ext;             //拼接图片名称
        $file->storeAs($save_path,$f_name);
    }






    //上传执行
    public function upload(Request $request,$name){
        if ($request->file($name)->isValid()) {
            $photo = $request->file($name);
//            $extension = $request->$name->extension();
            $Extension=$request->card->getClientOriginalExtension();  //获取未处理的上传文件后缀
            $store_result = $photo->storeAs(date('Ymd'),date('YmdHis').rand(10,99).'.'.$Extension);
            return $store_result;
        }
        exit('未获取到上传文件或上传过程出错');
    }


}
