<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="./css/fun.css">
    <title>课程管理 >> 新增申报项目</title>
</head>
<body>
<h3 class="subtitle">课程管理 >> 新增申报项目</h3>

<form action="./fun/addCourse.php" method="post" target="resultbox">
    <div class="inputbox"><span>比赛名称：</span><input name="competition_name" required type="text"></div>
    <div class="inputbox"><span>比赛简称：</span><input name="competition_short_name" type="text" placeholder="可选"></div>
    <div class="inputbox"><span>比赛级别：</span><input name="competition_level" required type="text"></div>
    <div class="inputbox"><span>申报时间：</span><input name="submit_time" required type="text"></div>
    <div class="inputbox" style="width: 100%;"><span>申报要求：</span><br>
        <textarea name="submit_requirements" rows="5" style="width: 90%;" required></textarea>
    </div>
    <div class="inputbox" style="width: 100%;"><span>学生需提交材料：</span><br>
        <textarea name="student_requirements" rows="5" style="width: 90%;" required></textarea>
    </div>
    <div class="inputbox"><span>卡种类要求：</span>
        <input name="card_requirement" type="number" min="0" step="1" required>
    </div>

    <br>
    <div class="clickbox clearfloat"><span></span><input name="submit" type="submit" value="提交"></div>
    <div class="redbox clickbox"><span></span><input name="reset" type="reset" value="清除"></div>
</form>

<iframe name="resultbox" frameborder="0" width="100%" height="100px"></iframe>
</body>
</html>
