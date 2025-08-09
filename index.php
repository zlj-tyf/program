<?php
session_start();
$wrong = '';
if(isset($_GET["retry"])){
    $wrong = '<div class="inputbox">
                <span style="color:#df3a01;font-size:10px;margin:10px;display:block">用户名或密码错误</span>
              </div>';
}
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    print <<<END
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="stylesheet" href="static/login.css" type="text/css" media="all" />
    <title>Login Page - Project Log System</title>
    <style>
        .tab {
            display: flex;
            margin-bottom: 20px;
            cursor: pointer;
        }
        .tab div {
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-bottom: none;
            background: #f0f0f0;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
        }
        .tab .active {
            background: white;
            font-weight: bold;
            border-bottom: 1px solid white;
        }
        .form-section {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 0 5px 5px 5px;
            background: white;
        }
        .inputbox {
            margin-bottom: 15px;
        }
        .radio-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="loginbox">
        <div class="title">
            <span>Project Log System</span>
        </div>
        <div class="subtitle">用户登录</div>

        <div class="tab">
            <div id="tab-student" class="active" onclick="showForm('student')">学生登录</div>
            <div id="tab-admin" onclick="showForm('admin')">管理员登录</div>
        </div>

        <div class="form-section">
            <form id="login-form" action="./login.php" method="post">
                <input type="hidden" name="role" id="role" value="student" />

                <div class="radio-group">
                    <label><input type="radio" name="login_type" value="id" checked> 用ID登录</label>
                    <label style="margin-left:20px;"><input type="radio" name="login_type" value="name"> 用用户名登录</label>
                </div>

                <div class="inputbox">
                    <span>帐号</span>
                    <input name="user" required type="text" autocomplete="username" />
                </div>
                <div class="inputbox">
                    <span>密码</span>
                    <input name="pass" type="password" autocomplete="current-password" />
                </div>
                <div class="submitbox">
                    <input name="submit" type="submit" value="提交" />
                </div>
                $wrong
            </form>
        </div>
    </div>
    <div class="footer">Opensource based on MIT licence.</div>

<script>
function showForm(role) {
    document.getElementById('role').value = role;
    // 切换后重置单选，默认用ID登录
    const radios = document.getElementsByName('login_type');
    for(let r of radios) {
        r.checked = false;
    }
    radios[0].checked = true; // 默认用ID登录

    if(role === 'student') {
        document.getElementById('tab-student').classList.add('active');
        document.getElementById('tab-admin').classList.remove('active');
    } else {
        document.getElementById('tab-admin').classList.add('active');
        document.getElementById('tab-student').classList.remove('active');
    }
}
</script>
</body>
</html>
END;

exit();
} else {
    if(isset($_SESSION["admin"])) {
        header("Location: ./admin/");
        exit();
    } else {
        header("Location: ./user/");
        exit();
    }
}
?>
