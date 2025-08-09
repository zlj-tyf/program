<?php
session_start();
if(!isset($_SESSION["login"]) || !isset($_SESSION["admin"]) || $_SESSION["admin"] != 999){
    echo "权限不足";
    exit();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8" />
<title>创建管理员</title>
<style>
  .permissions label {
      display: block;
      margin: 4px 0;
  }
  .btn {
      margin-top: 12px;
      padding: 6px 12px;
  }
</style>
<script>
function submitForm() {
    let form = document.getElementById('adminForm');
    let formData = new FormData(form);

    fetch('fun/create_admin.php', {
        method: 'POST',
        body: formData,
    }).then(async res => {
        const text = await res.text();
        try {
            const data = JSON.parse(text);
            console.log("服务器返回数据:", data);
            alert(data.message || (data.success ? "创建成功" : "创建失败"));
            if(data.debug) {
                console.error("调试信息:", data.debug);
                alert("调试信息请查看控制台");
            }
            if(data.success) form.reset();
        } catch (e) {
            console.error("解析JSON失败，服务器返回内容：", text);
            alert("服务器返回非JSON内容，可能发生错误，具体看控制台");
        }
    }).catch(() => alert("请求失败"));
    return false;
}

</script>
</head>
<body>
<h2>创建管理员账户</h2>
<form id="adminForm" onsubmit="return submitForm();">
    <label>用户名: <input type="text" name="adminName" required autocomplete="username" /></label>
    <label>密码: <input type="password" name="pwd" required autocomplete="current-password" /></label>
    <fieldset class="permissions">
    <legend>访问权限（勾选允许访问的功能）</legend>
    <label><input type="checkbox" name="permissions[]" value="addStudent" /> 新增学生</label>
    <label><input type="checkbox" name="permissions[]" value="queueStudent" /> 学生列表</label>
    <label><input type="checkbox" name="permissions[]" value="editStudent" /> 编辑学生</label>
    <label><input type="checkbox" name="permissions[]" value="queueCourse" /> 课程列表</label>
    <label><input type="checkbox" name="permissions[]" value="addCourse" /> 新增课程</label>
    <label><input type="checkbox" name="permissions[]" value="modifyCourse" /> 编辑课程</label>
    <label><input type="checkbox" name="permissions[]" value="queueChoose" /> 选课列表</label>
    <label><input type="checkbox" name="permissions[]" value="editStudentCourse" /> 编辑学生课程</label>
    <label><input type="checkbox" name="permissions[]" value="queryLog" /> 日志查询</label>
    <!-- 新增卡片管理权限 -->
    <label><input type="checkbox" name="permissions[]" value="createCard" /> 创建卡片</label>
    <label><input type="checkbox" name="permissions[]" value="modifyCard" /> 修改卡片</label>
    <label><input type="checkbox" name="permissions[]" value="userManage" /> 管理员管理</label>
    <label><input type="checkbox" name="permissions[]" value="changePassword" /> 修改密码</label>
    <!-- <label><input type="checkbox" name="permissions[]" value="createAdmin" /> 创建管理员</label> -->

</fieldset>
    <button type="submit" class="btn">创建</button>
</form>
</body>
</html>
