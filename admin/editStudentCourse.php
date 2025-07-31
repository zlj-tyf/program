<?php
session_start();
require_once("../config/database.php");

// 读取所有学生
$studentsRes = mysqli_query($db, "SELECT sid, name FROM student ORDER BY sid ASC");
$students = [];
while ($row = mysqli_fetch_assoc($studentsRes)) {
    $students[$row['sid']] = $row['name'];
}

// 读取所有比赛（course）
$coursesRes = mysqli_query($db, "SELECT id, competition_name FROM course ORDER BY id ASC");
$courses = [];
while ($row = mysqli_fetch_assoc($coursesRes)) {
    $courses[$row['id']] = $row['competition_name'];
}

// 读取所有报名数据，结构为 sid => array(cid)
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
        th, td {border: 1px solid #999; padding: 5px; text-align: center;}
        th {background-color: #eee;}
        a {text-decoration: none; color: blue;}
    </style>
</head>
<body>
<h2>学生选课管理（点击单元格切换报名状态）</h2>
<table>
    <tr>
        <th>学号</th>
        <th>姓名</th>
        <?php foreach ($courses as $cid => $cname): ?>
            <th><?php echo htmlspecialchars($cname); ?></th>
        <?php endforeach; ?>
    </tr>

    <?php foreach ($students as $sid => $sname): ?>
        <tr>
            <td><?php echo htmlspecialchars($sid); ?></td>
            <td><?php echo htmlspecialchars($sname); ?></td>
            <?php foreach ($courses as $cid => $cname): 
                $enrolled = isset($enroll[$sid]) && in_array($cid, $enroll[$sid]);
                $link = "toggleEnroll.php?sid=" . urlencode($sid) . "&cid=" . urlencode($cid);
                $text = $enrolled ? "已报名" : "未报名";
                $color = $enrolled ? "green" : "red";
            ?>
                <td>
                    <a href="<?php echo $link; ?>" style="color: <?php echo $color; ?>">
                        <?php echo $text; ?>
                    </a>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
