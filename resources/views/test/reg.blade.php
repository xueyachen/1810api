用户名：<input type="text" name="username"><br>
手机号：<input type="text" name="tel"><input type="button" value='获取' id='api'><br>
验证码：<input type="text" name='code'><br>
密码：<input type="text" name='pass'><br>
<input type="button" value="注册" id='but'>
<script src="/js/jquery-3.2.1.min.js"></script>
<script>
    $(function(){
        //点击获取
        $("#api").click(function(){
            var tel=$("input[name='tel']").val();
            $.post(
                    "/test/message",
                    {tel:tel},
                    function(res){
                        // console.log(res);
                        if(res==3){
                            alert('请输入手机号');
                        }else if(res==1){
                            alert('发送成功');
                        }else if(res==2){
                            alert('发送失败');
                        }
                    }
                    ,'json'
            )
        })

        $('#but').click(function(){
            var username=$("input[name='username']").val();
            var tel=$("input[name='tel']").val();
            var code=$("input[name='code']").val();
            var pass=$("input[name='pass']").val();
            $.post(
                    "/test/reg",
                    {username:username,tel:tel,code:code,pass:pass},
                    function(res){
//                        console.log(res);
                         if(res.code==001){
                         	alert(res.msg);
                         }else if(res.code==1){
                             alert(res.msg);
                             location.href='/test/login';
                         }else if(res.code==2){
                             alert(res.msg);
                         }
                    }
                    ,'json'
            )
        })
    })
</script>