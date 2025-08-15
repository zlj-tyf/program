<?php
session_start();
if (!isset($_SESSION["user"])) {
    echo "请先登录。";
    exit;
}
$sid = $_SESSION["user"];
require_once("../config/database.php");

$statusIcon = [
    0 => "❌", // 未开始
    1 => "🔵", // 当前进行
    2 => "✅"  // 已完成
];

function getStatusIcon($current, $step) {
    if ($current < $step) return "❌";
    if ($current == $step) return "🔵";
    return "✅";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>已选课程管理</title>
    <link rel="stylesheet" type="text/css" href="./user.css">
</head>
<body>
<h3>我的选课</h3>
<p style="color:red">本页面删除选课后，请及时联系教务老师确认恢复次卡额度状态。</p>
<table class="table-longtext" border="1">
    <tr>
        <th>课程编号</th>
        <th>比赛名称</th>
        <th>文书提交</th>
        <th>文书修改</th>
        <th>文书定稿</th>
        <th>项目提交</th>
    </tr>
    <?php
    $sql = "
        SELECT sc.cid, sc.status, c.*
        FROM student_course sc
        JOIN course c ON sc.cid = c.cid
        WHERE sc.sid = '$sid'
    ";
    $result = mysqli_query($db, $sql);
    if ($result) {
        while ($row = mysqli_fetch_object($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row->cid) . "</td>";
            echo "<td>" . htmlspecialchars($row->competition_name) . "</td>";
            for ($i = 1; $i <= 4; $i++) {
                echo "<td style='text-align:center'>" . getStatusIcon($row->status, $i) . "</td>";
            }
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='12'>加载失败</td></tr>";
    }
    mysqli_close($db);
    ?>
</table>
</body>
</html>
