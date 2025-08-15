<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once("../config/database.php");

if (!isset($_SESSION['user'])) {
    die("请先登录！");
}

$sid = $_SESSION["user"];

// 获取已报名的比赛
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
$stmt->close();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>添加日志</title>
<style>
html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: Arial, sans-serif;
    overflow: hidden; /* 禁止页面滚动 */
}
.container {
    display: flex;
    height: 100%;
    padding: 20px;
    box-sizing: border-box;
    gap: 20px;
}
/* 左侧表单：最大宽度 30% */
.left {
    flex: 0 0 30%;
    max-width: 30%;
}
.left select, .left input, .left textarea {
    width: 100%;
    margin-bottom: 20px;
    padding: 8px;
}
.left input[type="submit"] {
    width: 120px;
    background: green;
    color: white;
    cursor: pointer;
}

/* 右侧最近文章 */
.right {
    flex: 1;
    display: flex;
    flex-direction: column;
}
.right-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.button-group {
    display: flex;
    gap: 10px;
    margin: 10px 0;
}
.button-group button {
    flex: 1;
    padding: 8px;
    cursor: pointer;
}
/* iframe 高度与左侧表单一致 */
#iframeContainer {
    height: 100%;
    border: 1px solid #ccc;
    display: none;
}
#iframeContainer iframe {
    width: 100%;
    height: 100%;
    border: none;
}
</style>
<link rel="stylesheet" type="text/css" href="./user.css">
</head>
<body>
<div class="container">
    <!-- 左侧表单 -->
    <div class="left" id="leftForm">
        <h2>添加日志记录</h2>
        <form method="post" action="addLogFunc.php">
            <label for="cid">选择比赛：</label><br>
            <select class="selectbox" name="cid" required>
                <option value="">--请选择--</option>
                <?php foreach ($courses as $course): ?>
                <option value="<?= htmlspecialchars($course['cid']) ?>">
                    <?= htmlspecialchars($course['competition_name']) ?>
                </option>
                <?php endforeach; ?>
            </select><br>

            <label for="type">操作类型：</label><br>
            <select class="selectbox" name="type" required>
                <option value="1">创建项目</option>
                <option value="2">修改项目</option>
            </select><br>

            <label for="reason">备注说明：</label><br>
            <textarea name="reason" rows="4"></textarea><br>

            <label for="logdate">日志日期：</label><br>
            <input type="date" name="logdate" value="<?= date('Y-m-d') ?>" required><br>
            <input type="submit" value="提交日志">
        </form>
    </div>

    <!-- 右侧最近文章 -->
    <div class="right" id="rightContainer">
    <?php if (!empty($_GET['url'])): 
        $url = htmlspecialchars($_GET['url'], ENT_QUOTES);
        $edit_url = !empty($_GET['edit_url']) ? htmlspecialchars($_GET['edit_url'], ENT_QUOTES) : '';
    ?>
        <div class="right-header">
            <h2>最近文章</h2>
            <p>点击编辑以修改文章内容<br/>点击打开页面以上传文件</p>
            <div class="button-group">
                <button onclick="openInIframe('<?= $url ?>')">📄 打开 (嵌入)</button>
                <?php if ($edit_url): ?>
                <button onclick="openInIframe('<?= $edit_url ?>')">✏️ 编辑 (嵌入)</button>
                <?php endif; ?>
                <button onclick="window.open('<?= $url ?>', '_blank')">📄 打开 (新标签)</button>
                <?php if ($edit_url): ?>
                <button onclick="window.open('<?= $edit_url ?>', '_blank')">✏️ 编辑 (新标签)</button>
                <?php endif; ?>
            </div>
        </div>
        <div id="iframeContainer">
            <iframe id="wpIframe" src=""></iframe>
        </div>
        <!-- <p>文章链接：<a href="<?= $url ?>" target="_blank">具体链接</a></p> -->
        <script>
        function openInIframe(url) {
            const iframeContainer = document.getElementById("iframeContainer");
            const leftForm = document.getElementById("leftForm");
            iframeContainer.style.height = leftForm.offsetHeight + "px"; // 高度与左侧表单一致
            iframeContainer.style.display = "block";
            document.getElementById("wpIframe").src = url;
        }
        </script>
    <?php endif; ?>
    </div>
</div>
</body>
</html>
