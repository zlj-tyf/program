<?php
header('Content-Type: application/json');
require_once '../config/database.php';

// 获取请求类型
$method = $_SERVER['REQUEST_METHOD'];

if($method === 'GET'){
    // 获取管理员信息和学生列表
    $admins = [];
    $res = $db->query("SELECT adminID, adminName FROM user_admin ORDER BY adminID");
    while($row = $res->fetch_assoc()){
        $admins[] = $row;
    }

    $students = [];
    $res = $db->query("SELECT sid, name FROM student ORDER BY sid");
    while($row = $res->fetch_assoc()){
        $students[] = $row;
    }

    echo json_encode([
        'admins' => $admins,
        'students' => $students
    ]);
    exit();
}

if($method === 'POST'){
    $data = json_decode(file_get_contents('php://input'), true);
    $adminID = intval($data['adminID']);
    $students = isset($data['students']) ? $data['students'] : [];
    $jsonData = json_encode($students, JSON_UNESCAPED_UNICODE);

    $stmt = $db->prepare("UPDATE user_admin SET access_student=? WHERE adminID=?");
    $stmt->bind_param("si", $jsonData, $adminID);
    if($stmt->execute()){
        echo json_encode(['success'=>true, 'message'=>'保存成功']);
    }else{
        echo json_encode(['success'=>false, 'message'=>'保存失败']);
    }
    $stmt->close();
    exit();
}

if($method === 'GET' && isset($_GET['adminID'])){
    // 获取指定管理员已有访问学生
    $adminID = intval($_GET['adminID']);
    $stmt = $db->prepare("SELECT access_student FROM user_admin WHERE adminID=?");
    $stmt->bind_param("i", $adminID);
    $stmt->execute();
    $res = $stmt->get_result();
    $access = [];
    if($row = $res->fetch_assoc()){
        $access = json_decode($row['access_student'], true) ?: [];
    }
    $stmt->close();
    echo json_encode(['access' => $access]);
    exit();
}
?>
