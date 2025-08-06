<?php
session_start();
require_once("../config/database.php");

if (!isset($_SESSION['user'])) {
    die("请先登录！");
}

$sid = $_SESSION['user'];

// 读取学生已报名的比赛，生成下拉列表
$stmt = $db->prepare("SELECT c.cid, c.competition_name FROM course c JOIN student_course sc ON c.cid = sc.cid WHERE sc.sid = ?");
$stmt->bind_param("s", $sid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8" />
    <title>提交日志</title>
</head>
<body>
<h2>添加日志</h2>
<h2> 请先登录 WordPress：<a href="/wp-login.php" target="_blank">wp-login.php</a>，用户名为学号，密码为 123456。<br/>登陆完成后返回本页面。</h2>
<form action="addLogFunc.php" method="post">
    <label>比赛：
        <select name="cid" required>
            <option value="">请选择比赛</option>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?= $row['cid'] ?>"><?= htmlspecialchars($row['competition_name']) ?></option>
            <?php endwhile; ?>
        </select>
    </label><br><br>

    <label>操作类型：
        <select name="type" required>
            <option value="1">创建新项目</option>
            <option value="2">修改项目</option>
        </select>
    </label><br><br>

    <label>备注（reason）：<br>
        <textarea name="reason" rows="4" cols="50"></textarea>
    </label><br><br>

    <label>日志日期：
        <input type="date" name="logdate" required value="<?= date('Y-m-d') ?>">
    </label><br><br>

    <input type="submit" value="提交日志">
</form>

</body>
</html>
