<?php
require_once("../../config/database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        exit("无效的卡片ID");
    }

    // 检查卡片是否存在
    $check = mysqli_query($db, "SELECT id FROM card WHERE id = $id");
    if (!$check || mysqli_num_rows($check) === 0) {
        exit("未找到对应卡片，可能已被删除。");
    }

    // 删除卡片
    $sql = "DELETE FROM card WHERE id = ?";
    $stmt = mysqli_prepare($db, $sql);
    if (!$stmt) {
        exit("数据库错误: " . mysqli_error($db));
    }
    mysqli_stmt_bind_param($stmt, "i", $id);
    $res = mysqli_stmt_execute($stmt);

    if ($res) {
        echo "卡片删除成功。<br>";
        echo "<a href='../modifyCard.php'>返回修改页面</a>";
    } else {
        echo "删除失败：" . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
} else {
    exit("非法请求");
}
