<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/fun.css">
    <title>卡片管理 >> 销售与报名统计</title>
    <!-- jQuery 和 Tablesorter -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
    <script>
        $(function() {
            $(".table-longtext").tablesorter({
                theme: 'default'
                // 不启用 filter widget
            });

            $(".show-students").on("click", function(e){
                e.preventDefault();
                const cardId = $(this).data("cardid");
                const url = `getCardStudents.php?card_id=${cardId}`;
                window.open(url, "_blank", "width=600,height=400,scrollbars=yes");
            });
        });
    </script>
</head>
<body>
<h3 class="subtitle">卡片管理 >> 销售与报名统计  点击表头可以筛选</h3>

<table class="table-longtext" border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>卡片ID</th>
            <th>卡片名称</th>
            <th>卖出数量</th>
            <th>允许报名的比赛</th>
            <th>最大报名次数</th>
        </tr>
    </thead>
    <tbody>
        <?php
        include '../config/database.php';

        $cards = $db->query("SELECT * FROM card");
        if($cards->num_rows > 0){
            while($card = $cards->fetch_assoc()){
                $card_id = $card['id'];
                $card_name = htmlspecialchars($card['name']);
                $max_courses = $card['max_courses'];

                // 卖出数量
                $res_count = $db->query("SELECT COUNT(*) AS sold_count FROM student_card WHERE card_id = '$card_id'");
                $sold_count = $res_count->fetch_assoc()['sold_count'];

                // 允许报名的比赛
                $allowed_courses_ids = explode(',', $card['allowed_courses']);
                $allowed_courses_ids = array_filter($allowed_courses_ids); 
                $allowed_courses_names = [];

                if(!empty($allowed_courses_ids)){
                    $ids_str = implode(',', array_map('intval',$allowed_courses_ids));
                    $res_courses = $db->query("SELECT competition_name FROM course WHERE cid IN ($ids_str)");
                    while($row = $res_courses->fetch_assoc()){
                        $allowed_courses_names[] = htmlspecialchars($row['competition_name']);
                    }
                }
                $allowed_courses_str = implode('，', $allowed_courses_names);

                echo "<tr>";
                echo "<td>$card_id</td>";
                echo "<td>$card_name</td>";
                echo "<td><a href='#' class='show-students' data-cardid='$card_id'>$sold_count</a></td>";
                echo "<td>$allowed_courses_str</td>";
                echo "<td>$max_courses</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>暂无卡片数据</td></tr>";
        }

        $db->close();
        ?>
    </tbody>
</table>
</body>
</html>
