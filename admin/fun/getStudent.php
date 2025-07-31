<?php
require_once("../../config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sid = isset($_POST['sid']) ? mysqli_real_escape_string($db, $_POST['sid']) : '';
    $name = isset($_POST['name']) ? mysqli_real_escape_string($db, $_POST['name']) : '';

    $where = [];
    if ($sid !== '') {
        $where[] = "sid LIKE '%$sid%'";
    }
    if ($name !== '') {
        $where[] = "name LIKE '%$name%'";
    }
    $where_sql = '';
    if (count($where) > 0) {
        $where_sql = "WHERE " . implode(' AND ', $where);
    }

    // 查询符合条件的学生数
    $count_sql = "SELECT COUNT(*) as cnt FROM student $where_sql";
    $count_res = mysqli_query($db, $count_sql);
    if (!$count_res) {
        echo "查询失败：" . mysqli_error($db);
        exit;
    }
    $count_row = mysqli_fetch_assoc($count_res);
    $count = (int)$count_row['cnt'];

    if ($count === 0) {
        echo "<p>没有找到匹配的学生。</p>";
        mysqli_close($db);
        exit;
    }

    if ($count === 1) {
        // 只有一条，查询并显示所有字段
        $sql = "SELECT * FROM student $where_sql LIMIT 1";
        $res = mysqli_query($db, $sql);
        if (!$res) {
            echo "查询失败：" . mysqli_error($db);
            exit;
        }
        $row = mysqli_fetch_assoc($res);

        echo "<h3>学生详细信息</h3>";
        echo '<table border="1" cellpadding="5" cellspacing="0">';
        foreach ($row as $key => $val) {
            echo "<tr><th>" . htmlspecialchars($key) . "</th><td>" . htmlspecialchars($val) . "</td></tr>";
        }
        echo '</table>';

        mysqli_free_result($res);
    } else {
        // 多条结果，只显示学号 姓名 当前年级 卡种类 和 修改按钮
        $sql = "SELECT sid, name, current_grade, card_type FROM student $where_sql ORDER BY sid ASC";
        $res = mysqli_query($db, $sql);
        if (!$res) {
            echo "查询失败：" . mysqli_error($db);
            exit;
        }
        echo '<h3>查询结果（' . $count . '条）</h3>';
        echo '<table border="1" cellpadding="5" cellspacing="0">';
        echo '<tr><th>学号</th><th>姓名</th><th>当前年级</th><th>卡种类</th></tr>';
        while ($row = mysqli_fetch_assoc($res)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['sid']) . '</td>';
            echo '<td>' . htmlspecialchars($row['name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['current_grade']) . '</td>';
            echo '<td>' . htmlspecialchars($row['card_type']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        mysqli_free_result($res);
    }

    mysqli_close($db);
} else {
    echo "无效请求。";
}
?>
