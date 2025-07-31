<?php
session_start();
$sid = $_SESSION["user"];
require_once("../config/database.php");

$com = "SELECT * FROM student WHERE sid='$sid'";
$result = mysqli_query($db, $com);

if ($result && $row = mysqli_fetch_object($result)) {
?>
<!DOCTYPE html>
<html lang="cn">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./user.css">
    <title>学生信息</title>
</head>
<body>
<h3 class="subtitle">学生基本信息</h3>
<form>
    <div class="inputbox"><span>学号：</span><input name="sid" type="text" value="<?php echo $row->sid ?>" disabled></div>
    <div class="inputbox"><span>姓名：</span><input name="name" type="text" value="<?php echo $row->name ?>" disabled></div>
    <div class="inputbox"><span>性别：</span>
        <select name="sex" disabled>
            <option value="男" <?php if ($row->sex == '男') echo "selected"; ?>>男</option>
            <option value="女" <?php if ($row->sex == '女') echo "selected"; ?>>女</option>
        </select>
    </div>
    <div class="inputbox"><span>出生年月：</span><input name="birth" type="date" value="<?php echo $row->birth ?>" disabled></div>
    <div class="inputbox"><span>年龄：</span><input name="age" type="text" value="<?php echo $row->age ?>" disabled></div>

    <div class="inputbox"><span>当前年级：</span>
        <select name="current_grade" disabled>
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

    <div class="inputbox"><span>当前学校：</span><input name="current_school" type="text" value="<?php echo $row->current_school ?>" disabled></div>

    <h3 class="subtitle">教育经历</h3>
    <table>
        <tr><th>学段</th><th>起始年份</th><th>结束年份</th><th>学校名称</th></tr>
        <tr><td>小学</td>
            <td><input name="primary_start_year" value="<?php echo $row->primary_start_year ?>" disabled></td>
            <td><input name="primary_end_year" value="<?php echo $row->primary_end_year ?>" disabled></td>
            <td><input name="primary_school_name" value="<?php echo $row->primary_school_name ?>" disabled></td></tr>
        <tr><td>初中</td>
            <td><input name="junior_start_year" value="<?php echo $row->junior_start_year ?>" disabled></td>
            <td><input name="junior_end_year" value="<?php echo $row->junior_end_year ?>" disabled></td>
            <td><input name="junior_school_name" value="<?php echo $row->junior_school_name ?>" disabled></td></tr>
        <tr><td>高中</td>
            <td><input name="senior_start_year" value="<?php echo $row->senior_start_year ?>" disabled></td>
            <td><input name="senior_end_year" value="<?php echo $row->senior_end_year ?>" disabled></td>
            <td><input name="senior_school_name" value="<?php echo $row->senior_school_name ?>" disabled></td></tr>
    </table>

    <h3 class="subtitle">家长信息</h3>
    <div class="inputbox"><span>父亲姓名：</span><input name="father_name" value="<?php echo $row->father_name ?>" disabled></div>
    <div class="inputbox"><span>联系方式：</span><input name="father_tel" value="<?php echo $row->father_tel ?>" disabled></div>
    <div class="inputbox"><span>工作单位：</span><input name="father_work_unit" value="<?php echo $row->father_work_unit ?>" disabled></div>
    <div class="inputbox"><span>职务职称：</span><input name="father_title" value="<?php echo $row->father_title ?>" disabled></div>

    <div class="inputbox"><span>母亲姓名：</span><input name="mother_name" value="<?php echo $row->mother_name ?>" disabled></div>
    <div class="inputbox"><span>联系方式：</span><input name="mother_tel" value="<?php echo $row->mother_tel ?>" disabled></div>
    <div class="inputbox"><span>工作单位：</span><input name="mother_work_unit" value="<?php echo $row->mother_work_unit ?>" disabled></div>
    <div class="inputbox"><span>职务职称：</span><input name="mother_title" value="<?php echo $row->mother_title ?>" disabled></div>

    <div class="inputbox"><span>家中是否有科研人员：</span>
        <input type="checkbox" name="has_researcher" <?php if ($row->has_researcher) echo "checked"; ?> disabled>
    </div>

    <div class="inputbox"><span>邮箱：</span><input name="email" value="<?php echo $row->email ?>" disabled></div>
    <div class="inputbox"><span>手机：</span><input name="tel" value="<?php echo $row->tel ?>" disabled></div>
</form>
</body>
</html>
<?php
}
mysqli_close($db);
?>
