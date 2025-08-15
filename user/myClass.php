<?php
session_start();
if (!isset($_SESSION["user"])) {
    echo "请先登录。";
    exit;
}
$sid = $_SESSION["user"];
require_once("../config/database.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>已选课程管理</title>
    <link rel="stylesheet" type="text/css" href="./user.css">
    <style>
        .disabled {
            color: gray;
            /* pointer-events: none; */
            text-decoration: none;
        }
    </style>
    <script>
        function confirmDel(url, canDelete) {
            if (!canDelete) {
                alert("该比赛由于存在进度，不可退选！");
                return false;
            }
            if (confirm("确定要退选该比赛吗？")) {
                window.location.href = url;
            }
            return false;
        }
    </script>
</head>
<body>
<h3>我的选课</h3>
<p style="color:red">本页面删除选课后，请及时联系教务老师确认恢复次卡额度状态。</p>
<table class="table-longtext" border="1">
    <tr>
        <th>课程编号</th>
        <th>比赛名称</th>
        <th>比赛级别</th>
        <th>申报时间</th>
        <th>申报要求</th>
        <th>学生提交材料</th>
        <th>操作</th>
    </tr>
    <?php
    // 查询当前学生选的课程（未打分）及状态
    $sql = "
        SELECT course.*, sc.status 
        FROM course 
        NATURAL JOIN student_course sc
        WHERE sc.sid = '$sid' AND sc.score IS NULL
    ";

    $result = mysqli_query($db, $sql);
    if ($result) {
        while ($row = mysqli_fetch_object($result)) {
            $canDelete = ($row->status == 1); // status=1才可退选
            $delUrl = "delCourse.php?cid=" . urlencode($row->cid) . "&sid=" . urlencode($sid);
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row->cid) . "</td>";
            echo "<td>" . htmlspecialchars($row->competition_name) . "</td>";
            echo "<td>" . htmlspecialchars($row->competition_level) . "</td>";
            echo "<td>" . htmlspecialchars($row->submit_time) . "</td>";
            echo "<td>" . htmlspecialchars($row->submit_requirements) . "</td>";
            echo "<td>" . htmlspecialchars($row->student_requirements) . "</td>";
            echo "<td>";
            if ($canDelete) {
                echo "<a href='#' onclick='return confirmDel(\"$delUrl\", true)'>退选</a> | ";
            } else {
                echo "<a href='#' class='disabled' onclick='confirmDel(\"$delUrl\", false)'>退选</a> | ";
            }
            echo "<a href='addLog.php?cid=" . urlencode($row->cid) . "'>添加记录</a>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>加载失败</td></tr>";
    }

    mysqli_close($db);
    ?>
</table>
</body>
</html>
