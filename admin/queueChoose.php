<?php
include '../config/database.php';

// // 显示所有错误
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// 状态映射
$statusMap = [
    '文书提交' => 1,
    '文书修改' => 2,
    '文书定稿' => 3,
    '项目提交' => 4
];
$reverseStatusMap = array_flip($statusMap); // 数字转中文

// 处理状态保存
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_status'])) {
    $sid = $_POST['sid'];
    $statusArr = $_POST['status'] ?? [];

    try {
        $stmt = $db->prepare("UPDATE student_course SET status=? WHERE sid=? AND cid=?");
        if(!$stmt) throw new Exception("SQL Prepare 错误: " . $db->error);

        foreach($statusArr as $cid => $statusText) {
            $statusNum = $statusMap[$statusText] ?? 0;
            $stmt->bind_param("isi", $statusNum, $sid, $cid);
            if(!$stmt->execute()) throw new Exception("SQL Execute 错误: " . $stmt->error);
        }
        $stmt->close();
        $msg = "保存成功！";
    } catch(Exception $e){
        $msg = '<span style="color:red;">'. $e->getMessage() .'</span>';
    }
}

// 获取所有学生用于 sidebar
$students = [];
try {
    $res = $db->query("SELECT sid, name FROM student ORDER BY sid ASC");
    if(!$res) throw new Exception("SQL 错误: ".$db->error);
    while($row = $res->fetch_assoc()) {
        $students[] = $row;
    }
} catch(Exception $e) {
    $msg = '<span style="color:red;">'.$e->getMessage().'</span>';
}

// 处理选中的学生
$selectedSID = '';
if(isset($_GET['sid'])) {
    $selectedSID = $_GET['sid'];
} elseif(isset($_POST['sid'])) {
    $selectedSID = $_POST['sid'];
}

// 获取所选学生的课程
$courseRows = [];
if($selectedSID) {
    try {
        $stmt = $db->prepare("SELECT sc.sid, sc.cid, sc.status, s.name AS student_name, c.competition_name 
                              FROM student_course sc
                              JOIN student s ON sc.sid = s.sid
                              JOIN course c ON sc.cid = c.cid
                              WHERE sc.sid=?");
        if(!$stmt) throw new Exception("SQL Prepare 错误: " . $db->error);
        $stmt->bind_param("s", $selectedSID);
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()) {
            // 将数字转为中文显示
            $row['status_text'] = $reverseStatusMap[$row['status']] ?? '';
            $courseRows[] = $row;
        }
        $stmt->close();
    } catch(Exception $e) {
        $msg = '<span style="color:red;">'.$e->getMessage().'</span>';
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
        .sidebar { width: 220px; padding: 15px; border-right: 1px solid #ccc; }
        .content { flex: 1; padding: 15px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        .status-msg { color: green; font-weight: bold; }
        .sidebar a { display: block; margin: 3px 0; text-decoration: none; color: #333; }
        .sidebar a:hover { text-decoration: underline; }
    </style>
    <script>
        function autoSubmit(selectEl){
            selectEl.form.submit();
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <h3>选择学生</h3>
        <form method="POST">
            <input type="text" name="sid" required value="<?php echo htmlspecialchars($selectedSID); ?>">
            <button type="submit">确认</button>
        </form>
        <hr>
        <h4>所有学生</h4>
        <?php foreach($students as $stu): ?>
            <a href="?sid=<?php echo urlencode($stu['sid']); ?>"><?php echo htmlspecialchars($stu['sid'] . " - " . $stu['name']); ?></a>
        <?php endforeach; ?>
    </div>

    <div class="content">
        <h2>学生课程管理（自动保存）</h2>
        <?php if(!empty($msg)) echo '<p class="status-msg">'.$msg.'</p>'; ?>
        <?php if(!$selectedSID): ?>
            <p>请在左侧选择学生。</p>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="sid" value="<?php echo htmlspecialchars($selectedSID); ?>">
                <input type="hidden" name="save_status" value="1">
                <table>
                    <thead>
                        <tr>
                            <th>学生姓名</th>
                            <th>比赛名称</th>
                            <th>状态</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($courseRows)): ?>
                            <tr><td colspan="3">该学生没有报名课程。</td></tr>
                        <?php else: ?>
                            <?php foreach($courseRows as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['competition_name']); ?></td>
                                <td>
                                    <select name="status[<?php echo $row['cid']; ?>]" onchange="autoSubmit(this)">
                                        <?php foreach($statusMap as $text => $num): ?>
                                        <option value="<?php echo $text; ?>" <?php if($row['status_text']==$text) echo 'selected'; ?>>
                                            <?php echo $text; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
