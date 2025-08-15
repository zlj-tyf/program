<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../config/database.php");

// 读取所有学生
$studentsRes = mysqli_query($db, "SELECT sid, name, card_type FROM student ORDER BY sid ASC");
$students = [];
while ($row = mysqli_fetch_assoc($studentsRes)) {
    $students[$row['sid']] = [
        'name' => $row['name'],
        'card_type' => (int)$row['card_type']
    ];
}

// 读取所有课程
$coursesRes = mysqli_query($db, "SELECT cid, competition_name, competition_short_name, card_requirement FROM course ORDER BY cid ASC");
$courses = [];
while ($row = mysqli_fetch_assoc($coursesRes)) {
    $courses[$row['cid']] = [
        'name' => $row['competition_name'],
        'short_name' => $row['competition_short_name'],
        'card_requirement' => (int)$row['card_requirement']
    ];
}

// 读取所有报名数据
$enrollRes = mysqli_query($db, "SELECT sid, cid FROM student_course");
$enroll = [];
while ($row = mysqli_fetch_assoc($enrollRes)) {
    $enroll[$row['sid']][] = $row['cid'];
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>学生选课管理</title>
    <style>
        table {border-collapse: collapse; width: 100%;}
        th, td {border: 1px solid #999; padding: 5px; text-align: center; cursor: pointer;}
        th {background-color: #eee;}
        .disabled {color: gray;}
        .enrolled {color: blue;}
        .not-enrolled {color: red;}
    </style>
</head>
<body>
<h2>学生选课管理</h2>
<?php if (empty($courses) || empty($students)): ?>
    <p style="color:red;">暂无课程或学生数据，请检查数据库。</p>
<?php else: ?>
    <table>
        <tr>
            <th>学号</th>
            <th>姓名</th>
            <?php foreach ($courses as $cid => $course): ?>
                <th title="<?php echo htmlspecialchars($course['name']); ?>">
                    <?php echo htmlspecialchars($course['short_name'] ?: $course['name']); ?>
                </th>
            <?php endforeach; ?>
        </tr>

        <?php foreach ($students as $sid => $sdata): ?>
            <tr>
                <td><?php echo htmlspecialchars($sid); ?></td>
                <td><?php echo htmlspecialchars($sdata['name']); ?></td>
                <?php foreach ($courses as $cid => $course):
                    $enrolled = isset($enroll[$sid]) && in_array($cid, $enroll[$sid]);

                    // 检查是否有可用卡
                    $canEnroll = false;
                    $cardsRes = mysqli_query($db, "SELECT * FROM student_card WHERE sid='$sid' ORDER BY card_id ASC");
                    while ($card = mysqli_fetch_assoc($cardsRes)) {
                        $card_id = $card['card_id'];
                        $cardInfoRes = mysqli_query($db, "SELECT * FROM card WHERE id='$card_id'");
                        $cardInfo = mysqli_fetch_assoc($cardInfoRes);
                        $allowed_courses = explode(',', $cardInfo['allowed_courses']);
                        $remain = $card['card_count'] * $cardInfo['max_courses'] - $card['used_count'];

                        if (in_array($cid, $allowed_courses) && $remain > 0) {
                            $canEnroll = true;
                            break;
                        }
                    }

                    $statusText = $enrolled ? '已报名' : '未报名';
                    $colorClass = $enrolled ? 'enrolled' : ($canEnroll ? 'not-enrolled' : 'disabled');

                    // 将必要数据放入 data-* 属性
                    $dataAttrs = "data-sid='{$sid}' data-cid='{$cid}' data-enrolled='".($enrolled ? 1 : 0)."'";
                    ?>
                    <td class="<?php echo $colorClass; ?>" <?php echo $dataAttrs; ?>>
                        <?php echo $statusText; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<script>
document.querySelectorAll('td[data-sid]').forEach(td => {
    td.addEventListener('click', function() {
        const sid = this.dataset.sid;
        const cid = this.dataset.cid;
        const enrolled = this.dataset.enrolled === '1';

        // 弹窗提示，内容可自定义
        const canEnroll = td.classList.contains('disabled') ? false : true; // 判断是否可报名

        // 弹窗提示内容
        let message = '';
        if (!canEnroll) {
            message = '该学生没有任何一张卡包括了这个课程，请确认是否继续。'; // 不可报名提示
        } else {
            message = enrolled ? '在本表格中修改报名信息不会自动更新选课额度，确认取消报名？' : '在本表格中修改报名信息不会自动更新选课额度，确认报名？'; // 可报名提示
        }        
        if (confirm(message)) {
            // 调用 toggleEnroll.php 切换报名状态
            window.location.href = `toggleEnroll.php?sid=${encodeURIComponent(sid)}&cid=${encodeURIComponent(cid)}`;
        }
    });
});
</script>
</body>
</html>
