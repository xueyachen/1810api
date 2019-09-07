<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <form action="/regdo" method="post" enctype='multipart/form-data'>
        用户名：<input type="text" name="username"><br>
        密码： <input type="text" name="pass"><br>
        邮箱： <input type="text" name="email"><br>
        手机号：<input type="text" name="tel"><br>
        身份证：<input type="file" name="card"><br>
        <input type="submit" value="注册">
    </form>
</body>
</html>