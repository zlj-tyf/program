<?php
include '../config/database.php';
session_start();

$adminID = $_SESSION["admin"];

$statusMap = [
    '文书提交' => 1,
    '文书修改' => 2,
    '文书定稿' => 3,
    '项目提交' => 4
];
$reverseStatusMap = array_flip($statusMap);

$msg = '';
$sql_error = '';

// 获取管理员可访问学生
$accessSIDs = [];
try {
    $stmt = $db->prepare("SELECT access_student FROM user_admin WHERE adminID=?");
    $stmt->bind_param("s", $adminID);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $accessSIDs = array_map('intval', json_decode($row['access_student'], true));
    }
    $stmt->close();
} catch (Exception $e) {
    $sql_error .= "获取管理员可访问学生失败: " . $e->getMessage() . "<br>";
}

// 搜索
$searchName = '';
if (isset($_POST['search_name'])) {
    $searchName = trim($_POST['search_name']);
}

// 获取可访问学生
$students = [];
if (!empty($accessSIDs)) {
    try {
        $in = implode(',', array_fill(0, count($accessSIDs), '?'));
        $types = str_repeat('i', count($accessSIDs));
        $sql = "SELECT sid, name FROM student WHERE sid IN ($in)";
        if ($searchName) {
            $sql .= " AND name LIKE ?";
        }
        $stmt = $db->prepare($sql);
        $params = $accessSIDs;
        if ($searchName) {
            $types .= 's';
            $params[] = "%$searchName%";
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $students[] = $row;
        }
        $stmt->close();
    } catch (Exception $e) {
        $sql_error .= "获取学生列表失败: " . $e->getMessage() . "<br>";
    }
} else {
    $sql_error .= "管理员没有可访问学生<br>";
}

// 处理选中学生
$selectedSID = '';
if (isset($_GET['sid'])) {
    $selectedSID = $_GET['sid'];
} elseif (isset($_POST['sid'])) {
    $selectedSID = $_POST['sid'];
}

// 处理提醒学生查看
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remind_sid'], $_POST['remind_cid'])) {
    $sid = intval($_POST['remind_sid']);
    $cid = intval($_POST['remind_cid']);
    try {
        $stmt = $db->prepare("UPDATE student_course SET lastedit = NOW() WHERE sid = ? AND cid = ?");
        $stmt->bind_param("ii", $sid, $cid);
        $stmt->execute();
        $stmt->close();
        $msg = "已提醒该学生查看！";
    } catch (Exception $e) {
        $sql_error .= "提醒操作失败: " . $e->getMessage() . "<br>";
    }
}

// 保存状态、学科、获奖情况
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sid'], $_POST['status'], $_POST['subject'], $_POST['result']) && !isset($_POST['remind_sid'])) {
    $sid = $_POST['sid'];
    $hasError = false;

    foreach ($_POST['status'] as $cid => $statusText) {
        $statusNum = $statusMap[$statusText] ?? 0;
        $subject = trim($_POST['subject'][$cid] ?? '');
        $result  = trim($_POST['result'][$cid] ?? '');

        try {
            $stmt = $db->prepare("UPDATE student_course SET status=?, subject=?, result=? WHERE sid=? AND cid=?");
            $stmt->bind_param("isssi", $statusNum, $subject, $result, $sid, $cid);
            $stmt->execute();
            $stmt->close();
        } catch (Exception $e) {
            $sql_error .= "更新课程失败: " . $e->getMessage() . "<br>";
            $hasError = true;
        }
    }

    if (!$hasError) {
        $msg = "保存成功！";
    }
}

// 获取学生的课程
$courseRows = [];
if ($selectedSID) {
    try {
        $stmt = $db->prepare("SELECT sc.sid, sc.cid, sc.status, sc.subject, sc.result, sc.lastedit,
                                     s.name AS student_name, c.competition_short_name 
                              FROM student_course sc
                              JOIN student s ON sc.sid = s.sid
                              JOIN course c ON sc.cid = c.cid
                              WHERE sc.sid=?");
        $stmt->bind_param("i", $selectedSID);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $row['status_text'] = $reverseStatusMap[$row['status']] ?? '';
            $courseRows[] = $row;
        }
        $stmt->close();
    } catch (Exception $e) {
        $sql_error .= "获取学生课程失败: " . $e->getMessage() . "<br>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>管理员端学生课程管理</title>
    <style>
        body { display: flex; font-family: Arial, sans-serif; }
        .sidebar { width: 250px; padding: 15px; border-right: 1px solid #ccc; }
        .content { flex: 1; padding: 15px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        .status-msg { color: green; font-weight: bold; }
        .error-msg { color: red; font-weight: bold; }
        .sidebar a { display: block; margin: 3px 0; text-decoration: none; color: #333; }
        .sidebar a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="sidebar">
    <h3>选择学生</h3>
    <form method="POST">
        <input type="text" name="sid" required value="<?php echo htmlspecialchars($selectedSID); ?>">
        <button type="submit">确认</button>
    </form>

    <h4>搜索学生</h4>
    <form method="POST">
        <input type="text" name="search_name" value="<?php echo htmlspecialchars($searchName); ?>" placeholder="输入姓名搜索">
        <button type="submit">搜索</button>
    </form>

    <hr>
    <h4>所有可访问学生</h4>
    <?php
    if (!empty($sql_error)) {
        echo '<p class="error-msg">' . $sql_error . '</p>';
    }
    if (!empty($msg)) {
        echo '<p class="status-msg">' . $msg . '</p>';
    }

    if (empty($students)) {
        echo '<p>无可访问学生</p>';
    } else {
        foreach ($students as $stu) {
            echo '<a href="?sid=' . urlencode($stu['sid']) . '">' . htmlspecialchars($stu['sid'] . " - " . $stu['name']) . '</a>';
        }
    }
    ?>
</div>

<div class="content">
    <h2>学生课程管理</h2>
    <?php if ($selectedSID && empty($courseRows)) echo '<p>该学生没有报名课程。</p>'; ?>
    <?php if (!empty($courseRows)): ?>
    <form method="POST">
        <input type="hidden" name="sid" value="<?php echo htmlspecialchars($selectedSID); ?>">
        <table>
            <thead>
            <tr>
                <th>学生姓名</th>
                <th>比赛简称</th>
                <th>状态</th>
                <th>学科</th>
                <th>获奖情况（建议注明届别年份）</th>
                <th>最后提醒时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($courseRows as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['competition_short_name']); ?></td>
                    <td>
                        <select name="status[<?php echo $row['cid']; ?>]">
                            <?php foreach ($statusMap as $text => $num): ?>
                                <option value="<?php echo $text; ?>" <?php if ($row['status_text'] == $text) echo 'selected'; ?>>
                                    <?php echo $text; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="subject[<?php echo $row['cid']; ?>]" value="<?php echo htmlspecialchars($row['subject']); ?>">
                    </td>
                    <td>
                        <input type="text" name="result[<?php echo $row['cid']; ?>]" value="<?php echo htmlspecialchars($row['result']); ?>">
                    </td>
                    <td>
                        <?php echo $row['lastedit'] ? date('Y-m-d H:i:s', strtotime($row['lastedit'])) : ''; ?>
                    </td>
                    <td>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="remind_sid" value="<?php echo htmlspecialchars($row['sid']); ?>">
                            <input type="hidden" name="remind_cid" value="<?php echo htmlspecialchars($row['cid']); ?>">
                            <button type="submit">提醒学生查看</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">保存修改</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
