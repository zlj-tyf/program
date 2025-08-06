<?php
require_once("../../config/database.php");

// 获取并转义表单字段
$competition_name = mysqli_real_escape_string($db, $_POST["competition_name"]);
$competition_short_name = mysqli_real_escape_string($db, $_POST["competition_short_name"] ?? '');
$competition_level = mysqli_real_escape_string($db, $_POST["competition_level"]);
$submit_time = mysqli_real_escape_string($db, $_POST["submit_time"]);
$submit_requirements = mysqli_real_escape_string($db, $_POST["submit_requirements"]);
$student_requirements = mysqli_real_escape_string($db, $_POST["student_requirements"]);
$default_content = mysqli_real_escape_string($db, $_POST["default_content"] ?? '');
$card_requirement = (int)$_POST["card_requirement"];  // 转为整数

// 构造 SQL 插入语句，competition_short_name 可为空
$com = "INSERT INTO course (
    competition_name,
    competition_short_name,
    competition_level,
    submit_time,
    submit_requirements,
    student_requirements,
    default_content,
    card_requirement
) VALUES (
    '$competition_name',
    " . ($competition_short_name === '' ? "NULL" : "'$competition_short_name'") . ",
    '$competition_level',
    '$submit_time',
    '$submit_requirements',
    '$student_requirements',
    '$default_content',
    $card_requirement
)";

// 执行 SQL
$result = mysqli_query($db, $com);

// 显示结果
if($result){
    echo '<h4 style="margin:30px;">提示：已添加申报项目！</h4>';
} else {
    echo '<h4 style="margin:30px;">注意：添加失败，数据未更改！错误：' . mysqli_error($db) . '</h4>';
}

mysqli_close($db);
?>

<div style="width: 90%;height: 55px;margin: 50px">
    <div style="margin: 0 auto;width: 90px;height: 30px;background-color: #117700">
        <a style="text-decoration: none;padding:3px;color: #f3f3f3;text-align: center;display: block" href="./myLog.php">返回</a>
    </div>
</div>
