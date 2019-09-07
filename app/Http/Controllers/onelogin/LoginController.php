<?php

namespace App\Http\Controllers\onelogin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class LoginController extends Controller
{
    //单点登录
    public function logindo(Request $request){
        $username=$request->username;
        $pass=$request->pass;
        $nameInfo=DB::table('pc_login')->where(['name'=>$username])->first();
        if(empty($nameInfo)){
            echo "<script>alert('没有此用户');</script>";
        }else{
            //生成token
            $token=md5(time().mt_rand(11111,99999));
            //将用户信息存入session中
            session(['u_id'=>$nameInfo->pc_id,'token'=>$token]);
            //按规则定义token名
            $token_name='onelogin:token_name:'.$nameInfo->pc_id;
            Redis::set($token_name,$token,30);
            header("refresh:2,url='/onelogin/index'");
            echo "登录成功->前往首页";
        }
    }
    //首页
    public function loginindex(){
        return view('onelogin.index');
    }







}
