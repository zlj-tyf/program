<?php
session_start();
require_once '../config/database.php';

// 检查管理员登录
if(!isset($_SESSION["admin"]) || !$_SESSION["login"]){
    header("Location: ../");
    exit();
}

// 状态提示
$statusMsg = '';
$selectedAdminID = 0;
$accessStudent = [];

// 获取所有管理员
$admins = [];
$result = $db->query("SELECT adminID, adminName FROM user_admin ORDER BY adminID");
while($row = $result->fetch_assoc()){
    $admins[] = $row;
}

// 获取所有学生
$students = [];
$result = $db->query("SELECT sid, name FROM student ORDER BY sid");
while($row = $result->fetch_assoc()){
    $students[] = $row;
}

// 处理GET或POST选择的管理员
if(isset($_GET['adminID'])) {
    $selectedAdminID = intval($_GET['adminID']);
} elseif(isset($_POST['adminID'])) {
    $selectedAdminID = intval($_POST['adminID']);
}

// 获取当前管理员已有权限
if($selectedAdminID > 0){
    $stmt = $db->prepare("SELECT access_student FROM user_admin WHERE adminID=?");
    $stmt->bind_param("i", $selectedAdminID);
    $stmt->execute();
    $res = $stmt->get_result();
    if($row = $res->fetch_assoc()){
        $accessStudent = json_decode($row['access_student'], true) ?: [];
    }
    $stmt->close();
}

// 处理提交修改学生权限
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_access'])){
    $submittedStudents = isset($_POST['students']) ? $_POST['students'] : [];

    // 对比是否有修改
    $old = $accessStudent;
    sort($old);
    sort($submittedStudents);

    if($old === $submittedStudents){
        $statusMsg = '未修改';
    } else {
        $jsonData = json_encode($submittedStudents, JSON_UNESCAPED_UNICODE);
        $stmt = $db->prepare("UPDATE user_admin SET access_student=? WHERE adminID=?");
        $stmt->bind_param("si", $jsonData, $selectedAdminID);
        if($stmt->execute()){
            $statusMsg = '保存成功';
            $accessStudent = $submittedStudents; // 更新显示
        } else {
            $statusMsg = '保存失败';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>管理员可访问学生管理</title>
    <style>
        body { font-family: Arial; display: flex; }
        .left { width: 220px; padding: 10px; border-right: 1px solid #ccc; }
        .right { flex: 1; padding: 10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .msg { color: green; margin-bottom: 10px; }
        .admin-link { display: block; margin: 3px 0; text-decoration: none; color: #333; }
        .admin-link:hover { text-decoration: underline; color: blue; }
    </style>
</head>
<body>

<div class="left">
    <h3>选择管理员</h3>
    <form method="POST">
        <label>输入管理员ID:</label><br>
        <input type="number" name="adminID" required value="<?php echo $selectedAdminID; ?>"><br><br>
        <button type="submit">确认</button>
    </form>
    <hr>
    <h4>所有管理员</h4>
    <?php foreach($admins as $admin): ?>
        <a class="admin-link" href="?adminID=<?php echo $admin['adminID']; ?>">
            <?php echo $admin['adminID']." - ".htmlspecialchars($admin['adminName']); ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="right">
    <?php if($selectedAdminID > 0): ?>
    <h3>选择可访问学生</h3>
    <?php if($statusMsg): ?>
        <div class="msg"><?php echo $statusMsg; ?></div>
    <?php endif; ?>
    <form method="POST" id="studentForm">
        <input type="hidden" name="adminID" value="<?php echo $selectedAdminID; ?>">
        <button type="button" id="toggleAll">全选/反选</button>
        <br><br>
        <table>
            <thead>
                <tr>
                    <th>选择</th>
                    <th>学号</th>
                    <th>姓名</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $stu): ?>
                <tr>
                    <td>
                        <input type="checkbox" name="students[]" value="<?php echo $stu['sid']; ?>"
                        <?php echo in_array($stu['sid'], $accessStudent) ? 'checked' : ''; ?>>
                    </td>
                    <td><?php echo htmlspecialchars($stu['sid']); ?></td>
                    <td><?php echo htmlspecialchars($stu['name']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <button type="submit" name="submit_access">保存修改</button>
    </form>

    <script>
        const toggleBtn = document.getElementById('toggleAll');
        toggleBtn.addEventListener('click', () => {
            const checkboxes = document.querySelectorAll('#studentForm input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        });
    </script>
    <?php endif; ?>
</div>

</body>
</html>
