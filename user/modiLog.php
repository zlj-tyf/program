<?php
require_once("../config/database.php");

$sid = $_GET['sid'];
$addtime = $_GET['addtime'];

$sql = "SELECT * FROM student_log WHERE sid = ? AND addtime = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $sid, $addtime);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="../admin/css/fun.css">
    <title>修改日志</title>
</head>
<body>
<h3 class="subtitle">日志管理 >> 修改信息</h3>
<form action="editLog.php" method="post">
    <input type="hidden" name="sid" value="<?= htmlspecialchars($row['sid']) ?>">
    <input type="hidden" name="addtime" value="<?= htmlspecialchars($row['addtime']) ?>">

    <div class="inputbox"><span>链接：</span>
        <input type="text" name="url" value="<?= htmlspecialchars($row['url']) ?>" style="width: 400px;">
    </div>

    <div class="inputbox"><span>备注：</span>
        <input type="text" name="reason" value="<?= htmlspecialchars($row['reason']) ?>" style="width: 400px;">
    </div>

    <div class="clickbox clearfloat"><input type="submit" value="修改信息"></div>
    <div class="redbox clickbox"><input type="button" onclick="history.back();" value="返回"></div>
</form>
</body>
</html>
<?php
}
$db->close();
?>
