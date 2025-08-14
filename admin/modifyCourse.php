<?php
session_start();
require_once '../config/database.php';

// 获取所有课程
$courses = [];
$result = $db->query("SELECT cid, competition_name FROM course ORDER BY cid");
while($row = $result->fetch_assoc()){
    $courses[] = $row;
}

// 处理通过ID加载课程信息
$loaded_course = null;
$error = null;
$selectedCID = 0;

if(isset($_GET['cid'])){
    $selectedCID = intval($_GET['cid']);
} elseif(isset($_POST['cid'])){
    $selectedCID = intval($_POST['cid']);
}

if($selectedCID > 0){
    $stmt = $db->prepare("SELECT * FROM course WHERE cid=?");
    $stmt->bind_param("i", $selectedCID);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows > 0){
        $loaded_course = $res->fetch_assoc();
    } else {
        $error = "未找到对应的课程 ID。";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>课程管理 >> 修改 / 删除申报项目</title>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        body {
            margin: 0;
            font-family: "Microsoft YaHei", sans-serif;
            background: #f7f7f7;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 240px;
            background: #fff;
            border-right: 1px solid #ddd;
            padding: 20px;
            box-sizing: border-box;
        }
        .sidebar h3 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .sidebar form input[type="number"],
        .sidebar form button {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        .sidebar a {
            display: block;
            margin: 3px 0;
            color: #333;
            text-decoration: none;
        }
        .sidebar a:hover {
            text-decoration: underline;
        }
        .main {
            flex: 1;
            padding: 20px;
            box-sizing: border-box;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        form .form-group {
            margin-bottom: 15px;
            width: 100%;
        }
        form .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        form .form-group input[type="text"],
        form .form-group input[type="number"],
        form .form-group select,
        form .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            font-size: 14px;
        }
        form .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        form .form-group textarea#default_content {
            height: 200px;
        }
        .btn {
            padding: 10px 15px;
            font-size: 14px;
            cursor: pointer;
            background: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn:hover {
            opacity: 0.9;
        }
        hr {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h3>选择课程</h3>
        <form method="POST">
            <label>输入课程 ID:</label>
            <input type="number" name="cid" min="1" required value="<?php echo $selectedCID; ?>">
            <button type="submit" class="btn">确认</button>
        </form>
        <hr>
        <h4>所有课程</h4>
        <?php foreach($courses as $course): ?>
            <a href="?cid=<?php echo $course['cid']; ?>">
                <?php echo $course['cid']." - ".htmlspecialchars($course['competition_name']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="main">
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if($loaded_course): ?>
            <h3>编辑课程信息</h3>

            <form action="./fun/deleteCourse.php" method="get" onsubmit="return confirm('确定要删除此项目吗？')" style="margin-bottom:20px;">
                <input type="hidden" name="cid" value="<?= $loaded_course["cid"] ?>">
                <button type="submit" class="btn btn-danger">删除项目</button>
            </form>

            <form action="./fun/editCourse.php" method="post">
                <input type="hidden" name="cid" value="<?= $loaded_course["cid"] ?>">

                <div class="form-group">
                    <label>比赛名称：</label>
                    <input name="competition_name" type="text" value="<?= htmlspecialchars($loaded_course["competition_name"]) ?>" required>
                </div>

                <div class="form-group">
                    <label>比赛简称：</label>
                    <input name="competition_short_name" type="text" value="<?= htmlspecialchars($loaded_course["competition_short_name"]) ?>">
                </div>

                <div class="form-group">
                    <label>比赛级别：</label>
                    <select name="competition_level" required>
                        <?php
                        $levels = ["校级","市级","省级","国家级","国际级"];
                        foreach($levels as $level){
                            $selected = ($loaded_course["competition_level"] == $level) ? "selected" : "";
                            echo "<option value=\"$level\" $selected>$level</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>申报时间：</label>
                    <input name="submit_time" type="text" value="<?= htmlspecialchars($loaded_course["submit_time"]) ?>" required>
                </div>

                <div class="form-group">
                    <label>申报要求：</label>
                    <textarea name="submit_requirements"><?= htmlspecialchars($loaded_course["submit_requirements"]) ?></textarea>
                </div>

                <div class="form-group">
                    <label>学生需提交材料：</label>
                    <textarea name="student_requirements"><?= htmlspecialchars($loaded_course["student_requirements"]) ?></textarea>
                </div>

                <div class="form-group">
                    <label>默认页面内容：</label>
                    <textarea name="default_content" id="default_content"><?= htmlspecialchars($loaded_course["default_content"]) ?></textarea>
                </div>

                <div class="form-group">
                    <label>卡种类要求：</label>
                    <input name="card_requirement" type="number" min="0" step="1" value="<?= htmlspecialchars($loaded_course["card_requirement"]) ?>" required>
                </div>

                <button type="submit" class="btn">保存修改</button>
            </form>
        <?php else: ?>
            <p>请从左侧选择或输入课程 ID 进行加载。</p>
        <?php endif; ?>
    </div>
</div>

<script>
    ClassicEditor
        .create(document.querySelector('#default_content'))
        .catch(error => { console.error(error); });
</script>
</body>
</html>
