<?php
require_once("../../config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST["name"];
    $card_type = $_POST["card_type"];

    // 插入学生数据（sid 自动增长）
    $com = "INSERT INTO student (name, card_type) VALUES (?, ?)";
    $stmt = mysqli_prepare($db, $com);
    mysqli_stmt_bind_param($stmt, "si", $name, $card_type);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        // 获取自动生成的 sid
        $sid = mysqli_insert_id($db);
        $pwd = md5('123456');

        // 插入账户数据
        $com2 = "INSERT INTO user_student (sid, pwd) VALUES (?, ?)";
        $stmt2 = mysqli_prepare($db, $com2);
        mysqli_stmt_bind_param($stmt2, "is", $sid, $pwd);
        $result2 = mysqli_stmt_execute($stmt2);

        if ($result2) {
            echo "添加成功，学号为 $sid ，默认密码为123456";
        } else {
            echo "学生已添加，但账户创建失败：" . mysqli_error($db);
        }
    } else {
        echo "数据未更改。错误信息：" . mysqli_error($db);
    }

    mysqli_close($db);
} else {
    echo "无效请求。";
}
?>
<div style="width: 90%;height: 55px;margin: 50px">
    <div style="margin: 0 auto;width: 90px;height: 30px;background-color: #117700">
        <a style="text-decoration: none;padding:3px;color: #f3f3f3;text-align: center;display: block" href="#" onclick="javascript:history.back(-1);">返回</a>
    </div> 
</div>
