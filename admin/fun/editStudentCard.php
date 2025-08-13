<?php
// ./fun/editStudent.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../config/database.php';

header('Content-Type: text/plain; charset=utf-8'); // 纯文本输出方便调试

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("只接受 POST 请求");
}

$sid = isset($_POST['sid']) ? intval($_POST['sid']) : 0;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$cards = $_POST['cards'] ?? [];

if ($sid <= 0) {
    die("无效的学生学号");
}
if ($name === '') {
    die("姓名不能为空");
}

$db->autocommit(false); // 开启事务

try {
    // 更新学生姓名
    $stmt = $db->prepare("UPDATE student SET name = ? WHERE sid = ?");
    if (!$stmt) {
        throw new Exception("准备语句失败: " . $db->error);
    }
    if (!$stmt->bind_param("si", $name, $sid)) {
        throw new Exception("绑定参数失败: " . $stmt->error);
    }
    if (!$stmt->execute()) {
        throw new Exception("执行更新学生失败: " . $stmt->error);
    }
    $stmt->close();

    // 删除学生已有卡片
    $delSql = "DELETE FROM student_card WHERE sid = $sid";
    if (!$db->query($delSql)) {
        throw new Exception("删除学生卡片失败: " . $db->error . " SQL: $delSql");
    }

    // 插入新的卡片数据
    $insertSql = "INSERT INTO student_card (sid, card_id, card_count, used_count) VALUES (?, ?, ?, 0)";
    $stmtInsert = $db->prepare($insertSql);
    if (!$stmtInsert) {
        throw new Exception("准备插入卡片语句失败: " . $db->error);
    }

    foreach ($cards as $card) {
        $card_id = intval($card['card_id']);
        $count = intval($card['count']);
        if ($card_id <= 0 || $count < 0) {
            continue; // 跳过无效数据
        }
        if (!$stmtInsert->bind_param("iii", $sid, $card_id, $count)) {
            throw new Exception("绑定插入参数失败: " . $stmtInsert->error);
        }
        if (!$stmtInsert->execute()) {
            throw new Exception("插入卡片失败: " . $stmtInsert->error . " card_id=$card_id count=$count");
        }
    }
    $stmtInsert->close();

    $db->commit();

    echo "学生信息及卡片保存成功\n";

} catch (Exception $e) {
    $db->rollback();
    echo "错误: " . $e->getMessage() . "\n";
}

$db->autocommit(true);
$db->close();
