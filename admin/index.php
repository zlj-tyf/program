<?php 
session_start();
if(!isset($_SESSION["admin"])||!$_SESSION["login"]==true){
    header ("HTTP/1.1 302 Moved Temporatily"); 
    header ("Location: "."../"); 
    exit();
}

require_once '../config/database.php';

$adminID = $_SESSION["admin"];
$adminName = "管理员"; // 默认

if ($adminID == 999) {
    $adminName = "超级管理员";
} else {
    $stmt = $db->prepare("SELECT adminName FROM user_admin WHERE adminID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $adminID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $adminName = $row['adminName'];
        }
        $stmt->close();
    }
}

$permissions = [];
if($adminID == 999){
    $permissions = [
        "addStudent","queueStudent","editStudent","editStudentCard",
        "queueCourse","addCourse","modifyCourse",
        "queueChoose","editStudentCourse",
        "queryLog","userManage","changePassword",
        "createAdmin",  // 记得超级管理员拥有创建管理员权限
        "createCard",   // 新增卡片管理权限
        "modifyCard"
    ];
} else {
    $stmt = $db->prepare("SELECT permissions FROM user_admin WHERE adminID = ?");
    $stmt->bind_param("i", $adminID);
    $stmt->execute();
    $result = $stmt->get_result();
    if($row = $result->fetch_assoc()){
        $permissions = explode(',', $row['permissions']);
    }
    $stmt->close();
}

function hasPermission($perm, $permissions){
    return in_array($perm, $permissions);
}
?>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <title>Project Log System - Admin Page</title><script>
        // 判断是否为移动设备
        function isMobile() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }

        if(isMobile()){
            alert("检测到您正在使用移动设备访问，建议在电脑上打开以获得更好的使用体验。");
        }
    </script>
</head>
<body>
<div class="container topnav">
    <div class="logo">
        Project Log System
    </div>
    <div class="userbox" style="float:right">
        你好，管理员 <?php echo htmlspecialchars($adminName); ?> <a href="../logout.php"> 登出</a>
    </div>
</div>
<div class="container main">
    <div class="leftnav">
        <div class="homepage">
            <a href="./welcome.php" target="frame">首页</a>
        </div>

        <?php if(hasPermission("addStudent",$permissions) || hasPermission("queueStudent",$permissions) || hasPermission("editStudent",$permissions)): ?>
        <div class="subtitle">学生管理</div>
        <?php if(hasPermission("addStudent",$permissions)): ?>
        <div class="item"><a href="./addStudent.php" target="frame">新增学生</a></div>
        <?php endif; ?>
        <?php if(hasPermission("queueStudent",$permissions)): ?>
        <div class="item"><a href="./queueStudent.php" target="frame">查询学生</a></div>
        <?php endif; ?>
        <?php if(hasPermission("editStudent",$permissions)): ?>
        <div class="item"><a href="./editStudent.php" target="frame">编辑学生</a></div>
        <?php endif; ?>
        <?php if(hasPermission("editStudentCard",$permissions)): ?>
        <div class="item"><a href="./editStudentCard.php" target="frame">编辑学生卡片</a></div>
        <?php endif; ?>
        <?php endif; ?>

        <?php if(hasPermission("queueCourse",$permissions) || hasPermission("addCourse",$permissions) || hasPermission("modifyCourse",$permissions)): ?>
        <div class="subtitle">课程管理</div>
        <?php if(hasPermission("queueCourse",$permissions)): ?>
        <div class="item"><a href="./queueCourse.php" target="frame">课程查询</a></div>
        <?php endif; ?>
        <?php if(hasPermission("addCourse",$permissions)): ?>
        <div class="item"><a href="./addCourse.php" target="frame">新增课程</a></div>
        <?php endif; ?>
        <?php if(hasPermission("modifyCourse",$permissions)): ?>
        <div class="item"><a href="./modifyCourse.php" target="frame">修改课程</a></div>
        <?php endif; ?>
        <?php endif; ?>

        <?php if(hasPermission("queueChoose",$permissions) || hasPermission("editStudentCourse",$permissions) || hasPermission("queryLog",$permissions)): ?>
        <div class="subtitle">选课管理</div>
        <?php if(hasPermission("queueChoose",$permissions)): ?>
        <div class="item"><a href="./queueChoose.php" target="frame">学生选课</a></div>
        <?php endif; ?>
        <?php if(hasPermission("editStudentCourse",$permissions)): ?>
        <div class="item"><a href="./editStudentCourse.php" target="frame">选课修改</a></div>
        <?php endif; ?>
        <?php if(hasPermission("queryLog",$permissions)): ?>
        <div class="item"><a href="./queryLog.php" target="frame">学生日志查询与修改</a></div>
        <?php endif; ?>
        <?php endif; ?>

        <!-- 新增卡片管理菜单 -->
        <?php if(hasPermission("createCard",$permissions) || hasPermission("modifyCard",$permissions)): ?>
        <div class="subtitle">卡片管理</div>
        <?php if(hasPermission("createCard",$permissions)): ?>
        <div class="item"><a href="./createCard.php" target="frame">创建卡片</a></div>
        <?php endif; ?>
        <?php if(hasPermission("modifyCard",$permissions)): ?>
        <div class="item"><a href="./modifyCard.php" target="frame">修改卡片</a></div>
        <?php endif; ?>
        <?php endif; ?>

        <div class="subtitle">系统设置</div>
        <?php if(hasPermission("userManage",$permissions)): ?>
        <div class="item"><a href="./userManage.php" target="frame">用户管理</a></div>
        <?php endif; ?>
        <?php if(hasPermission("changePassword",$permissions)): ?>
        <div class="item"><a href="./changePassword.php" target="frame">修改密码</a></div>
        <?php endif; ?>
        <?php if($_SESSION["admin"] == 999): ?>
        <div class="item"><a href="./createAdmin.php" target="frame">创建管理员</a></div>
        <div class="item"><a href="./editAdmin.php" target="frame">编辑管理员功能权限</a></div>
        <div class="item"><a href="./editAdminAccess.php" target="frame">编辑管理员学生权限</a></div>
        <?php endif; ?>
    </div>

    <div class="content">
        <iframe name="frame" frameborder="0" width="100%"  scrolling="yes"  src="./welcome.php"></iframe>
    </div>
