<?php
require_once("../../config/database.php");

$sid = $_GET['sid'] ?? '';
if (!$sid) {
    echo '<h4 style="margin:30px;">错误：缺少学号参数！</h4>';
    exit;
}

$sid_esc = mysqli_real_escape_string($db, $sid);

// 删除学生表和用户表
$sql_del_student = "DELETE FROM student WHERE sid = '$sid_esc'";
$sql_del_user = "DELETE FROM user_student WHERE sid = '$sid_esc'";
// 删除 student_card 中卡片数据
$sql_del_card = "DELETE FROM student_card WHERE sid = '$sid_esc'";

$res1 = mysqli_query($db, $sql_del_student);
$res2 = mysqli_query($db, $sql_del_user);
$res3 = mysqli_query($db, $sql_del_card);

if ($res1 && $res2 && $res3) {
    echo '<h4 style="margin:30px;">提示：操作成功，相关的学生账户及卡片信息已移除。</h4>';
} else {
    echo '<h4 style="margin:30px;">注意：数据未完全删除！</h4>';
}

mysqli_close($db);
?>

<div style="width: 90%;height: 55px;margin: 50px">
    <div style="margin: 0 auto;width: 90px;height: 30px;background-color: #117700">
        <a style="text-decoration: none;padding:3px;color: #f3f3f3;text-align: center;display: block" href="#" onclick="javascript:history.back(-1);">返回</a>
    </div>
</div>
