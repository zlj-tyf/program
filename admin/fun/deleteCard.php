<?php
require_once("../../config/database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id <= 0) {
        exit("无效的卡种ID");
    }

    $sql = "DELETE FROM card WHERE id=?";
    $stmt = mysqli_prepare($db, $sql);
    if (!$stmt) {
        exit("数据库错误: " . mysqli_error($db));
    }
    mysqli_stmt_bind_param($stmt, "i", $id);
    $res = mysqli_stmt_execute($stmt);

    if ($res) {
        echo "卡种删除成功。<br>";
        echo "<a href='../modifyCard.php'>返回修改页面</a>";
    } else {
        echo "删除失败：" . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
} else {
    exit("非法请求");
}
