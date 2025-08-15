<?php
session_start();
require_once '../config/database.php';

// 保留提示内容
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
    
    <!-- <p><strong>屏幕分辨率建议：</strong>1920×1080 及以上，否则页面布局无法完整显示。</p> -->
    
    <p><strong>安全建议：</strong>请避免因安全漏洞导致账号信息泄露；避免在公共或不安全的网络环境下登录系统。</p>

    <p><strong>当前系统仍在测试中，请对相关数据（如填报材料，信息等）及时本地备份。</strong><p/>如有任何问题，请点击页面右下角的反馈按钮，并复制页面中显示的错误信息，并提供联系方式。对造成的不变敬请谅解。</p>
</div>

<?php
if (!isset($_SESSION['user'])) {
    echo "<p>未登录，无法显示日志信息。</p>";
    exit;
}

$sid = $_SESSION['user'];

// 获取 lastlogin
$stmt = $db->prepare("SELECT lastlogin FROM user_student WHERE sid = ?");
$stmt->bind_param("s", $sid);
$stmt->execute();
$stmt->bind_result($lastlogin);
$stmt->fetch();
$stmt->close();

if ($lastlogin) {
    // 获取当前时间
    $now = date('Y-m-d H:i:s');

    // 查询 student_course 中 lastedit 在 lastlogin 和现在之间的记录
    $sql = "
        SELECT sc.sid, s.name AS student_name, c.competition_name, sc.lastedit
        FROM student_course sc
        JOIN student s ON sc.sid = s.sid
        JOIN course c ON sc.cid = c.cid
        WHERE sc.sid = ? AND sc.lastedit BETWEEN ? AND ?
        ORDER BY sc.lastedit DESC
    ";
    $stmt2 = $db->prepare($sql);
    $stmt2->bind_param("sss", $sid, $lastlogin, $now);
    $stmt2->execute();
    $result = $stmt2->get_result();

    if ($result->num_rows > 0) {
        echo '<h2>你的指导老师在上次登陆至今进行了如下修改，请前往相关页面查看。</h2>';
        echo '<table class="table-longtext" border="1" cellpadding="5" cellspacing="0">';
        echo '<tr><th>学生姓名</th><th>比赛名称</th><th>修改时间</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['student_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['competition_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['lastedit']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo "<p>暂无最近修改记录。</p>";
    }
    $stmt2->close();

    // 更新 lastlogin
    $stmt3 = $db->prepare("UPDATE user_student SET lastlogin = ? WHERE sid = ?");
    $stmt3->bind_param("ss", $now, $sid);
    $stmt3->execute();
    $stmt3->close();
} else {
    echo "<p>尚未记录上次登录时间，暂无日志显示。</p>";
}
?>
</body>
</html>
