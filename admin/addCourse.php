<?php
require_once("../config/database.php");
session_start();
$sid = $_SESSION['sid'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>创建比赛项目</title>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#html_content',
            height: 400
        });
    </script>
</head>
<body>
    <h2>创建比赛项目</h2>
    <form action="fun/addCourse.php" method="post">
        <label>选择比赛：</label>
        <select name="cid" required>
            <?php
            $sql = "SELECT * FROM student_course WHERE sid = ? AND status = '1'";
            $stmt = $db->prepare($sql);
            $stmt->execute([$sid]);
            while ($row = $stmt->fetch()) {
                $cid = $row['cid'];
                $courseQuery = $db->prepare("SELECT competition_name FROM course WHERE cid = ?");
                $courseQuery->execute([$cid]);
                $course = $courseQuery->fetch();
                echo "<option value='{$cid}'>{$course['competition_name']}</option>";
            }
            ?>
        </select><br><br>

        <label>申报内容（支持 HTML）：</label><br>
        <textarea id="html_content" name="html_content"></textarea><br><br>

        <input type="submit" value="创建项目">
    </form>
</body>
</html>
