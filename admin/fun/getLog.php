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
session_start();
require_once("../../config/database.php");

$adminID = $_SESSION["admin"] ?? '';

if (!$adminID) {
    die("<tr><td colspan='8'>未登录或权限不足</td></tr>");
}

// 获取当前管理员可访问的学生 sid 列表
$accessSids = [];
$adminRes = $db->query("SELECT access_student FROM user_admin WHERE adminID = '" . $db->real_escape_string($adminID) . "' LIMIT 1");
if ($adminRes && $row = $adminRes->fetch_assoc()) {
    $accessSids = json_decode($row['access_student'], true);
}

// 如果 accessSids 为空或者解析失败，直接返回无权限
if (empty($accessSids) || !is_array($accessSids)) {
    echo "<tr><td colspan='8'>无权限访问任何学生日志</td></tr>";
    $db->close();
    exit;
}

$sid = $_GET['sid'] ?? '';
$name = $_GET['name'] ?? '';

// 基本 SQL，限制可访问学生
$sql = "SELECT s.sid, s.name, c.competition_name, l.type, l.logdate, l.reason, l.url, l.addtime 
        FROM student_log l
        JOIN student s ON l.sid = s.sid 
        JOIN course c ON l.cid = c.cid 
        WHERE s.sid IN ('" . implode("','", array_map([$db, 'real_escape_string'], $accessSids)) . "')";

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
