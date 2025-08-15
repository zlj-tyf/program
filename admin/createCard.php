<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>卡种管理 >> 新增卡种</title>
    <link rel="stylesheet" href="./css/fun_addcard.css">
     <style>

        </style>
        
</head>
<body>
<h3 class="subtitle">卡种管理 >> 新增卡种</h3>

<form action="./fun/addCard.php" method="post" target="resultbox">

    <div class="inputbox"><span>卡名称：</span><input name="name" required type="text"></div>
<div class="inputbox"><span>最大报名次数：</span><input name="max_courses" required type="number" min="1" step="1"></div>

<div class="inputbox">
    <span>允许报名课程：</span><br>
    <?php
    require_once("../config/database.php");
    $courses = mysqli_query($db, "SELECT cid, competition_name FROM course");
    while ($row = mysqli_fetch_assoc($courses)) {
        echo '<label style="display:block; margin-bottom:5px;">';
        echo '<input class="input-new" type="checkbox" name="allowed_courses[]" value="' . $row["cid"] . '"> ' . htmlspecialchars($row["competition_name"]);
        echo '</label>';
    }
    mysqli_close($db);
    ?>
</div>



    
    <div class="clickbox clearfloat"><input type="submit" name="submit" value="提交"></div>
    <div class="redbox clickbox"><input type="reset" value="清除"></div>

</form>

<iframe name="resultbox" frameborder="0" width="100%" height="120px"></iframe>

</body>
</html>
