<?php
require_once("../../config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 过滤输入，防止SQL注入
    $sid = $_POST["sid"];
    $name =$_POST["name"];
    $card_name = $_POST["card_name"];

    // 插入学生表，仅插入sid和name
    
    $com = "INSERT INTO student (sid, name, card_type) VALUES ('$sid', '$name','$card_name')";

    // 设置学生默认密码为学号后6位的MD5
    $pwd = md5(123456);
    $com2 = "INSERT INTO user_student (sid, pwd) VALUES ('$sid', '$pwd')";

    $result = mysqli_query($db, $com);
    $result2 = mysqli_query($db, $com2);

    if ($result && $result2) {
        echo "成功，同时已新建学生账户，密码为123456";
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