</div>

<!-- 省略原有的反馈悬浮窗和脚本 -->


<!-- 以下是你原先的反馈悬浮窗及样式和脚本，保持不变 -->

<style>
#feedbackBtn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background-color: #007bff;
    color: white;
    padding: 12px 18px;
    border-radius:  20px;
    font-size: 24px;
    cursor: pointer;
    z-index: 9999;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

#feedbackBtn input:focus {
    outline: none; /* 去掉默认蓝色边框 */
    border: 2px solid #007bff; /* 蓝色边框 */
    box-shadow: 0 0 6px rgba(0, 123, 255, 0.6); /* 蓝色外阴影 */
    transition: all 0.2s ease-in-out; /* 平滑过渡 */
}

#feedbackForm {
    display: none;
    position: fixed;
    bottom: 80px;
    right: 30px;
    width: 360px;
    background: white;
    border: 1px solid #ccc;
    padding: 15px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    z-index: 9999;
    border-radius: 8px;
}

#feedbackForm input, #feedbackForm textarea {
    width: 100%;
    margin-bottom: 10px;
    padding: 6px;
    font-size: 14px;
    box-sizing: border-box;
}

#feedbackForm textarea {
    height: 80px;
    resize: vertical;
}

#feedbackForm button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 15px;
    cursor: pointer;
    border-radius: 4px;
}

#feedbackForm button:hover {
    background-color: #0056b3;
}

#errorMsg {
    color: red;
    font-size: 13px;
    margin-bottom: 10px;
}
</style>

<div id="feedbackBtn" title="反馈问题">问题反馈</div>
<div id="feedbackForm">
    <form id="formFeedback">
        <div id="errorMsg" style="color:red;"></div>
        <div id="successMsg" style="color:green;"></div>

        <label>姓名：</label>
        <input type="text" name="name" id="name" placeholder="请输入姓名" required>

        <!-- <label>学号：</label> -->
        <!-- <input type="text" name="student_id" id="student_id" placeholder="请输入学号" required> -->
        <input type="hidden" name="student_id" id="student_id" 
       value="<?php echo htmlspecialchars(!empty($_SESSION['admin']) ? $_SESSION['admin'] : (!empty($_SESSION['user']) ? $_SESSION['user'] : '')); ?>">

        <label>手机号：</label>
        <input type="text" name="phone" id="phone" placeholder="请输入手机号">

        <label>电子邮箱：</label>
        <input type="email" name="email" id="email" placeholder="请输入邮箱">

        <label>问题描述：</label>
        <textarea name="feedback" id="feedback" placeholder="请输入问题描述" required></textarea>

        <button type="submit">提交反馈</button>
    </form>
</div>

<script>
// 切换表单显示
document.getElementById('feedbackBtn').onclick = function() {
    var form = document.getElementById('feedbackForm');
    form.style.display = form.style.display === 'block' ? 'none' : 'block';
};

// 表单验证
function validateForm() {
    var name = document.getElementById('name').value.trim();
    var student_id = document.getElementById('student_id').value.trim();
    var phone = document.getElementById('phone').value.trim();
    var email = document.getElementById('email').value.trim();
    var feedback = document.getElementById('feedback').value.trim();
    var errorMsg = document.getElementById('errorMsg');
    errorMsg.innerText = '';

    if (!name) { errorMsg.innerText = '姓名为必填项'; return false; }
    if (!student_id) { errorMsg.innerText = '学号为必填项'; return false; }
    if (!phone ) { errorMsg.innerText = '手机号为必填项'; return false; }
    if (!email) { errorMsg.innerText = '电子邮箱为必填项'; return false; }
    if (!feedback) { errorMsg.innerText = '问题描述为必填项'; return false; }
    return true;
}

// 拦截提交事件
document.getElementById('formFeedback').addEventListener('submit', function(e) {
    e.preventDefault(); // 阻止表单默认提交

    if (!validateForm()) return;

    var formData = new FormData(this);

    fetch('send_feedback.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text()) // 如果返回 JSON 可改成 .json()
    .then(data => {
        document.getElementById('successMsg').innerText = data;
        document.getElementById('errorMsg').innerText = '';
        document.getElementById('formFeedback').reset();
    })
    .catch(error => {
        document.getElementById('errorMsg').innerText = '提交失败，请稍后重试。';
        console.error(error);
    });
});
</script>

<div class="container footer">
    <span>Project Log System<br/>Opensource based on MIT licence.</span>
</div>

</body>
</html>
