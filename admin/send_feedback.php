<?php
// 获取前端提交数据并简单处理
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';

// 简单后端验证（防止绕过前端）
if (!$name || !$student_id || !$feedback || (!$phone && !$email)) {
    echo "提交的数据不完整，请返回检查。";
    exit;
}

// 获取用户信息
$user_ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// 调用 Python 脚本发送邮件
// 参数依次：姓名、学号、手机号、邮箱、反馈内容、IP、User-Agent
$cmd = escapeshellcmd("python3 scripts/send_feedback.py " .
    escapeshellarg($name) . " " .
    escapeshellarg($student_id) . " " .
    escapeshellarg($phone) . " " .
    escapeshellarg($email) . " " .
    escapeshellarg($feedback) . " " .
    escapeshellarg($user_ip) . " " .
    escapeshellarg($user_agent)
);
$output = shell_exec($cmd . " 2>&1");

if (strpos($output, "邮件发送成功") !== false) {
    echo "反馈已发送成功！谢谢您的宝贵意见。";
} else {
    echo "邮件发送失败：" . htmlspecialchars($output);
}
?>
