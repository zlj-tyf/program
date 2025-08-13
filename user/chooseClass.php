<?php
session_start();
if (!isset($_SESSION["user"])) {
    echo "请先登录后选课。";
    exit;
}
$sid = $_SESSION["user"];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "课程编号缺失。";
    exit;
}

$cid = $_GET['id'];

require_once("../config/database.php");

// 判断是否已选过该课程
$sql = "SELECT * FROM student_course WHERE sid = ? AND cid = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "ss", $sid, $cid);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) > 0) {
    echo "您已选过该课程。";
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit;
}
mysqli_stmt_close($stmt);

// 读取该学生所有的卡，按 card_id 升序
$sql = "SELECT id, card_id, card_count, used_count FROM student_card WHERE sid = ? ORDER BY card_id ASC";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "s", $sid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$can_select = false;
$selected_card_id = null;

while ($row = mysqli_fetch_assoc($result)) {
    $student_card_id = $row['id'];
    $card_id = $row['card_id'];
    $card_count = $row['card_count'];
    $used_count = $row['used_count'];

    // 读card表，取allowed_courses和max_courses
    $sql2 = "SELECT allowed_courses, max_courses FROM card WHERE id = ?";
    $stmt2 = mysqli_prepare($db, $sql2);
    mysqli_stmt_bind_param($stmt2, "i", $card_id);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_bind_result($stmt2, $allowed_courses_str, $max_courses);
    mysqli_stmt_fetch($stmt2);
    mysqli_stmt_close($stmt2);

    // allowed_courses是逗号分隔字符串，转数组
    $allowed_courses = array_map('trim', explode(',', $allowed_courses_str));

    if (!is_array($allowed_courses)) {
        // 解析失败跳过此卡
        continue;
    }

    // 注意 $cid 是字符串，allowed_courses是字符串数组，严格比较最好统一类型
    if (in_array((string)$cid, $allowed_courses, true)) {
        // 判断剩余选课额度
        if (($card_count * $max_courses - $used_count) > 0) {
            // 允许选课，更新used_count+1
            $new_used_count = $used_count + 1;
            $update_sql = "UPDATE student_card SET used_count = ? WHERE id = ?";
            $update_stmt = mysqli_prepare($db, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "ii", $new_used_count, $student_card_id);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);

            $can_select = true;
            $selected_card_id = $card_id;
            break; // 找到合适卡后跳出循环
        }
    }
}

mysqli_stmt_close($stmt);

if (!$can_select) {
    echo "套餐额度不足，无法选课。";
    mysqli_close($db);
    exit;
}

// 追加选课记录
$sql = "INSERT INTO student_course (sid, cid, score, status) VALUES (?, ?, NULL, 'N')";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "ss", $sid, $cid);
if (mysqli_stmt_execute($stmt)) {
    echo "选课成功！";
} else {
    echo "选课失败，请稍后重试。";
}
mysqli_stmt_close($stmt);
mysqli_close($db);
?>
