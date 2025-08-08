<?php
// 获取前端提交数据
$student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
$feedback = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';
$verify_code = isset($_POST['verify_code']) ? trim($_POST['verify_code']) : '';

// 读取配置
$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);

// 校验验证码
// if ($verify_code !== $config['VERIFY_CODE']) {
//     echo "验证码错误";
//     exit;
// }

// 获取用户信息
$user_ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// 调用 Python 脚本发送邮件
$cmd = escapeshellcmd("python3 scripts/send_feedback.py " . 
    escapeshellarg($student_id) . " " . 
    escapeshellarg($feedback) . " " . 
    escapeshellarg($user_ip) . " " . 
    escapeshellarg($user_agent)
);
$output = shell_exec($cmd . " 2>&1");

if (strpos($output, "邮件发送成功") !== false) {
    echo "反馈已发送成功！";
} else {
    echo "邮件发送失败：" . htmlspecialchars($output);
}
?>
