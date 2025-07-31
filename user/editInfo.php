<?php
session_start();
$sid = $_SESSION["user"];
require_once("../config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $birth = $_POST["birth"];
    $email = $_POST["email"];
    $tel = $_POST["tel"];
    $current_grade = $_POST["current_grade"];
    $has_researcher = isset($_POST["has_researcher"]) ? 1 : 0;

    $sql = "UPDATE student SET
        birth='$birth',
        email='$email',
        tel='$tel',
        current_grade='$current_grade',
        has_researcher='$has_researcher'
        WHERE sid='$sid'";
    
    $result = mysqli_query($db, $sql);
    echo $result ? "<h4 style='margin:30px;'>提示：信息更改成功！</h4>" :
                   "<h4 style='margin:30px;'>注意：数据未更改！</h4>";
    echo '<div style="width: 90%;margin: 50px"><div style="margin: 0 auto;width: 90px;height: 30px;background-color: #117700">
        <a style="text-decoration: none;padding:3px;color: #f3f3f3;text-align: center;display: block" href="#" onclick="javascript:history.back(-1);">返回</a></div></div>';
    mysqli_close($db);
    exit();
}

$com = "SELECT * FROM student WHERE sid='$sid'";
$result = mysqli_query($db, $com);

if ($result && $row = mysqli_fetch_object($result)) {
?>
<!DOCTYPE html>
<html lang="cn">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./user.css">
    <title>修改信息</title>
</head>
<body>
<h3 class="subtitle">修改部分信息</h3>
<form method="post" action="editinfo.php">
    <div class="inputbox"><span>出生年月：</span><input name="birth" type="date" value="<?php echo $row->birth ?>"></div>
    <div class="inputbox"><span>邮箱：</span><input name="email" type="text" value="<?php echo $row->email ?>"></div>
    <div class="inputbox"><span>手机：</span><input name="tel" type="text" value="<?php echo $row->tel ?>"></div>

    <div class="inputbox"><span>当前年级：</span>
        <select name="current_grade">
            <?php
            for ($i = 1; $i <= 12; $i++) {
                $grade = "$i 年级";
                echo "<option value='$grade'";
                if ($row->current_grade == $grade) echo " selected";
                echo ">$grade</option>";
            }
            ?>
        </select>
    </div>

    <div class="inputbox"><span>家中是否有科研人员：</span>
        <input type="checkbox" name="has_researcher" <?php if ($row->has_researcher) echo "checked"; ?>>
    </div>

    <div class="clickbox clearfloat">
        <input type="submit" value="提交修改">
    </div>
</form>
</body>
</html>
<?php
}
mysqli_close($db);
?>
