<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

require '../../config/database.php';

$response = [
    'success' => false,
    'debug' => [],
    'error' => ''
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['error'] = "只接受 POST 请求";
    echo json_encode($response); exit;
}

$sid = isset($_POST['sid']) ? intval($_POST['sid']) : 0;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$cards = $_POST['cards'] ?? [];

if($sid <= 0){ $response['error'] = "无效的学号"; echo json_encode($response); exit; }
if($name === ''){ $response['error'] = "姓名不能为空"; echo json_encode($response); exit; }

$db->autocommit(false); // 开启事务

try {
    // 更新学生姓名
    $stmt = $db->prepare("UPDATE student SET name = ? WHERE sid = ?");
    if(!$stmt) throw new Exception("准备语句失败: " . $db->error);
    if (!$stmt->bind_param("si", $name, $sid)) {
        throw new Exception("绑定参数失败: " . $stmt->error);
    }    
    $stmt->execute() or throw new Exception("更新学生失败: " . $stmt->error);
    $response['debug'][] = "更新学生成功：sid=$sid, name=$name";
    $stmt->close();

    // 删除学生已有卡片
    $delSql = "DELETE FROM student_card WHERE sid=$sid";
    $db->query($delSql) or throw new Exception("删除卡片失败: " . $db->error . " SQL: $delSql");
    $response['debug'][] = "删除原有卡片成功";

    // 插入新卡片
    $insertSql = "INSERT INTO student_card (sid, card_id, card_count, used_count) VALUES (?, ?, ?, 0)";
    $stmtInsert = $db->prepare($insertSql) or throw new Exception("准备插入语句失败: " . $db->error);
    foreach($cards as $card){
        $card_id = intval($card['card_id']);
        $count = intval($card['count']);
        if($card_id<=0 || $count<0) continue;
        $stmtInsert->bind_param("iii",$sid,$card_id,$count) or throw new Exception("绑定参数失败: " . $stmtInsert->error);
        $stmtInsert->execute() or throw new Exception("插入卡片失败: " . $stmtInsert->error);
        $response['debug'][] = "插入卡片成功：card_id=$card_id, count=$count";
    }
    $stmtInsert->close();

    $db->commit();
    $response['success'] = true;

} catch(Exception $e){
    $db->rollback();
    $response['error'] = $e->getMessage();
}

$db->autocommit(true);
$db->close();

echo json_encode($response);
