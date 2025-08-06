<?php
require_once("../../config/database.php");

$sid = $_GET["sid"];
$addtime = $_GET["addtime"];

$sql = "DELETE FROM student_log WHERE sid = ? AND addtime = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $sid, $addtime);

if ($stmt->execute()) {
    echo '<h4 style="margin:30px;">提示：操作成功。</h4>';
} else {
    echo '<h4 style="margin:30px;">注意：数据未更改！</h4>';
}
$db->close();
?>
<div style="width: 90%;height: 55px;margin: 50px">
    <div style="margin: 0 auto;width: 90px;height: 30px;background-color: #117700">
        <a style="text-decoration: none;padding:3px;color: #f3f3f3;text-align: center;display: block" href="#" onclick="history.back();">返回</a>
    </div>
</div>
