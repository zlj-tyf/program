<?php
require_once("../../config/database.php");

// 只允许POST提交
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "非法请求";
    exit;
}

$selection = $_POST['selection'] ?? [];

if (!is_array($selection)) {
    echo "数据格式错误";
    exit;
}

// 开启事务，提高效率和安全
mysqli_begin_transaction($db);

try {
    // 先清空所有学生选课表中对应学生的选课记录
    // 也可以选择只清除传入学生的选课，或者更细粒度优化
    $allSids = array_keys($selection);
    if (!empty($allSids)) {
        // 构造批量删除条件
        $sidsEscaped = array_map(function($sid) use ($db) {
            return "'" . mysqli_real_escape_string($db, $sid) . "'";
        }, $allSids);
        $sidsStr = implode(",", $sidsEscaped);
        $delSql = "DELETE FROM student_course WHERE sid IN ($sidsStr)";
        mysqli_query($db, $delSql);
    }

    // 插入新的选课数据
    $insertValues = [];
    foreach ($selection as $sid => $cids) {
        if (!is_array($cids)) continue;
        $sidEscaped = mysqli_real_escape_string($db, $sid);
        foreach ($cids as $cid) {
            $cidEscaped = mysqli_real_escape_string($db, $cid);
            // 插入时 score 默认 NULL, status 默认 NULL 或自定义
            $insertValues[] = "('$sidEscaped','$cidEscaped',NULL,NULL)";
        }
    }

    if (!empty($insertValues)) {
        $insertSql = "INSERT INTO student_course (sid,cid,score,status) VALUES " . implode(",", $insertValues);
        mysqli_query($db, $insertSql);
    }

    mysqli_commit($db);
    echo "学生选课信息已成功更新。";
} catch (Exception $e) {
    mysqli_rollback($db);
    echo "更新失败：" . $e->getMessage();
}

mysqli_close($db);
?>
<div style="margin: 20px;">
    <a href="../editStudentCourse.php">返回学生选课管理</a>
</div>
