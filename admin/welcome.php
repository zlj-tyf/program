<?php
session_start();

// 显示所有 PHP 错误
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php'; // $db 是 mysqli 连接对象

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>tt</title>
</head>
<body>
<div>
    <h1>Project Log System - Admin Page</h1>
    <h2>系统使用要求</h2>
    <p><strong>浏览器推荐使用：</strong>最新版谷歌浏览器 Google Chrome 版本 90 及以上、Microsoft Edge 版本 90 及以上、火狐浏览器 Firefox 版本 88 及以上、苹果Safari 版本 14 及以上。</p>
    
    <p><strong>不推荐使用：</strong>Internet Explorer。</p>

    <p><strong>不推荐使用手机访问本系统。</strong></p>
    
    <p><strong>安全建议：</strong>请避免因安全漏洞导致账号信息泄露；避免在公共或不安全的网络环境下登录系统。</p>

    <p><strong>当前系统仍在测试中，请对相关数据（如填报材料，信息等）及时本地备份。</strong><p/>如有任何问题，请点击页面右下角的反馈按钮，并复制页面中显示的错误信息，并提供联系方式。对造成的不便敬请谅解。</p>
</div>

<?php
$adminID = $_SESSION['admin'] ?? '';

if (!$adminID) {
    echo "<p>管理员未登录。</p>";
    exit;
}

try {
    // 获取 lastlogin 和 access_student
    $stmt = $db->prepare("SELECT lastlogin, access_student FROM user_admin WHERE adminID = ?");
    if (!$stmt) throw new Exception("准备语句失败: " . $db->error);

    $stmt->bind_param("s", $adminID);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();

    $now = date('Y-m-d H:i:s');

    if ($admin) {
        $lastlogin = $admin['lastlogin'];
        $access_student = json_decode($admin['access_student'], true);

        // 只有 lastlogin 原本存在才显示学生修改表格
        if ($lastlogin && !empty($access_student)) {
            $in_list = implode(',', array_map('intval', $access_student)); // 保证数字安全

            $sql = "SELECT sc.sid, sc.cid, sc.lastedit, c.competition_name, s.name
                    FROM student_course sc
                    JOIN course c ON sc.cid = c.cid
                    JOIN student s ON sc.sid = s.sid
                    WHERE sc.sid IN ($in_list)
                      AND sc.lastedit BETWEEN ? AND ?
                    ORDER BY sc.lastedit DESC";

            $stmt2 = $db->prepare($sql);
            if (!$stmt2) throw new Exception("准备查询学生日志失败: " . $db->error);

            $stmt2->bind_param("ss", $lastlogin, $now);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($result2->num_rows > 0) {
                echo '<h2>你的学生在上次登陆至今进行了如下修改，请前往相关页面查看。</h2>';
                echo '<table class="table-longtext" border="1" cellspacing="0" cellpadding="5">';
                echo '<tr><th>学生姓名</th><th>比赛名称</th><th>修改时间</th></tr>';
                while ($log = $result2->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($log['name']) . '</td>';
                    echo '<td>' . htmlspecialchars($log['competition_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($log['lastedit']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p>没有学生在此期间修改记录。</p>';
            }

            $stmt2->close();
        }

        // 无论如何都更新 lastlogin 为当前时间
        $stmt3 = $db->prepare("UPDATE user_admin SET lastlogin = ? WHERE adminID = ?");
        if (!$stmt3) throw new Exception("更新 lastlogin 失败: " . $db->error);

        $stmt3->bind_param("ss", $now, $adminID);
        $stmt3->execute();
        $stmt3->close();

    } else {
        echo "<p>管理员信息不存在。</p>";
    }

} catch (Exception $e) {
    echo "<p><strong>错误：</strong> " . $e->getMessage() . "</p>";
}
?>
</body>
</html>
