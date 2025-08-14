<?php
session_start();

$sid = $_SESSION["user"]; // 当前登录学生的 sid
require_once("../config/database.php");
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="./user.css">
    <title>学生日志查询</title>
</head>
<body>
    <h3>我的日志</h3>
    <table border="1">
        <tr>
            <th>操作类型</th>
            <th>备注</th>
            <th>链接</th>
            <th>日志日期</th>
            <th>录入时间</th>
            <th>操作</th>
        </tr>
        <?php
        // 查询日志
        $sql = "SELECT l.*, c.competition_name, l.url
                FROM student_log l
                LEFT JOIN course c ON l.cid = c.cid
                WHERE l.sid = '" . $db->real_escape_string($sid) . "'
                ORDER BY l.addtime DESC";

        $result = $db->query($sql);

        if ($result) {
            $count = 0;
            while ($row = $result->fetch_object()) {
                $count++;
                echo "<tr>";
                echo "<td>" . ($row->type == 1 ? "创建项目" : "修改项目") . "</td>";
                echo "<td>" . htmlspecialchars($row->reason) . "</td>";
                echo "<td>";
                if (!empty($row->url)) {
                    echo "<a href='" . htmlspecialchars($row->url) . "' target='_blank'>查看</a>";
                } else {
                    echo "-";
                }
                echo "</td>";
                echo "<td>{$row->logdate}</td>";
                echo "<td>{$row->addtime}</td>";
                echo "<td><a href='modiLog.php?sid={$row->sid}&addtime={$row->addtime}'>修改</a></td>";
                echo "</tr>";
            }
            if ($count === 0) {
                echo "<tr><td colspan='6'>暂无日志记录</td></tr>";
            }
        }
        $db->close();
        ?>
    </table>
</body>
</html>
