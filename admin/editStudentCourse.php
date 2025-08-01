<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../config/database.php");

// 读取所有学生，包括 card_type
$studentsRes = mysqli_query($db, "SELECT sid, name, card_type FROM student ORDER BY sid ASC");
$students = []; // 格式：sid => [name, card_type]
while ($row = mysqli_fetch_assoc($studentsRes)) {
    $students[$row['sid']] = [
        'name' => $row['name'],
        'card_type' => (int)$row['card_type']
    ];
}

// 读取所有课程，包括 card_requirement 和 competition_short_name
$coursesRes = mysqli_query($db, "SELECT cid, competition_name, competition_short_name, card_requirement FROM course ORDER BY cid ASC");
$courses = []; // 格式：cid => [name, short_name, card_requirement]
while ($row = mysqli_fetch_assoc($coursesRes)) {
    $courses[$row['cid']] = [
        'name' => $row['competition_name'],
        'short_name' => $row['competition_short_name'],
        'card_requirement' => (int)$row['card_requirement']
    ];
}

// 读取所有报名数据
$enrollRes = mysqli_query($db, "SELECT sid, cid FROM student_course");
$enroll = []; // sid => array(cid)
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
        th, td {border: 1px solid #999; padding: 5px; text-align: center;}
        th {background-color: #eee;}
        a {text-decoration: none; color: blue;}
        .disabled {color: gray; cursor: not-allowed;}
    </style>
</head>
<body>
<h2>学生选课管理（点击“已报名 / 未报名”切换状态）</h2>
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
                    $link = "toggleEnroll.php?sid=" . urlencode($sid) . "&cid=" . urlencode($cid);
                    $text = $enrolled ? "已报名" : "未报名";
                    $color = $enrolled ? "green" : "red";

                    // 判断是否满足卡要求
                    if ($sdata['card_type'] < $course['card_requirement']) {
                        // 不满足：显示为灰色禁用，且有悬浮提示
                        echo "<td class='disabled' title='学生卡类型不足，无法报名此课程'>不可报名</td>";
                    } else {
                        // 可报名：显示链接，带悬浮提示报名状态
                        $title = $enrolled ? '点击取消报名' : '点击报名';
                        echo "<td><a href='$link' style='color: $color;' title='$title'>$text</a></td>";
                    }
                endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</body>
</html>
