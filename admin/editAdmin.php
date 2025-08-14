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
$permissions = [];

// 所有权限列表，对应前端菜单
$allPermissions = [
    "addStudent"=>"新增学生",
    "queueStudent"=>"查询学生",
    "editStudent"=>"编辑学生",
    "editStudentCard"=>"编辑学生卡片",
    "queueCourse"=>"课程查询",
    "addCourse"=>"新增课程",
    "modifyCourse"=>"修改课程",
    "queueChoose"=>"学生选课",
    "editStudentCourse"=>"选课修改",
    "queryLog"=>"学生日志查询与修改",
    "createCard"=>"创建卡片",
    "modifyCard"=>"修改卡片",
    "userManage"=>"用户管理",
    "changePassword"=>"修改密码",
    "createAdmin"=>"创建管理员",
    "editAdmin"=>"编辑管理员功能权限",
    "editAdminAccess"=>"编辑管理员学生权限"
];

// 获取所有管理员
$admins = [];
$result = $db->query("SELECT adminID, adminName FROM user_admin ORDER BY adminID");
while($row = $result->fetch_assoc()){
    $admins[] = $row;
}

// 处理GET或POST选择的管理员
if(isset($_GET['adminID'])) {
    $selectedAdminID = intval($_GET['adminID']);
} elseif(isset($_POST['adminID'])) {
    $selectedAdminID = intval($_POST['adminID']);
}

// 获取当前管理员已有权限
if($selectedAdminID > 0){
    $stmt = $db->prepare("SELECT permissions FROM user_admin WHERE adminID=?");
    $stmt->bind_param("i", $selectedAdminID);
    $stmt->execute();
    $res = $stmt->get_result();
    if($row = $res->fetch_assoc()){
        $permissions = explode(',', $row['permissions'] ?? '');
    }
    $stmt->close();
}

// 处理提交修改权限
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_permissions'])){
    $submittedPermissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

    $old = $permissions;
    sort($old);
    sort($submittedPermissions);

    if($old === $submittedPermissions){
        $statusMsg = '未修改';
    } else {
        $newPerm = implode(',', $submittedPermissions);
        $stmt = $db->prepare("UPDATE user_admin SET permissions=? WHERE adminID=?");
        $stmt->bind_param("si", $newPerm, $selectedAdminID);
        if($stmt->execute()){
            $statusMsg = '修改成功';
            $permissions = $submittedPermissions; // 更新显示
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
    <title>管理员权限管理</title>
</head>
<body>
<div style="display:flex;">
    <div style="width:220px; padding:10px; border-right:1px solid #ccc;">
        <h3>选择管理员</h3>
        <form method="POST">
            <label>输入管理员ID:</label><br>
            <input type="number" name="adminID" required value="<?php echo $selectedAdminID; ?>"><br><br>
            <button type="submit">确认</button>
        </form>
        <hr>
        <h4>所有管理员</h4>
        <?php foreach($admins as $admin): ?>
            <a style="display:block; margin:3px 0;" href="?adminID=<?php echo $admin['adminID']; ?>">
                <?php echo $admin['adminID']." - ".htmlspecialchars($admin['adminName']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div style="flex:1; padding:10px;">
        <?php if($selectedAdminID > 0): ?>
            <h3>设置管理员权限</h3>
            <?php if($statusMsg): ?>
                <div style="color:green; margin-bottom:10px;"><?php echo $statusMsg; ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="adminID" value="<?php echo $selectedAdminID; ?>">
                <?php foreach($allPermissions as $key=>$label): ?>
                    <label>
                        <input type="checkbox" name="permissions[]" value="<?php echo $key; ?>"
                        <?php echo in_array($key, $permissions) ? 'checked' : ''; ?>>
                        <?php echo $label; ?>
                    </label><br>
                <?php endforeach; ?>
                <br>
                <button type="submit" name="submit_permissions">保存修改</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
