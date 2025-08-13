<?php
require '../config/database.php';

$error = '';
$sid = 0;

// 先从 GET 或 POST 获取 sid
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sid'])) {
    $sid = intval($_POST['sid']);
} elseif (isset($_GET['sid'])) {
    $sid = intval($_GET['sid']);
}

if ($sid <= 0) {
    // 没有有效 sid，显示输入学号表单
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = "请输入有效的学号";
    }
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
    <?php if (!empty($error)) echo '<p class="error">' . htmlspecialchars($error) . '</p>'; ?>
    <form method="post" action="">
        <label>学号：<input type="text" name="sid" required></label>
        <input type="submit" value="编辑">
    </form>
    </body>
    </html>
    <?php
    exit;
}

// 下面是 sid 有效时，显示编辑页面

// 查询学生基本信息
$stmt = $db->prepare("SELECT sid, name FROM student WHERE sid = ?");
$stmt->bind_param("i", $sid);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
if (!$student) {
    die("学生不存在");
}

// 查询所有卡片信息
$cards = [];
$res = mysqli_query($db, "SELECT id, name, allowed_courses, max_courses FROM card ORDER BY id ASC");

// 预先查询所有课程名映射
$courseMap = [];
$res2 = mysqli_query($db, "SELECT cid, competition_name FROM course");
while ($row2 = mysqli_fetch_assoc($res2)) {
    $courseMap[(int)$row2['cid']] = $row2['competition_name'];
}

while ($row = mysqli_fetch_assoc($res)) {
    $allowed_ids = array_filter(array_map('intval', explode(',', (string)$row['allowed_courses'])));
    $allowed_names = [];
    foreach ($allowed_ids as $cid) {
        if (isset($courseMap[$cid])) {
            $allowed_names[] = $courseMap[$cid];
        }
    }
    $row['allowed_names'] = $allowed_names;
    $row['max_courses']  = (int)$row['max_courses'];
    $cards[] = $row;
}

// 查询该学生已有卡片数据
$student_cards = [];
$res3 = $db->query("SELECT card_id, card_count, used_count FROM student_card WHERE sid = $sid");
while ($row3 = $res3->fetch_assoc()) {
    $student_cards[(int)$row3['card_id']] = $row3;
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8" />
<title>编辑学生信息</title>
<style>
input[type=number] { width: 60px; }
.remaining { white-space: nowrap; }
</style>
</head>
<body>

<h2>编辑学生：<?php echo htmlspecialchars($student['name']); ?></h2>

<form id="editStudentForm" action="./fun/editStudent.php" method="post" target="resultbox" onsubmit="return buildCardsPayload();">
    <input type="hidden" name="sid" value="<?php echo $student['sid']; ?>">
    
    <div class="inputbox">
        <span>姓名：</span>
        <input name="name" required type="text" value="<?php echo htmlspecialchars($student['name']); ?>">
    </div>

    <h3>卡片信息</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>卡片名称</th>
                <th>可报名的比赛</th>
                <th>拥有数量</th>
                <th>剩余可报名次数</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($cards as $c): 
            $card_id = (int)$c['id'];
            $have_count = isset($student_cards[$card_id]) ? (int)$student_cards[$card_id]['card_count'] : 0;
            $used_count = isset($student_cards[$card_id]) ? (int)$student_cards[$card_id]['used_count'] : 0;
            $max_courses = (int)$c['max_courses'];
            $remaining = max(0, $have_count * $max_courses - $used_count);
        ?>
            <tr>
                <td><?php echo htmlspecialchars($c['name']); ?></td>
                <td>
                    <?php
                    if (!empty($c['allowed_names'])) {
                        echo implode("<br>", array_map('htmlspecialchars', $c['allowed_names']));
                    } else {
                        echo "<em>无</em>";
                    }
                    ?>
                </td>
                <td>
                    <input
                        type="number"
                        name="card_qty[<?php echo $card_id; ?>]"
                        value="<?php echo $have_count; ?>"
                        min="0"
                        data-max="<?php echo $max_courses; ?>"
                        oninput="updateRemaining(this)"
                    >
                </td>
                <td class="remaining"><?php echo $remaining . "/" . ($have_count * $max_courses); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <div>
        <input name="submit" type="submit" value="提交">
        <input name="reset" type="reset" value="清除" onclick="resetRemaining()">
    </div>

</form>

<iframe name="resultbox" frameborder="0" width="100%" height="150px"></iframe>

<script>
function updateRemaining(input) {
    var qty = parseInt(input.value, 10) || 0;
    var maxPerCard = parseInt(input.getAttribute('data-max'), 10) || 0;

    var tr = input.closest('tr');
    var remainingTd = tr.querySelector('.remaining');

    var total = qty * maxPerCard;
    remainingTd.textContent = total + "/" + total;
}

function resetRemaining() {
    document.querySelectorAll('input[name^="card_qty["]').forEach(function (inp) {
        inp.value = 0;
    });
    document.querySelectorAll('.remaining').forEach(function (td) {
        td.textContent = "0/0";
    });
    document.querySelectorAll('.card-hidden').forEach(function (n) { n.remove(); });
}

function buildCardsPayload() {
    var form = document.getElementById('editStudentForm');
    Array.from(form.querySelectorAll('.card-hidden')).forEach(function (n) { n.remove(); });

    var qtyInputs = form.querySelectorAll('input[name^="card_qty["]');
    var idx = 0;
    qtyInputs.forEach(function (inp) {
        var m = inp.name.match(/^card_qty\[(\d+)\]$/);
        if (!m) return;
        var cardId = parseInt(m[1], 10);
        var count = parseInt(inp.value, 10) || 0;
        if (count > 0) {
            var h1 = document.createElement('input');
            h1.type = 'hidden';
            h1.name = 'cards[' + idx + '][card_id]';
            h1.value = cardId;
            h1.className = 'card-hidden';
            form.appendChild(h1);

            var h2 = document.createElement('input');
            h2.type = 'hidden';
            h2.name = 'cards[' + idx + '][count]';
            h2.value = count;
            h2.className = 'card-hidden';
            form.appendChild(h2);

            idx++;
        }
    });

    return true;
}
</script>

</body>
</html>
