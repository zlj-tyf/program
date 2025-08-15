<?php
// session_start();

// 日志文件
$log_dir = __DIR__;
if (!is_dir($log_dir)) mkdir($log_dir, 0777, true);
$log_file = $log_dir . '/sql_log.txt';

// 获取当前用户名函数
function getCurrentUserName($db) {
    if (!empty($_SESSION['user'])) {
        $sid = $_SESSION['user'];
        $res = $db->query("SELECT name FROM student WHERE sid='$sid'");
        if ($res && $row = $res->fetch_assoc()) return $row['name'];
    } elseif (!empty($_SESSION['admin'])) {
        $adminID = $_SESSION['admin'];
        $res = $db->query("SELECT adminName FROM user_admin WHERE adminID='$adminID'");
        if ($res && $row = $res->fetch_assoc()) return $row['adminName'];
    }
    return 'guest';
}

// 日志函数
function logSQL($sql, $error = '', $username = 'guest') {
    global $log_file;
    $time = date("Y-m-d H:i:s");
    $page = $_SERVER['REQUEST_URI'] ?? $_SERVER['SCRIPT_NAME'] ?? 'cli';
    $status = $error ? "FAIL | Error: $error" : "SUCCESS";
    $log_entry = "[$time] [User: $username] [Page: $page] [$status] $sql\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// 自定义 MyDB 类，继承 mysqli
class MyDB extends mysqli {
    public $username = 'guest';

    public function __construct($host, $user, $pass, $db, $username = 'guest') {
        $this->username = $username;
        parent::__construct($host, $user, $pass, $db);
        if ($this->connect_error) {
            logSQL("Connection failed: " . $this->connect_error, $this->connect_error, $this->username);
        }
    }

    public function query($sql, $resultmode = MYSQLI_STORE_RESULT) {
        $result = parent::query($sql, $resultmode);
        if ($result === false) {
            logSQL($sql, $this->error, $this->username);
        } else {
            logSQL($sql, '', $this->username);
        }
        return $result;
    }
}

// 创建临时连接获取当前用户名
$temp_db = new mysqli("localhost","root","123456","school");
$current_user = getCurrentUserName($temp_db);
$temp_db->close();

// 全局替换原来的 $db 对象
$db = new MyDB("localhost","root","123456","school", $current_user);
$wpdb_new = new MyDB("localhost","root","123456","wordpress", $current_user);

// 覆盖全局 mysqli_query 函数
if (!function_exists('mysqli_query')) {
    function mysqli_query($db, $sql) {
        global $log_file;
        $username = $db->username ?? 'guest';
        $result = \mysqli_query($db, $sql); // 调用原生 mysqli_query
        $error = $result === false ? mysqli_error($db) : '';
        logSQL($sql, $error, $username);
        return $result;
    }
}

// 测试 SQL
mysqli_query($db, "SELECT * FROM student");
mysqli_query($wpdb_new, "SELECT * FROM wp_users");
?>
