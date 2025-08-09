<?php
session_start();

if (!isset($_POST['user'], $_POST['pass'], $_POST['role'], $_POST['login_type'])) {
    header("Location: ./?retry=1");
    exit();
}

$user_input = $_POST["user"];
$pass = $_POST["pass"];
$role = $_POST["role"];
$login_type = $_POST["login_type"];
$pwd = md5($pass);

require_once('./config/database.php');

if ($role === 'student') {
    // 如果用用户名登录，先找 student 表拿 sid
    if ($login_type === 'name') {
        $stmt = $db->prepare("SELECT sid FROM student WHERE name = ?");
        $stmt->bind_param("s", $user_input);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $sid = $row['sid'];
        } else {
            // 找不到用户名
            header("Location: ./?retry=1");
            exit();
        }
        $stmt->close();
    } else {
        // 直接用输入当 sid
        $sid = $user_input;
    }
    // 用 sid + pwd 去 user_student 表查找
    $stmt = $db->prepare("SELECT sid FROM user_student WHERE sid = ? AND pwd = ?");
    $stmt->bind_param("ss", $sid, $pwd);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $_SESSION["login"] = true;
        $_SESSION["user"] = $sid;
        header("Location: ./user/");
        exit();
    } else {
        header("Location: ./?retry=1");
        exit();
    }
} else if ($role === 'admin') {
    // 管理员登录，先尝试用ID查，再用用户名查
    if ($login_type === 'id') {
        $stmt = $db->prepare("SELECT adminID FROM user_admin WHERE adminID = ? AND pwd = ?");
        $stmt->bind_param("ss", $user_input, $pwd);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $_SESSION["login"] = true;
            $_SESSION["admin"] = $user_input;
            header("Location: ./admin/");
            exit();
        } else {
            header("Location: ./?retry=1");
            exit();
        }
    } else if ($login_type === 'name') {
        // 用用户名登录，查 adminName 字段
        $stmt = $db->prepare("SELECT adminID FROM user_admin WHERE adminName = ? AND pwd = ?");
        $stmt->bind_param("ss", $user_input, $pwd);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $_SESSION["login"] = true;
            $_SESSION["admin"] = $row['adminID'];  // 记录 adminID 做session
            header("Location: ./admin/");
            exit();
        } else {
            header("Location: ./?retry=1");
            exit();
        }
    } else {
        // 未知登录类型
        header("Location: ./?retry=1");
        exit();
    }
} else {
    // 未知身份
    header("Location: ./?retry=1");
    exit();
}
?>
