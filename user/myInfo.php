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
    <title>学籍信息</title>
</head>
<body>
<h3 class="subtitle">学籍信息</h3>
<form>
    <div class="inputbox"><span>学号：</span><?php echo htmlspecialchars($row->sid) ?></p></div>
    <div class="inputbox"><span>姓名：</span><?php echo htmlspecialchars($row->name) ?></p></div>
    <div class="inputbox"><span>性别：</span><?php echo htmlspecialchars($row->sex) ?></p></div>
    <div class="inputbox"><span>出生年月：</span><?php echo htmlspecialchars($row->birth) ?></p></div>
    <div class="inputbox"><span>年龄：</span><?php echo htmlspecialchars($row->age) ?></p></div>

    <h4>教育经历</h4>
    <div class="inputbox">
        <table border="1" cellpadding="5" cellspacing="0">
            <tr><th>阶段</th><th>起始年份</th><th>结束年份</th><th>学校名称</th></tr>
            <tr>
                <td>小学</td>
                <td><p><?php echo htmlspecialchars($row->edu_primary_start) ?></p></td>
                <td><p><?php echo htmlspecialchars($row->edu_primary_end) ?></p></td>
                <td><p><?php echo htmlspecialchars($row->edu_primary_school) ?></p></td>
            </tr>
            <tr>
                <td>初中</td>
                <td><p><?php echo htmlspecialchars($row->edu_junior_start) ?></p></td>
                <td><p><?php echo htmlspecialchars($row->edu_junior_end) ?></p></td>
                <td><p><?php echo htmlspecialchars($row->edu_junior_school) ?></p></td>
            </tr>
            <tr>
                <td>高中</td>
                <td><p><?php echo htmlspecialchars($row->edu_senior_start) ?></p></td>
                <td><p><?php echo htmlspecialchars($row->edu_senior_end) ?></p></td>
                <td><p><?php echo htmlspecialchars($row->edu_senior_school) ?></p></td>
            </tr>
        </table>
    </div>

    <div class="inputbox"><span>当前年级：<p><?php echo htmlspecialchars($row->current_grade) ?></p></div>
    <div class="inputbox"><span>当前学校：<p><?php echo htmlspecialchars($row->current_school) ?></p></div>

    <h4>家长信息</h4>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr><th></th><th>姓名</th><th>联系方式</th><th>工作单位</th><th>职务职称</th></tr>
        <tr>
            <td>父亲</td>
            <td><?php echo htmlspecialchars($row->father_name) ?></td>
            <td><?php echo htmlspecialchars($row->father_tel) ?></td>
            <td><?php echo htmlspecialchars($row->father_workplace) ?></td>
            <td><?php echo htmlspecialchars($row->father_position) ?></td>
        </tr>
        <tr>
            <td>母亲</td>
            <td><?php echo htmlspecialchars($row->mother_name) ?></td>
            <td><?php echo htmlspecialchars($row->mother_tel) ?></td>
            <td><?php echo htmlspecialchars($row->mother_workplace) ?></td>
            <td><?php echo htmlspecialchars($row->mother_position) ?></td>
        </tr>
    </table>

    <div class="inputbox"><span>家中是否有科研人员：<p><?php echo $row->has_researcher ? '是' : '否' ?></p></div>
</form>
</body>
</html>
<?php 
}
mysqli_close($db);
?>
