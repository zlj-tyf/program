<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/fun.css">
    <title>学生管理 >> 新增学生</title>
</head>
<body>
<h3 class="subtitle">学生管理 >> 新增学生</h3>
<form action="./fun/addStudent.php" method="post" target="resultbox">
    <div class="inputbox"><span>姓名：</span><input name="name" required type="text"></div>
    <div class="inputbox"><span>卡片类型：(1/2)</span><input name="card_type" required type="text"></div>
    
    <br>
    <div class="clickbox clearfloat"><span></span><input name="submit" type="submit" value="提交"></div>
    <div class="redbox clickbox "><span></span><input name="reset" type="reset" value="清除"></div>
    <p>注：两个字段均必填！</p>
</form>

<iframe name="resultbox" frameborder="0" width="100%" height="200px"></iframe>
</body>
</html>
