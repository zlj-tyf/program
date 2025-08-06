<?php
session_start();
require_once("../config/database.php");

if (!isset($_SESSION['user'])) {
    die("请先登录！");
}

$sid = $_SESSION['user'];
$cid = $_POST['cid'] ?? '';
$type = $_POST['type'] ?? '';
$reason = $_POST['reason'] ?? '';
$logdate = $_POST['logdate'] ?? '';
$addtime = date("Y-m-d H:i:s");

if (!$cid || !$type || !$logdate) {
    die("缺少必要参数");
}

// 插入日志，url初始为空
$stmt = $db->prepare("INSERT INTO student_log (sid, cid, type, reason, logdate, addtime, url) VALUES (?, ?, ?, ?, ?, ?, NULL)");
$stmt->bind_param("ssisss", $sid, $cid, $type, $reason, $logdate, $addtime);

if ($stmt->execute()) {
    echo "日志添加成功";
} else {
    echo "日志添加失败：" . $stmt->error;
}
$stmt->close();
$db->close();
?>
