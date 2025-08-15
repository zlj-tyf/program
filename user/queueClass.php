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
    <style>
        /* 保持标题固定 */
        h2 {
            position: sticky;
            top: 0;
            background-color: #f9f9f9;
            padding: 10px 0;
            margin: 0;
            z-index: 10;
            border-bottom: 1px solid #ccc;
        }

        /* 表格容器可滚动 */
        .table-container {
            height: 500px; /* 根据页面实际需要调整高度 */
            overflow-y: auto;
            margin: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            position: sticky;
            top: 0; /* 表头固定在滚动容器顶部 */
            background-color: #f1f1f1;
            z-index: 5;
        }
    </style>
    <script>
    function chooseCourse(courseId) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "chooseClass.php?id=" + encodeURIComponent(courseId), true);
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

<!-- 选课结果显示区域 -->
<div id="resultBox" style="
    margin: 20px;
    padding: 12px 15px;
    min-height: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    font-size: 16px;
    color: #333;
    transition: box-shadow 0.3s, border-color 0.3s, background-color 0.3s;
    word-wrap: break-word;
    white-space: pre-wrap;
"></div>

<div class="table-container">
    <table class="table-longtext">
        <tr>
            <th>课程编号</th>
            <th>操作</th>
            <th>比赛名称</th>
            <th>比赛级别</th>
            <th>申报时间</th>
            <th>申报要求</th>
            <th>学生提交材料</th>
        </tr>
        <?php
        require_once("../config/database.php");

        $sql = "SELECT * FROM course";
        $result = mysqli_query($db, $sql);

        if ($result) {
            while ($row = mysqli_fetch_object($result)) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row->cid) . '</td>';
                echo '<td><button onclick="chooseCourse(\'' . addslashes($row->cid) . '\')">选课</button></td>';
                echo '<td>' . htmlspecialchars($row->competition_name) . '</td>';
                echo '<td>' . htmlspecialchars($row->competition_level) . '</td>';
                echo '<td>' . htmlspecialchars($row->submit_time) . '</td>';
                echo '<td>' . htmlspecialchars($row->submit_requirements) . '</td>';
                echo '<td>' . htmlspecialchars($row->student_requirements) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="7">课程加载失败</td></tr>';
        }

        mysqli_close($db);
        ?>
    </table>
</div>

</body>
</html>
