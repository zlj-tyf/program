<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../css/fun.css">
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
require_once("../../config/database.php");

$sid = $_GET['sid'] ?? '';
$name = $_GET['name'] ?? '';

$sql = "SELECT s.sid, s.name, c.competition_name, l.type, l.logdate, l.reason, l.url, l.addtime 
        FROM student_log l
        JOIN student s ON l.sid = s.sid 
        JOIN course c ON l.cid = c.cid 
        WHERE 1 = 1";

if (!empty($sid)) {
    $sql .= " AND s.sid LIKE '%" . $db->real_escape_string($sid) . "%'";
}
if (!empty($name)) {
    $sql .= " AND s.name LIKE '%" . $db->real_escape_string($name) . "%'";
}

$sql .= " ORDER BY l.addtime DESC";

$result = $db->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><a href='./modiLog.php?sid={$row['sid']}&addtime={$row['addtime']}'>{$row['sid']}</a></td>";
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
}
$db->close();
?>
</table>
</body>
</html>
