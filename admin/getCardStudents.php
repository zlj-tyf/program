<?php
include '../config/database.php';

$card_id = intval($_GET['card_id']);

$res = $db->query("
    SELECT s.sid, s.name, COUNT(sc.card_id) AS quantity
    FROM student_card sc
    JOIN student s ON sc.sid = s.sid
    WHERE sc.card_id = '$card_id'
    GROUP BY sc.sid, s.name
    ORDER BY s.name
");

echo "<h3>卡片ID $card_id 持有学生列表</h3>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<thead><tr><th>学号</th><th>姓名</th><th>持有数量</th></tr></thead><tbody>";

if($res->num_rows > 0){
    while($row = $res->fetch_assoc()){
        $sid = htmlspecialchars($row['sid']);
        $name = htmlspecialchars($row['name']);
        $quantity = $row['quantity'];
        echo "<tr><td>$sid</td><td>$name</td><td>$quantity</td></tr>";
    }
} else {
    echo "<tr><td colspan='3'>暂无持有该卡片的学生</td></tr>";
}

echo "</tbody></table>";

$db->close();
?>
