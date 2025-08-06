<?php
// 业务数据库连接（你的应用数据库）
require_once("../../config/database.php");

// 额外连接 WordPress 数据库
$wpdb_host = "localhost";
$wpdb_user = "root";
$wpdb_pass = "123456";
$wpdb_name = "wordpress";

$wpdb_new  = new mysqli($wpdb_host, $wpdb_user, $wpdb_pass, $wpdb_name);
if ($wpdb_new ->connect_error) {
    die("连接 WordPress 数据库失败: " . $wpdb_new ->connect_error);
}

// 引入 WordPress 环境，路径根据实际调整
require_once("../../../wp-load.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "无效请求。";
    exit;
}

$name = $_POST["name"] ?? '';
$card_type = $_POST["card_type"] ?? '';

if (empty($name) || !is_numeric($card_type)) {
    echo "姓名或卡类型不能为空，且卡类型需为数字。";
    exit;
}

// 开启业务库事务
mysqli_begin_transaction($db);

try {
    // 1. 插入业务数据库 student 表
    $sql1 = "INSERT INTO student (name, card_type) VALUES (?, ?)";
    $stmt1 = mysqli_prepare($db, $sql1);
    mysqli_stmt_bind_param($stmt1, "si", $name, $card_type);
    if (!mysqli_stmt_execute($stmt1)) {
        throw new Exception("插入学生表失败：" . mysqli_error($db));
    }

    $sid = mysqli_insert_id($db);
    $pwd_plain = '123456';

    // 2. 插入业务库用户账户表
    $pwd_md5 = md5($pwd_plain);
    $sql2 = "INSERT INTO user_student (sid, pwd) VALUES (?, ?)";
    $stmt2 = mysqli_prepare($db, $sql2);
    mysqli_stmt_bind_param($stmt2, "is", $sid, $pwd_md5);
    if (!mysqli_stmt_execute($stmt2)) {
        throw new Exception("插入学生账户失败：" . mysqli_error($db));
    }

    // 3. WordPress 用户信息
    $wp_username = (string)$sid;
    $wp_password = $pwd_plain;
    $wp_email = "user{$sid}@example.com";

    // 4. 检查 WP 用户是否存在
    $query = $wpdb_new ->prepare("SELECT ID FROM wp_users WHERE user_login = ?");
    $wp_user_id = null;
    $stmt = $wpdb_new ->prepare($query);
    $stmt = $wpdb_new ->stmt_init();
    if ($stmt = $wpdb_new ->prepare("SELECT ID FROM wp_users WHERE user_login = ?")) {
        $stmt->bind_param("s", $wp_username);
        $stmt->execute();
        $stmt->bind_result($user_id);
        if ($stmt->fetch()) {
            $wp_user_id = $user_id;
        }
        $stmt->close();
    }

    // 5. 若不存在则创建 WordPress 用户
    if (!$wp_user_id) {
        $wp_user_id = wp_create_user($wp_username, $wp_password, $wp_email);
        if (is_wp_error($wp_user_id)) {
            throw new Exception("WordPress 用户创建失败：" . $wp_user_id->get_error_message());
        }

        // 设置角色为 author
        $user = new WP_User($wp_user_id);
        $user->set_role('author');
    }

    // 6. 确认 wp_user_id 是整数
    $wp_user_id_int = intval($wp_user_id);

    // 7. 更新业务数据库 student 表，存储 wp_user_id
    $sql3 = "UPDATE student SET wp_user_id = ? WHERE sid = ?";
    $stmt3 = mysqli_prepare($db, $sql3);
    mysqli_stmt_bind_param($stmt3, "ii", $wp_user_id_int, $sid);
    if (!mysqli_stmt_execute($stmt3)) {
        throw new Exception("更新学生表 WordPress 用户 ID 失败：" . mysqli_error($db));
    }

    // 提交业务库事务
    mysqli_commit($db);

    echo "✅ 添加成功，学号为 <strong>$sid</strong>，默认密码为 <strong>$pwd_plain</strong><br>WordPress 用户 ID 已关联。";

} catch (Exception $e) {
    mysqli_rollback($db);
    echo "❌ 操作失败，原因：" . $e->getMessage();
}

$wpdb_new ->close();
mysqli_close($db);
?>
<div style="width: 90%;height: 55px;margin: 50px">
    <div style="margin: 0 auto;width: 90px;height: 30px;background-color: #117700">
        <a style="text-decoration: none;padding:3px;color: #f3f3f3;text-align: center;display: block" href="#" onclick="javascript:history.back(-1);">返回</a>
    </div>
</div>
