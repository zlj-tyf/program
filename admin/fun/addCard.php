<?php
require_once("../../config/database.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = mysqli_real_escape_string($db, $_POST["name"]);
    $allowed_courses = isset($_POST["allowed_courses"]) ? $_POST["allowed_courses"] : [];
    $max_courses = (int)$_POST["max_courses"];

    // allowed_courses 存为逗号分隔字符串
    $allowed_courses_str = implode(",", array_map('intval', $allowed_courses));

    $sql = "INSERT INTO card (name, allowed_courses, max_courses) VALUES (
        '$name',
        '$allowed_courses_str',
        $max_courses
    )";

    $result = mysqli_query($db, $sql);

    if ($result) {
        echo "<h4 style='margin:30px;color:green;'>卡种添加成功！</h4>";
    } else {
        echo "<h4 style='margin:30px;color:red;'>添加失败，错误：" . mysqli_error($db) . "</h4>";
    }

    mysqli_close($db);
} else {
    echo "<h4 style='margin:30px;color:red;'>非法请求</h4>";
}
?>
