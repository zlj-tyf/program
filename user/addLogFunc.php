<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../config/database.php"); // 业务数据库
$wordpress_url = "http://106.15.139.140";

if (!isset($_SESSION['user'])) {
    die("请先登录！");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("无效请求");
}

$sid = $_SESSION['user'];
$cid = $_POST['cid'] ?? '';
$type = $_POST['type'] ?? '';
$reason = $_POST['reason'] ?? '';
$logdate = $_POST['logdate'] ?? '';
$addtime = date("Y-m-d H:i:s");

if (!$cid || !$type || !$logdate) {
    die("缺少必要参数");
}

// 获取学生姓名和比赛信息
$sql = "SELECT s.name as student_name, c.competition_name, c.default_content
        FROM student s
        JOIN student_course sc ON s.sid = sc.sid
        JOIN course c ON c.cid = sc.cid
        WHERE s.sid = ? AND c.cid = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $sid, $cid);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $student_name = $row['student_name'];
    $competition_name = $row['competition_name'];
    $default_content = $row['default_content'];
} else {
    die("未找到学生或比赛信息");
}
$stmt->close();

$post_title = "{$student_name} + {$competition_name} + 申报材料";

// 获取 JWT Token
function get_jwt_token($username, $password, $url) {
    $data = json_encode(['username' => $username, 'password' => $password]);
    $opts = ['http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $data,
        'ignore_errors' => true,
    ]];
    $context = stream_context_create($opts);
    $result = file_get_contents($url . "/wp-json/jwt-auth/v1/token", false, $context);
    if (!$result) return null;
    $json = json_decode($result, true);
    return $json['token'] ?? null;
}

// 获取 Post ID
function get_post_id_from_url($post_url, $wordpress_url) {
    $parsed = parse_url($post_url);
    if (!isset($parsed['path'])) return null;
    $slug = basename(trim($parsed['path'], '/'));
    $api_url = rtrim($wordpress_url, '/') . '/wp-json/wp/v2/posts?slug=' . urlencode($slug);
    $result = file_get_contents($api_url);
    if (!$result) return null;
    $json = json_decode($result, true);
    if (isset($json[0]['id'])) {
        return $json[0]['id'];
    }
    return null;
}

if ($type == '1') {
    // 创建新项目
    $token = get_jwt_token($sid, '123456', $wordpress_url);
    if (!$token) {
        die("❌ 获取 JWT Token 失败，请确认用户名密码及JWT插件");
    }
    $upload_blank  = "<!-- wp:shortcode -->[shared_files file_upload=1 only_uploaded_files=1]<!-- /wp:shortcode -->";
    $post_content = $default_content ? $default_content . $upload_blank: "<p>请在此处填充申报信息（粘帖申报表）<br/></p><!-- wp:shortcode -->[shared_files file_upload=1 only_uploaded_files=1]<!-- /wp:shortcode -->";

    $post_data = [
        'title' => $post_title,
        'content' => $post_content,
        'status' => 'publish',
    ];

    $opts = ['http' => [
        'method' => 'POST',
        'header' => "Authorization: Bearer $token\r\nContent-Type: application/json\r\n",
        'content' => json_encode($post_data),
        'ignore_errors' => true,
    ]];

    $context = stream_context_create($opts);
    $result = file_get_contents($wordpress_url . "/wp-json/wp/v2/posts", false, $context);
    $response = json_decode($result, true);

    if (isset($response['id']) && isset($response['link'])) {
        $post_link = $response['link'];

        // 写入日志
        $stmt_log = $db->prepare("INSERT INTO student_log (sid, cid, type, reason, logdate, addtime, url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt_log) die("日志插入准备失败：" . $db->error);
        $stmt_log->bind_param("ssissss", $sid, $cid, $type, $reason, $logdate, $addtime, $post_link);
        if (!$stmt_log->execute()) {
            die("日志插入失败：" . $stmt_log->error);
        }
        $stmt_log->close();

        // 更新 student_course.lastedit
        $update_lastedit = $db->prepare("UPDATE student_course SET lastedit = CURRENT_TIMESTAMP WHERE sid = ? AND cid = ?");
        $update_lastedit->bind_param("ss", $sid, $cid);
        $update_lastedit->execute();
        $update_lastedit->close();

        // 创建成功后重定向并带上 edit_url
        $edit_url = "http://106.15.139.140/wp-admin/post.php?post={$response['id']}&action=edit";
        header("Location: addLog.php?url=" . urlencode($post_link) . "&edit_url=" . urlencode($edit_url));
        exit;
    } else {
        die("❌ 创建文章失败，接口返回：" . $result);
    }
} elseif ($type == '2') {
    // 修改项目：找最后一次创建的文章
    $stmt_link = $db->prepare("SELECT url FROM student_log WHERE sid = ? AND cid = ? AND type = '1' ORDER BY addtime DESC LIMIT 1");
    if (!$stmt_link) die("SQL准备失败：" . $db->error);
    $stmt_link->bind_param("ss", $sid, $cid);
    $stmt_link->execute();
    $stmt_link->bind_result($url);
    if ($stmt_link->fetch()) {
        $stmt_link->close();

        $post_id = get_post_id_from_url($url, $wordpress_url);
        $edit_url = $post_id ? "http://106.15.139.140/wp-admin/post.php?post={$post_id}&action=edit" : '';

        // ----------- 写入日志 -----------
        $stmt_log = $db->prepare("INSERT INTO student_log (sid, cid, type, reason, logdate, addtime, url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt_log) {
            die("日志插入准备失败：" . $db->error);
        }
        $stmt_log->bind_param("ssissss", $sid, $cid, $type, $reason, $logdate, $addtime, $url);
        if (!$stmt_log->execute()) {
            die("日志插入失败：" . $stmt_log->error);
        }
        $stmt_log->close();

        // 更新 student_course.lastedit
        $update_lastedit = $db->prepare("UPDATE student_course SET lastedit = CURRENT_TIMESTAMP WHERE sid = ? AND cid = ?");
        $update_lastedit->bind_param("ss", $sid, $cid);
        $update_lastedit->execute();
        $update_lastedit->close();

        header("Location: addLog.php?url=" . urlencode($url) . "&edit_url=" . urlencode($edit_url));
        exit;
    } else {
        die("未找到已创建的文章 URL");
    }
}
 else {
    die("无效操作类型");
}
