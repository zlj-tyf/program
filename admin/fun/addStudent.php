<?php
require_once("../../../wp-load.php"); // 先加载 WordPress 环境
require_once("../../config/database.php"); // 业务数据库

// 额外连接 WordPress 数据库（可选，仅用于查询）
$wpdb_host = "localhost";
$wpdb_user = "root";
$wpdb_pass = "123456";
$wpdb_name = "wordpress";

$wpdb_new  = new mysqli($wpdb_host, $wpdb_user, $wpdb_pass, $wpdb_name);
if ($wpdb_new->connect_error) {
    die("连接 WordPress 数据库失败: " . $wpdb_new->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "无效请求。";
    exit;
}

$name = $_POST["name"] ?? '';
$pinyin = $_POST["pinyin"] ?? '';
$cards = $_POST["cards"] ?? [];

if (empty($name)) {
    echo "姓名不能为空。";
    exit;
}
if (empty($pinyin)) {
    echo "拼音不能为空。";
    exit;
}
if (empty($cards) || !is_array($cards)) {
    echo "必须至少选择一张卡。";
    exit;
}

// 开启事务
mysqli_begin_transaction($db);

try {
    // 1. 插入 student 表
    $sql1 = "INSERT INTO student (name) VALUES (?)";
    $stmt1 = mysqli_prepare($db, $sql1);
    mysqli_stmt_bind_param($stmt1, "s", $name);
    if (!mysqli_stmt_execute($stmt1)) {
        throw new Exception("插入学生表失败：" . mysqli_error($db));
    }

    $sid = mysqli_insert_id($db);
    $pwd_plain = '123456';

    // 2. 插入 user_student
    $pwd_md5 = md5($pwd_plain);
    $sql2 = "INSERT INTO user_student (sid, pwd) VALUES (?, ?)";
    $stmt2 = mysqli_prepare($db, $sql2);
    mysqli_stmt_bind_param($stmt2, "is", $sid, $pwd_md5);
    if (!mysqli_stmt_execute($stmt2)) {
        throw new Exception("插入学生账户失败：" . mysqli_error($db));
    }

    // 3. 插入 student_card
    $sql_card = "INSERT INTO student_card (sid, card_id, card_count) VALUES (?, ?, ?)";
    $stmt_card = mysqli_prepare($db, $sql_card);
    foreach ($cards as $card) {
        $card_id = intval($card["card_id"]);
        $count   = intval($card["count"]);
        if ($count <= 0) continue;
        mysqli_stmt_bind_param($stmt_card, "iii", $sid, $card_id, $count);
        if (!mysqli_stmt_execute($stmt_card)) {
            throw new Exception("插入学生卡片失败：" . mysqli_error($db));
        }
    }

    // 4. WordPress 用户信息
    $wp_username = $pinyin;
    $wp_password = $pwd_plain;
    $wp_email = "{$pinyin}@example.com";

    // 5. 检查 WP 用户是否存在
    $wp_user_id = username_exists($wp_username);

    // 6. 若不存在则创建 WP 用户
    if (!$wp_user_id) {
        $wp_user_id = wp_create_user($wp_username, $wp_password, $wp_email);
        if (is_wp_error($wp_user_id)) {
            throw new Exception("WordPress 用户创建失败：" . $wp_user_id->get_error_message());
        }
    }

    // 7. 设置角色为 author
    $user = new WP_User($wp_user_id);
    $user->set_role('author');
    wp_update_user([
        'ID' => $wp_user_id,
        'role' => 'author'
    ]);

    // 8. 更新 student 表存储 wp_user_id
    $wp_user_id_int = intval($wp_user_id);
    $sql3 = "UPDATE student SET wp_user_id = ? WHERE sid = ?";
    $stmt3 = mysqli_prepare($db, $sql3);
    mysqli_stmt_bind_param($stmt3, "ii", $wp_user_id_int, $sid);
    if (!mysqli_stmt_execute($stmt3)) {
        throw new Exception("更新学生表 WordPress 用户 ID 失败：" . mysqli_error($db));
    }

    // 提交事务
    mysqli_commit($db);

    echo "✅ 添加成功，学号为 <strong>$sid</strong>，默认密码为 <strong>$pwd_plain</strong><br>WordPress 用户名为 <strong>$wp_username</strong>，已关联 WordPress 用户，并设置为 author。";

} catch (Exception $e) {
    mysqli_rollback($db);
    echo "❌ 操作失败，原因：" . $e->getMessage();
}

$wpdb_new->close();
mysqli_close($db);
?>
<div style="width: 90%;height: 55px;margin: 50px">
    <div style="margin: 0 auto;width: 90px;height: 30px;background-color: #117700">
        <a style="text-decoration: none;padding:3px;color: #f3f3f3;text-align: center;display: block" href="#" onclick="javascript:history.back(-1);">返回</a>
    </div>
</div>
