<?php
session_start();
if(isset($_GET["retry"])){
    $wrong='<div class="inputbox">
                <span style="color:#df3a01;font-size:10px;margin:10px;display:block">用户名或密码错误</span>
            </div>';
}
if (!$_SESSION['login']==true) {
    print <<<END

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, height=device-height, inital-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <link rel="stylesheet" href="static/login.css" type="text/css" media="all" />
        <title>Login Page - Project Log System</title>
    </head>
    
    <body>
        <div class="loginbox">
            <div class="title">
            <span>
                Project Log System
            </span>
            </div>
            <div class="subtitle">
                用户登录
            </div>
            <form action="./login.php" method="post">
            <div class="inputbox">
                <span>帐号</span>
                <input name="user" required type="text">
            </div>
            <div class="inputbox">
                <span>密码</span>
                <input name="pass" type="password">
            </div>
            <div class="submitbox">
                <input name="submit" type="submit" value="提交">
            </div>
            $wrong
            </form>
        </div>
        <div class="footer">
        Opensource based on MIT licence.
        </div>
    </body>
</html>


END;

exit();
}
else{
    if(isset($_SESSION["admin"])){
        header ("HTTP/1.1 302 Moved Temporatily"); 
        header ("Location: "."./admin/"); 
        exit();
    }
    else{
        header ("HTTP/1.1 302 Moved Temporatily"); 
        header ("Location: "."./user/"); 
        exit();
    }
    
}

?>