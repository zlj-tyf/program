<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>课程选课统计结果</title>
    <link rel="stylesheet" type="text/css" href="../css/fun.css">
</head>
<body>
<table>
    <tr>
        <th>课程号</th>
        <th>课程名</th>
        <th>教师名</th>
        <th>开课学院</th>
        <th>选课人数</th>
        <th>已修人数</th>
        <th>平均分</th>
        <th>操作</th>
    </tr>
    <?php
    require_once("../../config/database.php");

    // 构建查询语句
    $com = "SELECT course.cid, cname, tname, dname, 
                IFNULL(v2.taking, 0) AS taking, 
                IFNULL(v3.finished, 0) AS finished, 
                ROUND(v3.avg_score, 1) AS avg_score
            FROM course 
            LEFT JOIN department ON course.did = department.did
            LEFT JOIN (
                SELECT cid, COUNT(sid) AS taking 
                FROM student_course 
                WHERE score IS NULL 
                GROUP BY cid
            ) AS v2 ON course.cid = v2.cid
            LEFT JOIN (
                SELECT cid, COUNT(sid) AS finished, AVG(score) AS avg_score 
                FROM student_course 
                WHERE score IS NOT NULL 
                GROUP BY cid
            ) AS v3 ON course.cid = v3.cid
            WHERE 1 = 1";

    // 条件拼接
    if (!empty($_GET['cid'])) {
        $cid = mysqli_real_escape_string($db, $_GET['cid']);
        $com .= " AND course.cid LIKE '%$cid%'";
    }
    if (!empty($_GET['cname'])) {
        $cname = mysqli_real_escape_string($db, $_GET['cname']);
        $com .= " AND cname LIKE '%$cname%'";
    }
    if (!empty($_GET['tname'])) {
        $tname = mysqli_real_escape_string($db, $_GET['tname']);
        $com .= " AND tname LIKE '%$tname%'";
    }
    if (!empty($_GET['did'])) {
        $did = mysqli_real_escape_string($db, $_GET['did']);
        $com .= " AND course.did = '$did'";
    }

    $result = mysqli_query($db, $com);
    if ($result) {
        while ($row = mysqli_fetch_object($result)) {
            echo "<tr>";
            echo "<td>$row->cid</td>";
            echo "<td>$row->cname</td>";
            echo "<td>$row->tname</td>";
            echo "<td>$row->dname</td>";
            echo "<td>$row->taking</td>";
            echo "<td>$row->finished</td>";
            echo "<td>" . ($row->avg_score ?: "—") . "</td>";
            echo '<td><a href="getClassScore.php?cid=' . $row->cid . '">详情</a></td>';
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='8'>查询失败：" . mysqli_error($db) . "</td></tr>";
    }

    mysqli_close($db);
    ?>
</table>
</body>
</html>
