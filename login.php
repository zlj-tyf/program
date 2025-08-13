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

// 封装跳转失败函数，方便传参
function redirect_retry($role, $login_type) {
    $url = "./?retry=1&role=" . urlencode($role) . "&login_type=" . urlencode($login_type);
    header("Location: $url");
    exit();
}

if ($role === 'student') {
    if ($login_type === 'name') {
        $stmt = $db->prepare("SELECT sid FROM student WHERE name = ?");
        $stmt->bind_param("s", $user_input);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $sid = $row['sid'];
        } else {
            redirect_retry($role, $login_type);
        }
        $stmt->close();
    } else {
        $sid = $user_input;
    }

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
        redirect_retry($role, $login_type);
    }
} else if ($role === 'admin') {
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
            redirect_retry($role, $login_type);
        }
    } else if ($login_type === 'name') {
        $stmt = $db->prepare("SELECT adminID FROM user_admin WHERE adminName = ? AND pwd = ?");
        $stmt->bind_param("ss", $user_input, $pwd);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $_SESSION["login"] = true;
            $_SESSION["admin"] = $row['adminID'];
            header("Location: ./admin/");
            exit();
        } else {
            redirect_retry($role, $login_type);
        }
    } else {
        redirect_retry($role, $login_type);
    }
} else {
    redirect_retry($role, $login_type);
}
?>
