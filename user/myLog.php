<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="user.css">
    <title>查询结果</title>
</head>
<body>
<table>
    <tr>
        <th>学号</th>
        <th>姓名</th>
        <th>比赛</th>
        <th>操作类型</th>
        <th>日志日期</th>
        <th>备注</th>
        <th>链接</th>
        <th>操作</th>
    </tr>
<?php
session_start();
require_once("../config/database.php");

// 从 session 获取学生 sid
$sid = $_SESSION['user'];

// if (!$sid) {
//     die("<tr><td colspan='8'>未登录或权限不足</td></tr>");
// }

// 查询该学生的日志
$sql = "SELECT s.sid, s.name, c.competition_name, l.type, l.logdate, l.reason, l.url, l.addtime 
        FROM student_log l
        JOIN student s ON l.sid = s.sid 
        JOIN course c ON l.cid = c.cid 
        WHERE s.sid = '" . $db->real_escape_string($sid) . "'
        ORDER BY l.addtime DESC";

$result = $db->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['sid']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['competition_name']}</td>";
        echo "<td>" . ($row['type'] == 1 ? "创建项目" : "修改项目") . "</td>";
        echo "<td>{$row['logdate']}</td>";
        echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
        echo "<td><a href='" . htmlspecialchars($row['url']) . "' target='_blank'>查看</a></td>";
        echo "<td>
            <a href='modiLog.php?sid={$row['sid']}&addtime={$row['addtime']}'>修改</a> / 
            <a href='delLog.php?sid={$row['sid']}&addtime={$row['addtime']}'>删除</a>
        </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>没有日志记录</td></tr>";
}

$db->close();
?>
</table>
</body>
</html>
