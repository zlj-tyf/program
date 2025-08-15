<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/fun.css">
    <title>学生日志查询</title>
</head>
<body>
<form action="./fun/getLog.php" method="get" target="resultbox">
    <h3 class="subtitle">学生管理 >> 管理</h3>
    <p> 如需修改学生页面，请先<a target=blank href="/wp-login.php?redirect=/program/admin/queryLog.php">登录</a></p>
    <div class="inputbox"><span>学号：</span><input name="sid" type="text"></div>
    <div class="inputbox"><span>姓名：</span><input name="name" type="text"></div>
    <div class="clickbox clearfloat">
        <input name="submit" type="submit" value="查询">
    </div>
    <!-- <div class="clickbox">
        <a href="./addLog.php">新增</a>
    </div> -->
</form>

<iframe name="resultbox" frameborder="0" width="100%" height="690px"></iframe>
</body>
</html>
