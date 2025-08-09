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
body { font-family: Arial, sans-serif; padding: 40px; }
select, input, textarea { width: 300px; margin-bottom: 20px; padding: 8px; }
input[type="submit"] { width: 120px; background: green; color: white; cursor: pointer; }
#iframeContainer { margin-top: 20px; display: none; }
button { margin-right: 10px; padding: 6px 10px; }
</style>
</head>
<body>
<h2>添加日志记录</h2>
<form method="post" action="addLogFunc.php">
    <label for="cid">选择比赛：</label><br>
    <select name="cid" required>
        <option value="">--请选择--</option>
        <?php foreach ($courses as $course): ?>
        <option value="<?= htmlspecialchars($course['cid']) ?>">
            <?= htmlspecialchars($course['competition_name']) ?>
        </option>
        <?php endforeach; ?>
    </select><br>

    <label for="type">操作类型：</label><br>
    <select name="type" required>
        <option value="1">创建项目</option>
        <option value="2">修改项目</option>
    </select><br>

    <label for="reason">备注说明：</label><br>
    <textarea name="reason" rows="4"></textarea><br>

    <label for="logdate">日志日期：</label><br>
    <input type="date" name="logdate" value="<?= date('Y-m-d') ?>" required><br>
    <input type="submit" value="提交日志">
</form>

<?php
// 如果带有 URL 参数
if (!empty($_GET['url'])) {
    $url = htmlspecialchars($_GET['url'], ENT_QUOTES);
    $edit_url = !empty($_GET['edit_url']) ? htmlspecialchars($_GET['edit_url'], ENT_QUOTES) : '';

    echo "<hr><h3>最近文章</h3>";
    echo "<p>文章链接：{$url}</p>";

    echo "<button onclick=\"openInIframe('{$url}')\">📄 打开 (嵌入)</button>";
    echo "<button onclick=\"window.open('{$url}', '_blank')\">📄 打开 (新标签)</button>";

    if ($edit_url) {
        echo "<button onclick=\"openInIframe('{$edit_url}')\">✏️ 编辑 (嵌入)</button>";
        echo "<button onclick=\"window.open('{$edit_url}', '_blank')\">✏️ 编辑 (新标签)</button>";
    }

    echo '<div id="iframeContainer"><iframe id="wpIframe" src="" width="100%" height="600" style="border:1px solid #ccc;"></iframe></div>';
    echo '<script>
    function openInIframe(url) {
        document.getElementById("iframeContainer").style.display = "block";
        document.getElementById("wpIframe").src = url;
    }
    </script>';
}
?>
</body>
</html>
