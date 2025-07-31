<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/fun.css">
</head>
<body>
    <h3>信息更新</h3>
<form style="margin:30px;"action="./addLogFunc.php" method="post" id="log">

    <span>类型：</span><input name="type" required type="radio" value="1" >更新项目<input name="type" type="radio" value="0">新建项目<br>
    <!-- <span>时间：</span><input name="logdate" required type="date" style="width:180px"><br> -->
    <span>时间：</span>
<input name="logdate" required type="date" style="width:180px" id="logdate"><br>

<script>
// 获取当前日期（格式：YYYY-MM-DD）
const today = new Date().toISOString().split('T')[0];
// 设置默认值为今天
document.getElementById('logdate').value = today;
</script>
    <span>缘由：</span><input name="reason"required  type="text" class="boxwidth">
    <br>
    <span>详情：<br/>请引用前端页面链接</span><br><textarea style="display:block;width:90%;height:60px;"name="detail" required form="log"></textarea><br>
    <span></span><input name="submit" type="submit" value="提交"><br>
</form>
</body>
</html>