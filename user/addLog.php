<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../config/database.php");

if (!isset($_SESSION['user'])) {
    die("请先登录！");
}

$sid = $_SESSION["user"];

// 读取当前学生报名的所有比赛
$sql = "SELECT sc.cid, c.competition_name 
        FROM student_course sc 
        JOIN course c ON sc.cid = c.cid 
        WHERE sc.sid = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $sid);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>添加日志</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; }
        select, input, textarea { width: 300px; margin-bottom: 20px; padding: 8px; }
        input[type="submit"] { width: 120px; background: green; color: white; cursor: pointer; }
    </style>
</head>
<body>
    <h2>添加日志记录</h2>
    <form method="post" action="addLogFunc.php">
        <label for="cid">选择比赛：</label><br>
        <select name="cid" required>
            <option value="">--请选择--</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= htmlspecialchars($course['cid']) ?>">
                    <?= htmlspecialchars($course['competition_name']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="type">操作类型：</label><br>
        <select name="type" required>
            <option value="1">创建项目</option>
            <option value="2">修改项目</option>
        </select><br>

        <label for="reason">备注说明：</label><br>
        <textarea name="reason" rows="4"></textarea><br>

        <label for="logdate">日志日期：</label><br>
        <input type="date" name="logdate" value="<?= date('Y-m-d') ?>" required><br>
        <input type="submit" value="提交日志">
    </form>
</body>
</html>
