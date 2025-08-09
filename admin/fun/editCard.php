<?php
require_once("../../config/database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $allowed_courses = isset($_POST['allowed_courses']) ? $_POST['allowed_courses'] : [];
    $max_courses = isset($_POST['max_courses']) ? intval($_POST['max_courses']) : 0;

    if ($id <= 0) {
        exit("无效的卡种ID");
    }
    if ($name === '') {
        exit("卡种名称不能为空");
    }
    if (!is_array($allowed_courses)) {
        $allowed_courses = [];
    }

    // 处理允许课程，转成逗号分隔字符串
    $allowed_courses_str = implode(",", $allowed_courses);

    // 更新数据库
    $sql = "UPDATE card SET name=?, allowed_courses=?, max_courses=? WHERE id=?";
    $stmt = mysqli_prepare($db, $sql);
    if (!$stmt) {
        exit("数据库错误: " . mysqli_error($db));
    }
    mysqli_stmt_bind_param($stmt, "ssii", $name, $allowed_courses_str, $max_courses, $id);
    $res = mysqli_stmt_execute($stmt);

    if ($res) {
        echo "卡种信息更新成功。<br>";
        echo "<a href='../modifyCard.php'>返回修改页面</a>";
    } else {
        echo "更新失败：" . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
} else {
    exit("非法请求");
}
