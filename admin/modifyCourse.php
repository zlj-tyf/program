<?php
require_once("../config/database.php");

// 获取所有课程
$courses = mysqli_query($db, "SELECT id, competition_name FROM course");

// 处理通过ID加载课程信息
$loaded_course = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["load_by_id"])) {
    $load_id = intval($_POST["load_by_id"]);
    $result = mysqli_query($db, "SELECT * FROM course WHERE id = $load_id");
    if (mysqli_num_rows($result) > 0) {
        $loaded_course = mysqli_fetch_assoc($result);
    } else {
        $error = "未找到对应的课程 ID。";
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>课程管理 >> 修改 / 删除申报项目</title>
    <link rel="stylesheet" href="./css/fun.css">
</head>
<body>
<h3 class="subtitle">课程管理 >> 修改 / 删除申报项目</h3>

<!-- 输入ID选择课程 -->
<div class="inputbox" style="width: 300px;">
    <form method="post">
        <span>输入课程 ID：</span>
        <input type="number" name="load_by_id" min="1" required>
        <input type="submit" value="加载">
    </form>
</div>

<?php if (isset($error)): ?>
    <p style="color: red; margin-left: 30px;"><?= $error ?></p>
<?php endif; ?>

<?php if ($loaded_course): ?>
<form action="./fun/updateCourse.php" method="post" style="margin-top:30px;">
    <input type="hidden" name="id" value="<?= $loaded_course["id"] ?>">

    <div class="inputbox"><span>比赛名称：</span>
        <input name="competition_name" type="text" value="<?= htmlspecialchars($loaded_course["competition_name"]) ?>" required>
    </div>

    <div class="inputbox"><span>比赛级别：</span>
        <input name="competition_level" type="text" value="<?= htmlspecialchars($loaded_course["competition_level"]) ?>" required>
    </div>

    <div class="inputbox"><span>申报时间：</span>
        <input name="submit_time" type="date" value="<?= htmlspecialchars($loaded_course["submit_time"]) ?>" required>
    </div>

    <div class="inputbox" style="width: 100%;"><span>申报要求：</span><br>
        <textarea name="submit_requirements" rows="5" style="width: 90%;" required><?= htmlspecialchars($loaded_course["submit_requirements"]) ?></textarea>
    </div>

    <div class="inputbox" style="width: 100%;"><span>学生需提交材料：</span><br>
        <textarea name="student_requirements" rows="5" style="width: 90%;" required><?= htmlspecialchars($loaded_course["student_requirements"]) ?></textarea>
    </div>

    <div class="inputbox"><span>卡种类要求：</span>
        <input name="card_requirement" type="number" min="0" step="1" value="<?= htmlspecialchars($loaded_course["card_requirement"]) ?>" required>
    </div>

    <div class="clickbox clearfloat"><input type="submit" value="保存修改"></div>
</form>

<!-- 删除按钮 -->
<form action="./fun/deleteCourse.php" method="get" onsubmit="return confirm('确定要删除此项目吗？')">
    <input type="hidden" name="id" value="<?= $loaded_course["id"] ?>">
    <div class="clickbox clearfloat"><input type="submit" value="删除项目" style="background: red;"></div>
</form>
<?php endif; ?>

<!-- 所有课程ID参考列表 -->
<div class="inputbox" style="margin-top: 40px;">
    <span>当前已有课程参考（ID - 名称）：</span><br>
    <?php while($row = mysqli_fetch_assoc($courses)): ?>
        <?= $row["id"] ?> - <?= htmlspecialchars($row["competition_name"]) ?><br>
    <?php endwhile; ?>
</div>

</body>
</html>
