<?php
include '../config/database.php';

$selectedSID = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['sid'])) {
    $selectedSID = $_POST['sid'];
} elseif (isset($_GET['sid'])) {
    $selectedSID = $_GET['sid'];
}

// 获取所有学生
$students = [];
$result = $db->query("SELECT sid, name FROM student ORDER BY name");
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// 获取所有卡片并左连接学生已有卡片
$cards = [];
if ($selectedSID) {
    $stmt = $db->prepare("
        SELECT 
            c.id AS card_id,
            c.name AS card_name,
            c.max_courses,
            IFNULL(sc.card_count, 0) AS card_count,
            IFNULL(sc.used_count, 0) AS used_count
        FROM card c
        LEFT JOIN student_card sc ON sc.card_id = c.id AND sc.sid = ?
        ORDER BY c.id
    ");
    $stmt->bind_param('s', $selectedSID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $row['remaining'] = $row['card_count'] * $row['max_courses'] - $row['used_count'];
        $cards[] = $row;
    }
    $stmt->close();
}

// 保存修改
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['save_cards'])) {
    foreach ($_POST['card_count'] as $card_id => $count) {
        $stmt = $db->prepare("SELECT * FROM student_card WHERE sid=? AND card_id=?");
        $stmt->bind_param('si', $selectedSID, $card_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows) {
            $stmt2 = $db->prepare("UPDATE student_card SET card_count=? WHERE sid=? AND card_id=?");
            $stmt2->bind_param('isi', $count, $selectedSID, $card_id);
            $stmt2->execute();
            $stmt2->close();
        } else {
            $stmt2 = $db->prepare("INSERT INTO student_card (sid, card_id, card_count, used_count) VALUES (?, ?, ?, 0)");
            $stmt2->bind_param('sii', $selectedSID, $card_id, $count);
            $stmt2->execute();
            $stmt2->close();
        }
        $stmt->close();
    }
    echo "<script>alert('保存成功');window.location='?sid=$selectedSID';</script>";
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<title>学生卡片管理</title>
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; }
.container { display: flex; }
.sidebar { width: 250px; padding: 10px; border-right: 1px solid #ccc; background:#f9f9f9; }
.sidebar a { display:block; padding:2px 0; text-decoration:none; color:#333; }
.sidebar a:hover { text-decoration:underline; }
.content { flex:1; padding:10px; }
table { width:100%; border-collapse: collapse; }
th, td { border:1px solid #ccc; padding:5px; text-align:center; }
input[type=number] { width:60px; }
.negative { color:red; font-weight:bold; }
</style>
<script>
function updateRemaining() {
    document.querySelectorAll('.card-count').forEach(function(input){
        var row = input.closest('tr');
        var max = parseInt(row.dataset.max);
        var used = parseInt(row.dataset.used);
        var count = parseInt(input.value) || 0;
        var remaining = count * max - used;
        var remCell = row.querySelector('.remaining');
        remCell.innerText = remaining;
        if(remaining < 0){
            remCell.classList.add('negative');
        } else {
            remCell.classList.remove('negative');
        }
    });
}
</script>
</head>
<body>
<div class="container">
    <!-- 左侧学生选择 -->
    <div class="sidebar">
        <h3>选择学生</h3>
        <form method="POST">
            <label>输入学号:</label><br>
            <input type="text" name="sid" required value="<?php echo htmlspecialchars($selectedSID); ?>"><br><br>
            <button type="submit">确认</button>
        </form>
        <hr>
        <h4>所有学生</h4>
        <?php foreach($students as $stu): ?>
            <a href="?sid=<?php echo urlencode($stu['sid']); ?>">
                <?php echo htmlspecialchars($stu['sid'] . " - " . $stu['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- 右侧卡片展示 -->
    <div class="content">
        <?php if ($selectedSID && $cards): ?>
        <form method="POST">
            <input type="hidden" name="save_cards" value="1">
            <table>
                <thead>
                    <tr>
                        <th>卡片名称</th>
                        <th>数量</th>
                        <th>已使用次数</th>
                        <th>最大可用次数</th>
                        <th>剩余次数</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($cards as $card): ?>
                    <tr data-max="<?php echo $card['max_courses']; ?>" data-used="<?php echo $card['used_count']; ?>">
                        <td><?php echo htmlspecialchars($card['card_name']); ?></td>
                        <td>
                            <input type="number" class="card-count" name="card_count[<?php echo $card['card_id']; ?>]" 
                                   value="<?php echo $card['card_count']; ?>" min="0" oninput="updateRemaining()">
                        </td>
                        <td><?php echo $card['used_count']; ?></td>
                        <td><?php echo $card['max_courses']; ?></td>
                        <td class="remaining <?php echo $card['remaining']<0?'negative':''; ?>">
                            <?php echo $card['remaining']; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <br>
            <button type="submit">保存修改</button>
        </form>
        <script>updateRemaining();</script>
        <?php elseif ($selectedSID): ?>
            <p>没有卡片数据。</p>
        <?php else: ?>
            <p>请在左侧输入学号或选择学生。</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
