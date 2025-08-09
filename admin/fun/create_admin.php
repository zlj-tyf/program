<?php
session_start();

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');

if(!isset($_SESSION["login"]) || !isset($_SESSION["admin"]) || $_SESSION["admin"] != 999){
    echo json_encode(["success" => false, "message" => "权限不足"]);
    exit();
}

require_once '../../config/database.php';

function respond($success, $message, $debug=null) {
    $ret = ["success" => $success, "message" => $message];
    if ($debug !== null) {
        $ret['debug'] = $debug;
    }
    echo json_encode($ret);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, "无效请求方式");
}

$adminName = trim($_POST['adminName'] ?? '');
$pwd = $_POST['pwd'] ?? '';
$permissions = $_POST['permissions'] ?? [];

if ($adminName === '' || $pwd === '') {
    respond(false, "用户名和密码不能为空");
}

if (in_array($adminName, ['admin', 'root', '999'], true)) {
    respond(false, "非法用户名");
}

// 检查用户名是否存在
$stmt = $db->prepare("SELECT adminID FROM user_admin WHERE adminName = ?");
if (!$stmt) {
    respond(false, "数据库错误（prepare失败）", $db->error);
}
$stmt->bind_param("s", $adminName);
if (!$stmt->execute()) {
    respond(false, "数据库错误（execute失败）", $stmt->error);
}
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    respond(false, "用户名已存在");
}
$stmt->close();

// 查询最大 adminID
$result = $db->query("SELECT MAX(CAST(adminID AS UNSIGNED)) AS max_id FROM user_admin");
if (!$result) {
    respond(false, "数据库错误（查询最大adminID失败）", $db->error);
}
$row = $result->fetch_assoc();
$max_id = $row && $row['max_id'] ? intval($row['max_id']) : 0;
$new_id = $max_id + 1;
if ($new_id == 999) $new_id = 1000;

$pwd_md5 = md5($pwd);
$permissions_str = implode(',', $permissions);

$stmt = $db->prepare("INSERT INTO user_admin (adminID, adminName, pwd, permissions) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    respond(false, "数据库错误（插入prepare失败）", $db->error);
}

$stmt->bind_param("isss", $new_id, $adminName, $pwd_md5, $permissions_str);
if (!$stmt->execute()) {
    $error = $stmt->error;
    $stmt->close();
    respond(false, "数据库错误（插入execute失败）", $error);
}
$stmt->close();

respond(true, "创建成功");

$db->close();
