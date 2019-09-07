<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
class OneToken
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
        $token_name='onelogin:token_name:'.$u_id;    //按规则定义token名
        Redis::expire($token_name,30);
        return $next($request);
    }
}
