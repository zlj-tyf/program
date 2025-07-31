<?php
require_once("../../config/database.php");

$id = $_POST["cid"];
$competition_name = $_POST["competition_name"];
$competition_level = $_POST["competition_level"];
$submit_time = $_POST["submit_time"];
$submit_requirements = $_POST["submit_requirements"];
$student_requirements = $_POST["student_requirements"];
$card_requirement = $_POST["card_requirement"];

$sql = "UPDATE course SET
    competition_name = '$competition_name',
    competition_level = '$competition_level',
    submit_time = '$submit_time',
    submit_requirements = '$submit_requirements',
    student_requirements = '$student_requirements',
    card_requirement = '$card_requirement'
    WHERE cid = $id
";

$result = mysqli_query($db, $sql);

if ($result) {
    echo "<h4 style='margin:30px;'>修改成功！</h4>";
} else {
    echo "<h4 style='margin:30px;'>修改失败，请检查数据或联系管理员。</h4>";
}
mysqli_close($db);
?>
