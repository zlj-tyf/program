<?php
session_start();
if (!isset($_SESSION["user"])) {
    echo "请先登录后选课。";
    exit;
}
$sid = $_SESSION["user"];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "课程编号缺失。";
    exit;
}

$cid = $_GET['id'];

require_once("../config/database.php");

// 查询学生套餐等级
$sql = "SELECT card_type FROM student WHERE sid = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "s", $sid);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $card_type);
if (!mysqli_stmt_fetch($stmt)) {
    echo "找不到学生信息。";
    exit;
}
mysqli_stmt_close($stmt);

// 查询课程卡种类要求
$sql = "SELECT card_requirement FROM course WHERE id = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "s", $cid);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $card_requirement);
if (!mysqli_stmt_fetch($stmt)) {
    echo "找不到课程信息。";
    exit;
}
mysqli_stmt_close($stmt);

// 判断套餐是否满足
if ($card_type < $card_requirement) {
    echo "套餐等级不足，无法选课。";
    exit;
}

// 判断是否已选过该课程
$sql = "SELECT * FROM student_course WHERE sid = ? AND cid = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "ss", $sid, $cid);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) > 0) {
    echo "您已选过该课程。";
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit;
}
mysqli_stmt_close($stmt);

// 追加选课记录
$sql = "INSERT INTO student_course (sid, cid, score, status) VALUES (?, ?, NULL, 'N')";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "ss", $sid, $cid);
if (mysqli_stmt_execute($stmt)) {
    echo "选课成功！";
} else {
    echo "选课失败，请稍后重试。";
}
mysqli_stmt_close($stmt);
mysqli_close($db);
?>
