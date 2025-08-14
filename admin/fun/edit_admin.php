<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if(!isset($_SESSION["login"]) || !isset($_SESSION["admin"]) || $_SESSION["admin"] != 999){
    echo json_encode(["success"=>false,"message"=>"权限不足"]);
    exit();
}

require_once '../../config/database.php';

function respond($success, $message){
    echo json_encode(["success"=>$success,"message"=>$message]);
    exit();
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    respond(false,"无效请求方式");
}

$adminID = $_POST['adminID'] ?? '';
$permissions = $_POST['permissions'] ?? [];

if(!$adminID){
    respond(false,"管理员ID不能为空");
}

$permissions_str = implode(',', $permissions);

$stmt = $db->prepare("UPDATE user_admin SET permissions=? WHERE adminID=?");
if(!$stmt){
    respond(false,"数据库错误(prepare失败) ".$db->error);
}
$stmt->bind_param("si", $permissions_str, $adminID);
if(!$stmt->execute()){
    $err = $stmt->error;
    $stmt->close();
    respond(false,"数据库错误(execute失败) ".$err);
}
$stmt->close();

respond(true,"修改成功");
$db->close();
