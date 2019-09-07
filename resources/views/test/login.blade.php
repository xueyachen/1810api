用户名：<input type="text" name="username"><br>
密码：<input type="text" name="pass"><br>
<input type="button" value="登录" id="but">
<script src="/js/jquery-3.2.1.min.js"></script>
<script>
    $(function(){
        $('#but').click(function(){
            var username=$("input[name='username']").val();
            var pass=$("input[name='pass']").val();
            $.post(
                    "/test/logindo",
                    {username:username,pass:pass},
                    function(res){
//                        console.log(res);
                        if(res.code==001){
                            alert(res.msg);
                        }else if(res.code==1){
                            alert(res.msg);

                        }else if(res.code==002){
                            alert(res.msg);
                        }
                    }
                    ,'json'
            );
        })
    })
</script>