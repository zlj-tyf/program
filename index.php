<?php
session_start();

$wrong = '';
if (isset($_GET["retry"])) {
    $wrong = '<div class="inputbox">
                <span style="color:#df3a01;font-size:10px;margin:10px;display:block">用户名或密码错误</span>
              </div>';
}

// 获取之前的选择，优先 POST，然后 GET，默认 student/id
$role = 'student';
if (isset($_POST['role'])) {
    $role = $_POST['role'] === 'admin' ? 'admin' : 'student';
} elseif (isset($_GET['role'])) {
    $role = $_GET['role'] === 'admin' ? 'admin' : 'student';
}

$login_type = 'id';
if (isset($_POST['login_type'])) {
    $login_type = $_POST['login_type'] === 'name' ? 'name' : 'id';
} elseif (isset($_GET['login_type'])) {
    $login_type = $_GET['login_type'] === 'name' ? 'name' : 'id';
}

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    $tabStudentClass = $role === 'student' ? 'active' : '';
    $tabAdminClass = $role === 'admin' ? 'active' : '';

    $checkedId = $login_type === 'id' ? 'checked' : '';
    $checkedName = $login_type === 'name' ? 'checked' : '';

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
            display:flex;
            justify-content:spae-between;
            align:center;
            display: flex;
            margin-bottom: 0px;
            cursor: pointer;
        }
        .tab div {
            width:50%;
            text-align:center;
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
            align:center;
            align-content:center;
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
            <span>统一比赛申报系统</span>
        </div>
        <div align=center class="subtitle">用户登录</div>

        <div class="tab">
            <div id="tab-student" class="$tabStudentClass" onclick="showForm('student')">学生登录</div>
            <div id="tab-admin" class="$tabAdminClass" onclick="showForm('admin')">管理员登录</div>
        </div>

        <div class="form-section">
            <form id="login-form" action="./login.php" method="post">
                <input type="hidden" name="role" id="role" value="$role" />

                <div class="radio-group">
                    <label><input type="radio" name="login_type" value="id" $checkedId> 通过学号/ID登录</label>
                    <label style="margin-left:flex;"><input type="radio" name="login_type" value="name" $checkedName> 通过用户名登录</label>
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

    if(role === 'student') {
        document.getElementById('tab-student').classList.add('active');
        document.getElementById('tab-admin').classList.remove('active');
    } else {
        document.getElementById('tab-admin').classList.add('active');
        document.getElementById('tab-student').classList.remove('active');
    }
    // 保持登录方式单选状态，不重置
}
</script>
</body>
</html>
END;

    exit();
} else {
    if (isset($_SESSION["admin"])) {
        header("Location: ./admin/");
        exit();
    } else {
        header("Location: ./user/");
        exit();
    }
}
?>
