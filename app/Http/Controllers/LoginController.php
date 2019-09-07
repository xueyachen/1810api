<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\model\Login;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;//引用Guzzle
class LoginController extends Controller
{
    //接受数据   将数据传给 lumen来处理
    public function user(){
        $name=Request()->name;
        $pass=Request()->pass;
        $url="http://www.1810lumen.com/logindo";
        //使用Guzzle传值
        $clinet = new Client();
        $response = $clinet ->request("POST",$url,[
            'form_params'=>[
                'username'=>$name,
                'pass'=>$pass
            ]
        ]);
        echo $response->getBody();
    }
}
