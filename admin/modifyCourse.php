<?php
require_once("../config/database.php");

// 获取所有课程
$courses = mysqli_query($db, "SELECT cid, competition_name FROM course");

// 处理通过ID加载课程信息
$loaded_course = null;
$error = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["load_by_id"])) {
    $load_cid = intval($_POST["load_by_id"]);
    $result = mysqli_query($db, "SELECT * FROM course WHERE cid = $load_cid");
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
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
</head>
<body>
<h3 class="subtitle">课程管理 >> 修改 / 删除申报项目</h3>

<div class="inputbox" style="width: 300px;">
    <form method="post">
        <span>输入课程 ID：</span>
        <input type="number" name="load_by_id" min="1" required>
        <input type="submit" value="加载">
    </form>
</div>

<?php if ($error): ?>
    <p style="color: red; margin-left: 30px;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($loaded_course): ?>

    <form action="./fun/deleteCourse.php" method="get" onsubmit="return confirm('确定要删除此项目吗？')">
    <input type="hidden" name="cid" value="<?= $loaded_course["cid"] ?>">
    <div class="clickbox clearfloat"><input type="submit" value="删除项目" style="background: red;"></div>
</form>
<form action="./fun/editCourse.php" method="post" style="margin-top:30px;">
    <input type="hidden" name="cid" value="<?= $loaded_course["cid"] ?>">
    <div class="clickbox clearfloat"><input type="submit" value="保存修改"></div>

    <div class="inputbox"><span>比赛名称：</span>
        <input name="competition_name" type="text" value="<?= htmlspecialchars($loaded_course["competition_name"]) ?>" required>
    </div>

    <div class="inputbox"><span>比赛简称：</span>
        <input name="competition_short_name" type="text" value="<?= htmlspecialchars($loaded_course["competition_short_name"]) ?>">
    </div>

    <div class="inputbox"><span>比赛级别：</span>
        <input name="competition_level" type="text" value="<?= htmlspecialchars($loaded_course["competition_level"]) ?>" required>
    </div>

    <div class="inputbox"><span>申报时间：</span>
        <input name="submit_time" type="text" value="<?= htmlspecialchars($loaded_course["submit_time"]) ?>" required>
    </div>

    <div class="inputbox" style="width: 90%;"><span>申报要求：</span><br>
        <textarea name="submit_requirements" rows="5" style="width: 90%;" required><?= htmlspecialchars($loaded_course["submit_requirements"]) ?></textarea>
    </div>

    <div class="inputbox" style="width: 90%;"><span>学生需提交材料：</span><br>
        <textarea name="student_requirements" rows="5" style="width: 90%;" required><?= htmlspecialchars($loaded_course["student_requirements"]) ?></textarea>
    </div>

    <div class="inputbox" style="width: 90%;"><span>默认页面内容：</span><br>
        <textarea name="default_content" id="default_content" rows="15" style="width: 90%;"><?= htmlspecialchars($loaded_course["default_content"]) ?></textarea>
    </div>

    <div class="inputbox"><span>卡种类要求：</span>
        <input name="card_requirement" type="number" min="0" step="1" value="<?= htmlspecialchars($loaded_course["card_requirement"]) ?>" required>
    </div>

</form>


<?php endif; ?>

<div class="inputbox" style="margin-top: 40px;">
    <span>当前已有课程参考（ID - 名称）：</span><br>
    <?php while($row = mysqli_fetch_assoc($courses)): ?>
        <?= htmlspecialchars($row["cid"]) ?> - <?= htmlspecialchars($row["competition_name"]) ?><br>
    <?php endwhile; ?>
</div>

<script>
    ClassicEditor
        .create(document.querySelector('#default_content'))
        .catch(error => { console.error(error); });
</script>
</body>
</html>
