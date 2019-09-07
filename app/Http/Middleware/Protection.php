<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
class Protection
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
        //接受用户 id
        $four_id=$_GET['four_id'];
        $token_id='token_id:'.$four_id;
        $num=Redis::incr($token_id);
        if($num>=5){
            $ttl=Redis::ttl($token_id);
            if($ttl=='-1'){
                Redis::expire($token_id,30);
                echo "<script>alert('many request');location.href='/four';</script>";exit;
            }
        }
        return $next($request);
    }
}
