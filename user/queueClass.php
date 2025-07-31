<?php
session_start();
if (!isset($_SESSION["user"])) {
    echo "请先登录。";
    exit;
}
$sid = $_SESSION["user"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>课程列表</title>
    <link rel="stylesheet" type="text/css" href="./user.css" />
    <script>
    function chooseCourse(courseId) {
        // 创建异步请求
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "./chooseClass.php?id=" + encodeURIComponent(courseId), true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                var resultBox = document.getElementById("resultBox");
                if (xhr.status === 200) {
                    resultBox.innerHTML = xhr.responseText;
                } else {
                    resultBox.innerHTML = "请求失败，状态码：" + xhr.status;
                }
            }
        };
        xhr.send();
    }
    </script>
</head>
<body>
<h2>课程列表</h2>
<table border="1">
    <tr>
        <th>课程编号</th>
        <th>比赛名称</th>
        <th>比赛级别</th>
        <th>申报时间</th>
        <th>申报要求</th>
        <th>学生提交材料</th>
        <th>卡种类要求</th>
        <th>操作</th>
    </tr>
    <?php
    require_once("../config/database.php");

    $sql = "SELECT * FROM course";
    $result = mysqli_query($db, $sql);

    if ($result) {
        while ($row = mysqli_fetch_object($result)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row->cid) . '</td>';
            echo '<td>' . htmlspecialchars($row->competition_name) . '</td>';
            echo '<td>' . htmlspecialchars($row->competition_level) . '</td>';
            echo '<td>' . htmlspecialchars($row->submit_time) . '</td>';
            echo '<td>' . htmlspecialchars($row->submit_requirements) . '</td>';
            echo '<td>' . htmlspecialchars($row->student_requirements) . '</td>';
            echo '<td>' . htmlspecialchars($row->card_requirement) . '</td>';
            echo '<td><button onclick="chooseCourse(\'' . addslashes($row->cid) . '\')">选课</button></td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="8">课程加载失败</td></tr>';
    }

    mysqli_close($db);
    ?>
</table>

<!-- 选课结果显示区域 -->
<div id="resultBox" style="margin-top:20px; padding:10px; border:1px solid #ccc; min-height:30px;"></div>

</body>
</html>
