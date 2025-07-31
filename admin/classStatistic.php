<?php
require_once("config/database.php");

// 设置字符集
mysqli_set_charset($conn, "utf8");

// 获取所有比赛信息
$sql = "SELECT cid, competition_name FROM course";
$result = mysqli_query($conn, $sql);

echo "<h2>各比赛选课人数统计</h2>";
echo "<table border='1' cellspacing='0' cellpadding='8'>";
echo "<tr><th>比赛 ID</th><th>比赛名称</th><th>已报名人数</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    $cid = $row['cid'];
    $name = $row['competition_name'];

    // 查询选课人数（只统计 status != '0'）
    $count_sql = "SELECT COUNT(*) AS total FROM student_course WHERE cid='$cid' AND status != '0'";
    $count_result = mysqli_query($conn, $count_sql);
    $count_data = mysqli_fetch_assoc($count_result);
    $count = $count_data['total'];

    echo "<tr>";
    echo "<td>{$cid}</td>";
    echo "<td>{$name}</td>";
    echo "<td>{$count}</td>";
    echo "</tr>";
}

echo "</table>";
?>
