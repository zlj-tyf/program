<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// require_once("./config/database.php");
require_once("../config/database.php"); // 根据实际路径调整

// 初始化提示信息和结果变量
$message = '';
$result_html = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 判断是查询还是重置密码（根据提交的按钮名字判断）
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === '查询学生') {
            // 查询学生功能
            $sid = isset($_POST['sid']) ? mysqli_real_escape_string($db, trim($_POST['sid'])) : '';
            $name = isset($_POST['name']) ? mysqli_real_escape_string($db, trim($_POST['name'])) : '';

            $where = [];
            if ($sid !== '') {
                $where[] = "sid LIKE '%$sid%'";
            }
            if ($name !== '') {
                $where[] = "name LIKE '%$name%'";
            }
            $where_sql = '';
            if (count($where) > 0) {
                $where_sql = "WHERE " . implode(' AND ', $where);
            }

            $sql = "SELECT sid, name, current_grade, card_type FROM student $where_sql ORDER BY sid ASC";
            $res = mysqli_query($db, $sql);
            if (!$res) {
                $message = "查询失败：" . mysqli_error($db);
            } else {
                $rows = mysqli_num_rows($res);
                if ($rows === 0) {
                    $message = "没有找到匹配的学生。";
                } else {
                    $result_html .= "<h3>查询结果（共 $rows 条）</h3>";
                    $result_html .= '<table border="1" cellpadding="5" cellspacing="0">';
                    $result_html .= '<tr><th>学号</th><th>姓名</th><th>当前年级</th><th>卡种类</th></tr>';
                    while ($row = mysqli_fetch_assoc($res)) {
                        $result_html .= '<tr>';
                        $result_html .= '<td>' . htmlspecialchars($row['sid']) . '</td>';
                        $result_html .= '<td>' . htmlspecialchars($row['name']) . '</td>';
                        $result_html .= '<td>' . htmlspecialchars($row['current_grade']) . '</td>';
                        $result_html .= '<td>' . htmlspecialchars($row['card_type']) . '</td>';
                        $result_html .= '</tr>';
                    }
                    $result_html .= '</table>';
                }
                mysqli_free_result($res);
            }
        } elseif ($action === '重置密码') {
            // 重置密码功能
            $sid = isset($_POST['reset_sid']) ? mysqli_real_escape_string($db, trim($_POST['reset_sid'])) : '';

            if ($sid === '') {
                $message = "错误：请输入要重置密码的学号。";
            } else {
                $new_pwd = md5('123456');
                $sql = "UPDATE user_student SET pwd = '$new_pwd' WHERE sid = '$sid'";
                $result = mysqli_query($db, $sql);
                if ($result && mysqli_affected_rows($db) > 0) {
                    $message = "学号为 <b>" . htmlspecialchars($sid) . "</b> 的学生密码已重置为123456。";
                } else {
                    $message = "密码重置失败，可能学号不存在或密码未变化。错误：" . mysqli_error($db);
                }
            }
        }
    }
}

mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="./css/fun.css" />
    <title>系统设置 >> 用户管理</title>
</head>
<body>
<h3 class="subtitle">系统设置 >> 用户管理</h3>
<p>提示：作为管理员，你可以激活学生账户，为学生账户重置密码。</p>

<!-- 查询学生表单 -->
<form method="post">
    <div class="inputbox"><span>学号：</span><input name="sid" type="text" value="<?php echo isset($_POST['sid']) ? htmlspecialchars($_POST['sid']) : '' ?>"></div>
    <div class="inputbox"><span>姓名：</span><input name="name" type="text" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>"></div>
    <div class="clickbox clearfloat"><input type="submit" name="action" value="查询学生"></div>
    <div class="redbox clickbox"><input type="reset" value="清除"></div>
</form>

<hr style="margin:30px 0;" />

<!-- 重置密码表单 -->
<form method="post">
    <div class="inputbox"><span>重置密码学号：</span><input name="reset_sid" type="text" value="<?php echo isset($_POST['reset_sid']) ? htmlspecialchars($_POST['reset_sid']) : '' ?>" required></div>
    <div class="clickbox clearfloat"><input type="submit" name="action" value="重置密码"></div>
</form>

<!-- 显示结果或提示 -->
<div style="margin-top: 30px;">
    <?php if ($message !== ''): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <?php echo $result_html; ?>
</div>

</body>
</html>
