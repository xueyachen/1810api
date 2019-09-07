
<h1 align="center">登录</h1>
请输入用户名|邮箱|手机号：<input type="text" id="name"><br>
请输入密码：&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="text" id="pass"><br>
<input type="button"  value="登录" class="but">
<script src="{{asset('/js/jquery-3.2.1.min.js')}}"></script>
<script>
    $(function(){
        $('.but').click(function(){
            var username=$('#name').val();
            var pass=$('#pass').val();
            $.post(
                    "/weather/logindo",
                    {username:username,pass:pass},
                    function(res){
//                        console.log(res);
                        if(res.code==001){
                            alert(res.font);
                        }else if(res.code==002){
                            alert(res.font);
                        }else if(res.code==003){
                            alert(res.font);
                            location.href='/weather/index';
                        }
                    }
                    ,'json'
            )
        })
    })
</script>