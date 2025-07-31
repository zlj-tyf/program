<?php
require_once("../../config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 读取并过滤输入
    $sid = isset($_GET['sid']) ? mysqli_real_escape_string($db, $_GET['sid']) : '';
    $name = isset($_GET['name']) ? mysqli_real_escape_string($db, $_GET['name']) : '';
    $cid = isset($_GET['cid']) ? mysqli_real_escape_string($db, $_GET['cid']) : '';
    $cname = isset($_GET['cname']) ? mysqli_real_escape_string($db, $_GET['cname']) : '';

    // 构造WHERE条件
    $where = [];
    if ($sid !== '') {
        $where[] = "student.sid LIKE '%$sid%'";
    }
    if ($name !== '') {
        $where[] = "student.name LIKE '%$name%'";
    }
    if ($cid !== '') {
        $where[] = "course.cid LIKE '%$cid%'";
    }
    if ($cname !== '') {
        $where[] = "course.competition_name LIKE '%$cname%'";
    }

    $where_sql = '';
    if (count($where) > 0) {
        $where_sql = "WHERE " . implode(' AND ', $where);
    }

    // 联表查询选课记录，包括学生和课程信息
    $sql = "
        SELECT student.sid, student.name, course.cid, course.competition_name, sc.score, sc.status
        FROM student_course sc
        INNER JOIN student ON sc.sid = student.sid
        INNER JOIN course ON sc.cid = course.cid
        $where_sql
        ORDER BY student.sid, course.cid
    ";

    $result = mysqli_query($db, $sql);

    if (!$result) {
        echo "查询失败：" . mysqli_error($db);
        exit;
    }

    // 输出结果表格
    echo '<h3>查询结果</h3>';
    echo '<table border="1" cellspacing="0" cellpadding="5">';
    echo '<tr>
            <th>学号</th>
            <th>姓名</th>
            <th>课程号</th>
            <th>比赛名称</th>
            <th>成绩</th>
            <th>状态</th>
          </tr>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['sid']) . '</td>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['cid']) . '</td>';
        echo '<td>' . htmlspecialchars($row['competition_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['score'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($row['status'] ?? '') . '</td>';
        echo '</tr>';
    }
    echo '</table>';

    mysqli_free_result($result);
    mysqli_close($db);
} else {
    echo "无效请求。";
}
