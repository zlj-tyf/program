<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/fun.css">
    <title>学生管理 >> 新增学生</title>
</head>
<body>
<h3 class="subtitle">学生管理 >> 新增学生</h3>

<?php
// 需要读取卡片与课程列表以在表格中展示
require_once("../config/database.php");

// 读取所有课程 -> 映射表 cid => competition_name
$courseMap = [];
$crs = mysqli_query($db, "SELECT cid, competition_name FROM course ORDER BY cid ASC");
while ($r = mysqli_fetch_assoc($crs)) {
    $courseMap[(int)$r['cid']] = $r['competition_name'];
}

// 读取所有卡片
$cards = [];
$res = mysqli_query($db, "SELECT id, name, allowed_courses, max_courses FROM card ORDER BY id ASC");
while ($row = mysqli_fetch_assoc($res)) {
    // 解析 allowed_courses（逗号分隔的 cid）
    $allowed_ids = array_filter(array_map('intval', explode(',', (string)$row['allowed_courses'])));
    $allowed_names = [];
    foreach ($allowed_ids as $cid) {
        if (isset($courseMap[$cid])) {
            $allowed_names[] = $courseMap[$cid];
        }
    }
    $row['allowed_names'] = $allowed_names;   // 仅显示这些允许的比赛
    $row['max_courses']  = (int)$row['max_courses'];
    $cards[] = $row;
}
?>

<form id="addStudentForm" action="./fun/addStudent.php" method="post" target="resultbox" onsubmit="return buildCardsPayload();">
    <div class="inputbox"><span>姓名：</span><input name="name" required type="text"></div>

    <!-- 卡片表格：第一列卡名，第二列允许的比赛，第三列拥有数量，第四列剩余(剩余/总共) -->
    <table>
        <thead>
            <tr>
                <th>卡片名称</th>
                <th>可报名的比赛</th>
                <th>拥有数量</th>
                <th>剩余可报名次数</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($cards as $c): ?>
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
                        name="card_qty[<?php echo (int)$c['id']; ?>]"
                        value="0"
                        min="0"
                        style="width:60px;"
                        data-max="<?php echo (int)$c['max_courses']; ?>"
                        oninput="updateRemaining(this)"
                    >
                </td>
                <td class="remaining">0/0</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <div class="clickbox clearfloat"><span></span><input name="submit" type="submit" value="提交"></div>
    <div class="redbox clickbox "><span></span><input name="reset" type="reset" value="清除" onclick="resetRemaining()"></div>
    <p>说明：第四列“剩余/总共”会根据第三列数量 × 该卡可报次数自动计算。</p>
</form>

<iframe name="resultbox" frameborder="0" width="100%" height="200px"></iframe>

<script>
function updateRemaining(input) {
    var qty = parseInt(input.value, 10) || 0;
    var maxPerCard = parseInt(input.getAttribute('data-max'), 10) || 0;
    var total = qty * maxPerCard;
    var td = input.closest('tr').querySelector('.remaining');
    td.textContent = total + "/" + total;
}

function resetRemaining() {
    document.querySelectorAll('input[name^="card_qty["]').forEach(function (inp) {
        inp.value = 0;
    });
    document.querySelectorAll('.remaining').forEach(function (td) {
        td.textContent = "0/0";
    });
    // 同时清空之前构建的隐藏字段
    document.querySelectorAll('.card-hidden').forEach(function (n) { n.remove(); });
}

// 为了兼容后端（接收 cards[][card_id] / cards[][count]），在提交前把 card_qty 转换成 cards 数组隐藏字段
function buildCardsPayload() {
    var form = document.getElementById('addStudentForm');

    // 清理旧的隐藏字段
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

    return true; // 继续提交
}
</script>

</body>
</html>
