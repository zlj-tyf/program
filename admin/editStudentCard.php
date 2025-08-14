<?php
// 开启错误显示
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 接入数据库
require_once '../config/database.php';

$sid = '';
$debug = [];

// 1. 获取学号
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sid']) && !empty($_POST['sid'])) {
    $sid = $_POST['sid'];
} elseif (isset($_GET['sid'])) {
    $sid = $_GET['sid'];
}

// 2. 如果没有学号，显示输入表单
if (!$sid) {
    ?>
    <!DOCTYPE html>
    <html lang="zh">
    <head>
        <meta charset="UTF-8">
        <title>编辑学生 - 输入学号</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            label { font-weight: bold; }
            input[type="text"] { padding: 5px; width: 200px; }
            input[type="submit"] { padding: 5px 10px; }
            .error { color: red; margin-top: 10px; }
        </style>
    </head>
    <body>
    <h3>请输入要编辑的学生学号</h3>
    <form method="post" action="">
        <label>学号：<input type="text" name="sid" required></label>
        <input type="submit" value="编辑">
    </form>
    </body>
    </html>
    <?php
    exit;
}

// 3. 提交修改
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update']) && isset($_POST['card_count'])) {
    foreach ($_POST['card_count'] as $card_id => $new_count) {
        $card_id = (int)$card_id;
        $new_count = (int)$new_count;

        $stmt = $db->prepare("UPDATE student_card SET card_count=? WHERE sid=? AND card_id=?");
        if (!$stmt) {
            $debug[] = "Prepare failed: " . $db->error;
            continue;
        }
        $stmt->bind_param('isi', $new_count, $sid, $card_id);
        if (!$stmt->execute()) {
            $debug[] = "Execute failed for card_id $card_id: " . $stmt->error;
        } else {
            $debug[] = "Card $card_id updated successfully to $new_count";
        }
        $stmt->close();
    }
}

// 4. 获取学生姓名
$stmt = $db->prepare("SELECT name FROM student WHERE sid=?");
$stmt->bind_param('s', $sid);
$stmt->execute();
$stmt->bind_result($student_name);
$stmt->fetch();
$stmt->close();

// 5. 获取学生卡信息
$stmt = $db->prepare("
    SELECT sc.card_id, sc.card_count, sc.used_count, c.name AS card_name, c.max_courses
    FROM student_card sc
    LEFT JOIN card c ON sc.card_id = c.id
    WHERE sc.sid=?
");
$stmt->bind_param('s', $sid);
$stmt->execute();
$result = $stmt->get_result();
$cards = [];
while ($row = $result->fetch_assoc()) {
    $row['remaining'] = $row['card_count'] * $row['max_courses'] - $row['used_count'];
    $cards[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>编辑学生卡</title>
<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    input[type="number"] { width: 60px; }
    .debug { background: #f0f0f0; border: 1px solid #ccc; padding: 10px; margin-top: 20px; white-space: pre-wrap; }
</style>
<script>
function updateRemaining() {
    document.querySelectorAll('.card-count').forEach(function(input){
        var row = input.closest('tr');
        var max = parseInt(row.dataset.max);
        var used = parseInt(row.dataset.used);
        var count = parseInt(input.value);
        row.querySelector('.remaining').innerText = count * max - used;
    });
}
</script>
</head>
<body>
<h2>编辑学生卡 - <?php echo htmlspecialchars($student_name); ?> (<?php echo htmlspecialchars($sid); ?>)</h2>

<form method="post" action="">
<input type="hidden" name="sid" value="<?php echo htmlspecialchars($sid); ?>">
<input type="hidden" name="update" value="1">
<table>
    <tr>
        <th>卡名</th>
        <th>数量</th>
        <th>已使用次数</th>
        <th>最大可用次数</th>
        <th>剩余次数</th>
    </tr>
    <?php foreach($cards as $card): ?>
    <tr data-max="<?php echo $card['max_courses']; ?>" data-used="<?php echo $card['used_count']; ?>">
        <td><?php echo htmlspecialchars($card['card_name']); ?></td>
        <td><input type="number" name="card_count[<?php echo $card['card_id']; ?>]" value="<?php echo $card['card_count']; ?>" class="card-count" min="0" oninput="updateRemaining()"></td>
        <td><?php echo $card['used_count']; ?></td>
        <td><?php echo $card['max_courses']; ?></td>
        <td class="remaining"><?php echo $card['remaining']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
<br>
<input type="submit" value="确认修改">
</form>

<?php if (!empty($debug)): ?>
<div class="debug">
<?php echo implode("\n", $debug); ?>
</div>
<?php endif; ?>

<script>
updateRemaining();
</script>
</body>
</html>
