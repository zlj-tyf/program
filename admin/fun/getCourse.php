<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>课程结果</title>
    <link rel="stylesheet" type="text/css" href="../css/fun.css">
</head>
<body>
<table>
    <tr>
        <th>课程ID</th>
        <th>比赛名称</th>
        <th>比赛级别</th>
        <th>申报时间</th>
        <th>申报要求</th>
        <th>学生材料要求</th>
        <th>是否需要卡</th>
    </tr>
    <?php
    require_once("../../config/database.php");

    $com = "SELECT * FROM course WHERE 1=1 ";

    if (!empty($_GET['card_requirement'])) {
        $card_input = $_GET['card_requirement'];
        $com .= " AND card_requirement LIKE '%" . mysqli_real_escape_string($db, $card_input) . "%'";
    }

    $result = mysqli_query($db, $com);

    if ($result) {
        while ($row = mysqli_fetch_object($result)) {
            ?>
            <tr>
                <td><?php echo $row->cid ?></td>
                <td><?php echo $row->competition_name ?></td>
                <td><?php echo $row->competition_level ?></td>
                <td><?php echo $row->submit_time ?></td>
                <td><?php echo $row->submit_requirements ?></td>
                <td><?php echo $row->student_requirements ?></td>
                <td><?php echo $row->card_requirement ?></td>
            </tr>
            <?php
        }
    } else {
        echo "<tr><td colspan='7'>查询失败: " . mysqli_error($db) . "</td></tr>";
    }

    mysqli_close($db);
    ?>
</table>
</body>
</html>
