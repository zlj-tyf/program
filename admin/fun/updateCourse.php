<?php
require_once("../../config/database.php");

$cid = (int)$_POST["cid"];
$competition_name = mysqli_real_escape_string($db, $_POST["competition_name"]);
$competition_short_name = isset($_POST["competition_short_name"]) ? mysqli_real_escape_string($db, $_POST["competition_short_name"]) : null;
$competition_level = mysqli_real_escape_string($db, $_POST["competition_level"]);
$submit_time = mysqli_real_escape_string($db, $_POST["submit_time"]);
$submit_requirements = mysqli_real_escape_string($db, $_POST["submit_requirements"]);
$student_requirements = mysqli_real_escape_string($db, $_POST["student_requirements"]);
$card_requirement = (int)$_POST["card_requirement"];

$sql = "UPDATE course SET
    competition_name = '$competition_name',
    competition_short_name = " . ($competition_short_name === null || $competition_short_name === '' ? "NULL" : "'$competition_short_name'") . ",
    competition_level = '$competition_level',
    submit_time = '$submit_time',
    submit_requirements = '$submit_requirements',
    student_requirements = '$student_requirements',
    card_requirement = $card_requirement
    WHERE cid = $cid
";

$result = mysqli_query($db, $sql);

if ($result) {
    echo "<h4 style='margin:30px;'>修改成功！</h4>";
} else {
    echo "<h4 style='margin:30px;'>修改失败，请检查数据或联系管理员。错误：" . mysqli_error($db) . "</h4>";
}

mysqli_close($db);
?>
