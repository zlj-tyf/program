<?php
require_once("../config/database.php");
session_start();
$sid = $_SESSION['user'];
$cid = $_GET['cid'];

// 开启事务
mysqli_begin_transaction($db, MYSQLI_TRANS_START_READ_WRITE);

try {
    // 1. 获取当前学生的所有卡片（按 card_id 升序）
    $sql_cards = "SELECT * FROM student_card WHERE sid = '$sid' ORDER BY card_id ASC FOR UPDATE";
    $res_cards = mysqli_query($db, $sql_cards);

    $found = false; // 标记是否找到可扣减的卡片

    if ($res_cards && mysqli_num_rows($res_cards) > 0) {
        while ($card = mysqli_fetch_assoc($res_cards)) {
            $card_id = $card['card_id'];

            // 2. 这里改成 WHERE id=xxx
            $sql_card_info = "SELECT allowed_courses, max_courses FROM card WHERE id = '$card_id'";
            $res_card_info = mysqli_query($db, $sql_card_info);

            if ($res_card_info && $info = mysqli_fetch_assoc($res_card_info)) {
                $allowed_raw = $info['allowed_courses'];

                // 优先尝试 JSON 解析
                $allowed_courses = json_decode($allowed_raw, true);
                if (!is_array($allowed_courses)) {
                    // 如果不是 JSON，就按逗号切分
                    $allowed_courses = array_map('trim', explode(',', $allowed_raw));
                }

                $used_count = intval($card['used_count']);

                // 检查该课程是否在 allowed_courses 内
                if (is_array($allowed_courses) && in_array($cid, $allowed_courses)) {
                    if ($used_count > 0) {
                        // 扣减学生卡片的 used_count
                        $new_count = $used_count - 1;
                        $update_sql = "UPDATE student_card SET used_count = $new_count WHERE sid='$sid' AND card_id='$card_id'";
                        if (!mysqli_query($db, $update_sql)) {
                            throw new Exception("扣减卡片失败: " . mysqli_error($db));
                        }

                        // 3. 执行退选功能
                        $del_sql = "DELETE FROM student_course WHERE sid='$sid' AND cid='$cid' AND score IS NULL";
                        if (!mysqli_query($db, $del_sql)) {
                            throw new Exception("退选课程失败: " . mysqli_error($db));
                        }

                        $found = true;
                        break; // 找到可扣减的卡片后退出循环
                    }
                }
            }
        }
    }

    if ($found) {
        mysqli_commit($db);
        $msg = "提示：操作成功！";
    } else {
        throw new Exception("错误：未找到可扣减的卡片，请通过页面右下角的反馈按钮联系老师。");
    }

} catch (Exception $e) {
    // 回滚事务
    mysqli_rollback($db);
    $msg = $e->getMessage();
}

mysqli_close($db);
?>
<h3 style='text-align:center'><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></h3>
<div style="width: 90%;height: 55px;margin: 50px">
    <div style="margin: 0 auto;width: 90px;height: 30px;background-color: #117700">
        <a style="text-decoration: none;padding:3px;color: #f3f3f3;text-align: center;display: block" href="#" onclick="javascript:history.back(-1);">返回</a>
    </div>
</div>
