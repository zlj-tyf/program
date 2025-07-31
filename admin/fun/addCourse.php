<?php
require_once("../../config/database.php");

// 获取表单字段
$competition_name = $_POST["competition_name"];
$competition_level = $_POST["competition_level"];
$submit_time = $_POST["submit_time"];
$submit_requirements = $_POST["submit_requirements"];
$student_requirements = $_POST["student_requirements"];
$card_requirement = $_POST["card_requirement"];

// 构造 SQL 插入语句
$com = "INSERT INTO course (
    competition_name,
    competition_level,
    submit_time,
    submit_requirements,
    student_requirements,
    card_requirement
) VALUES (
    '$competition_name',
    '$competition_level',
    '$submit_time',
    '$submit_requirements',
    '$student_requirements',
    '$card_requirement'
)";

// 执行 SQL
$result = mysqli_query($db, $com);

// 显示结果
if($result){
    echo '<h4 style="margin:30px;">提示：已添加申报项目！</h4>';
} else {
    echo '<h4 style="margin:30px;">注意：添加失败，数据未更改！</h4>';
}

mysqli_close($db);
?>

<div style="width: 90%;height: 55px;margin: 50px">
    <div style="margin: 0 auto;width: 90px;height: 30px;background-color: #117700">
        <a style="text-decoration: none;padding:3px;color: #f3f3f3;text-align: center;display: block" href="./myLog.php">返回</a>
    </div>
</div>
