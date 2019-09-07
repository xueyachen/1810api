<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
class OneTokenroute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $u_id=session('u_id');              //获取session中的用户id
        $session_token=session('token');    //获取session中的token
        $token_name='onelogin:token_name:'.$u_id;    //按规则定义token名
        $redis_token=Redis::get($token_name);          //获取redis中的token
        //判断token是否一样
        if(Redis::ttl($token_name)=='-2'){
            header("refresh:2,url='/onelogin'");
            echo '由于您长时间未操作，您已下线';die;
        }else if($session_token==$redis_token){
//            header("refresh:2,url='/onelogin/index'");
        }else{
            header("refresh:2,url='/onelogin'");
            echo '您的账号已在其他地方登录->您已下线';die;
        }
        return $next($request);
    }
}
