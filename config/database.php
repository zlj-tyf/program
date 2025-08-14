<?php
// session_start();

// 日志文件
$log_dir = __DIR__ ;
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

// 自定义数据库类
class MyDB extends mysqli {
    private $log_file;
    private $username;

    public function __construct($host, $user, $pass, $db, $log_file, $username = 'guest') {
        $this->log_file = $log_file;
        $this->username = $username;
        parent::__construct($host, $user, $pass, $db);
        if ($this->connect_error) {
            $this->log("Connection failed: " . $this->connect_error);
        }
    }

    public function query($sql, $resultmode = MYSQLI_STORE_RESULT) {
        $result = parent::query($sql, $resultmode);
        if ($result) {
            $this->log("SQL SUCCESS: $sql");
        } else {
            $this->log("SQL FAIL: $sql | Error: " . $this->error);
        }
        return $result;
    }

    private function log($message) {
        $time = date("Y-m-d H:i:s");
        $page = $_SERVER['REQUEST_URI'] ?? $_SERVER['SCRIPT_NAME'] ?? 'cli';
        $log_entry = "[$time] [User: {$this->username}] [Page: $page] $message\n";
        file_put_contents($this->log_file, $log_entry, FILE_APPEND);
    }
}

// 先创建一个临时连接获取当前用户名
$temp_db = new mysqli("localhost","root","123456","school");
$current_user = getCurrentUserName($temp_db);
$temp_db->close();

// 使用自定义类创建数据库连接
$db = new MyDB("localhost", "root", "123456", "school", $log_file, $current_user);
$wpdb_new = new MyDB("localhost", "root", "123456", "wordpress", $log_file, $current_user);

// 测试 SQL
$db->query("SELECT * FROM student");
$wpdb_new->query("SELECT * FROM wp_users");
?>
