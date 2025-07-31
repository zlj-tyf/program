<?php
session_start();
require_once("../config/database.php");

if (!isset($_GET['sid']) || !isset($_GET['cid'])) {
    echo "参数错误";
    exit;
}

$sid = $_GET['sid'];
$cid = $_GET['cid'];

// 防止SQL注入
$sid = mysqli_real_escape_string($db, $sid);
$cid = mysqli_real_escape_string($db, $cid);

// 查询是否已报名
$query = "SELECT * FROM student_course WHERE sid='$sid' AND cid='$cid'";
$res = mysqli_query($db, $query);

if (mysqli_num_rows($res) > 0) {
    // 已报名，删除记录（取消报名）
    $delSql = "DELETE FROM student_course WHERE sid='$sid' AND cid='$cid'";
    mysqli_query($db, $delSql);
} else {
    // 未报名，插入记录（报名）
    $insSql = "INSERT INTO student_course (sid, cid) VALUES ('$sid', '$cid')";
    mysqli_query($db, $insSql);
}

mysqli_close($db);

// 跳转回管理页面
header("Location: editStudentCourse.php");
exit;
?>
