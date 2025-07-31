<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>课程查询</title>
    <link rel="stylesheet" type="text/css" href="./css/fun.css">
</head>
<body>
    <div class="subtitle">
        <h3>课程查询</h3>
    </div>
    <form action="./fun/getCourse.php" method="get" target="resultbox">
        <div class="inputbox">
            <span>套餐级别</span>
            <input name="card_requirement" type="text" placeholder="模糊匹配 0 或 1">
        </div>
        <div class="clickbox clearfloat">
            <span></span><input name="submit" type="submit" value="提交">
        </div>
        <div class="redbox clickbox">
            <span></span><input name="reset" type="reset" value="清除">
        </div>
    </form>
    <iframe name="resultbox" frameborder="0" width="100%" height="550px"></iframe>
</body>
</html>
