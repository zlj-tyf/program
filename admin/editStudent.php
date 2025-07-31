<?php
session_start();
require_once("../config/database.php");

// 如果没有传sid，显示输入学号的表单
if (!isset($_GET['sid'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sid = trim($_POST['sid'] ?? '');
        if ($sid !== '') {
            header("Location: editStudent.php?sid=" . urlencode($sid));
            exit;
        } else {
            $error = "请输入学号";
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="zh">
    <head>
        <meta charset="UTF-8">
        <title>编辑学生 - 输入学号</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; }
            label { font-weight: bold; }
            input[type="text"] { padding: 5px; width: 200px; }
            input[type="submit"] { padding: 5px 10px; }
            .error { color: red; margin-top: 10px; }
        </style>
    </head>
    <body>
    <h3>请输入要编辑的学生学号</h3>
    <?php if (!empty($error)) echo '<p class="error">' . htmlspecialchars($error) . '</p>'; ?>
    <form method="post" action="">
        <label>学号：<input type="text" name="sid" required></label>
        <input type="submit" value="编辑">
    </form>
    </body>
    </html>
    <?php
    exit;
}

$sid = $_GET['sid'];

// 处理POST，更新学生信息
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // 读取并过滤POST数据，注意字段名需要和数据库一致
    $fields = [
        'name', 'sex', 'birth', 'age', 'edu_primary_start', 'edu_primary_end', 'edu_primary_school',
        'edu_junior_start', 'edu_junior_end', 'edu_junior_school', 'edu_senior_start', 'edu_senior_end',
        'edu_senior_school', 'current_grade', 'current_school', 'father_name', 'father_tel', 'father_workplace',
        'father_position', 'mother_name', 'mother_tel', 'mother_workplace', 'mother_position', 'has_researcher',
        'card_type'
    ];

    $updates = [];
    foreach ($fields as $field) {
        if ($field === 'has_researcher') {
            $value = isset($_POST[$field]) ? 1 : 0;
        } else {
            $value = isset($_POST[$field]) && $_POST[$field] !== '' ? mysqli_real_escape_string($db, $_POST[$field]) : null;
        }
        if ($value === null) {
            $updates[] = "$field = NULL";
        } elseif (is_numeric($value) && in_array($field, ['age', 'current_grade', 'has_researcher', 'card_type'])) {
            // 数字字段不加引号
            $updates[] = "$field = $value";
        } else {
            $updates[] = "$field = '$value'";
        }
    }

    $sql_update = "UPDATE student SET " . implode(", ", $updates) . " WHERE sid = '" . mysqli_real_escape_string($db, $sid) . "'";
    $result_update = mysqli_query($db, $sql_update);

    if ($result_update) {
        $message = "信息更新成功！";
    } else {
        $message = "更新失败：" . mysqli_error($db);
    }
}

// 查询学生信息用于显示
$sql_select = "SELECT * FROM student WHERE sid = '" . mysqli_real_escape_string($db, $sid) . "'";
$result_select = mysqli_query($db, $sql_select);

if (!$result_select || mysqli_num_rows($result_select) === 0) {
    echo "<p>未找到学号为 " . htmlspecialchars($sid) . " 的学生信息。</p>";
    exit;
}

$row = mysqli_fetch_assoc($result_select);
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>编辑学生信息 - <?php echo htmlspecialchars($sid); ?></title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 900px; margin: auto; }
        form div { margin-bottom: 12px; }
        label { display: inline-block; width: 140px; font-weight: bold; vertical-align: top; }
        input[type="text"], input[type="number"], input[type="date"], select {
            width: 250px; padding: 5px;
        }
        input[type="checkbox"] {
            transform: scale(1.3);
            vertical-align: middle;
            margin-left: 4px;
        }
        .message { margin: 15px 0; color: green; font-weight: bold; }
        .error { color: red; }
        .btn-group { margin-top: 20px; }
        input[type="submit"] { padding: 8px 20px; font-size: 16px; cursor: pointer; }
    </style>
</head>
<body>
<h2>编辑学生信息 - 学号：<?php echo htmlspecialchars($sid); ?></h2>

<?php if (!empty($message)) echo '<div class="message">' . htmlspecialchars($message) . '</div>'; ?>

<form method="post" action="">
    <div><label>姓名：</label><input name="name" type="text" value="<?php echo htmlspecialchars($row['name']); ?>"></div>

    <div><label>性别：</label>
        <select name="sex">
            <option value="男" <?php if ($row['sex'] === '男') echo 'selected'; ?>>男</option>
            <option value="女" <?php if ($row['sex'] === '女') echo 'selected'; ?>>女</option>
        </select>
    </div>

    <div><label>出生日期：</label><input name="birth" type="date" value="<?php echo htmlspecialchars($row['birth']); ?>"></div>
    <div><label>年龄：</label><input name="age" type="number" min="0" value="<?php echo htmlspecialchars($row['age']); ?>"></div>

    <h3>教育经历</h3>
    <div><label>小学起始：</label><input name="edu_primary_start" type="date" value="<?php echo htmlspecialchars($row['edu_primary_start']); ?>"></div>
    <div><label>小学结束：</label><input name="edu_primary_end" type="date" value="<?php echo htmlspecialchars($row['edu_primary_end']); ?>"></div>
    <div><label>小学学校：</label><input name="edu_primary_school" type="text" value="<?php echo htmlspecialchars($row['edu_primary_school']); ?>"></div>

    <div><label>初中起始：</label><input name="edu_junior_start" type="date" value="<?php echo htmlspecialchars($row['edu_junior_start']); ?>"></div>
    <div><label>初中结束：</label><input name="edu_junior_end" type="date" value="<?php echo htmlspecialchars($row['edu_junior_end']); ?>"></div>
    <div><label>初中学校：</label><input name="edu_junior_school" type="text" value="<?php echo htmlspecialchars($row['edu_junior_school']); ?>"></div>

    <div><label>高中起始：</label><input name="edu_senior_start" type="date" value="<?php echo htmlspecialchars($row['edu_senior_start']); ?>"></div>
    <div><label>高中结束：</label><input name="edu_senior_end" type="date" value="<?php echo htmlspecialchars($row['edu_senior_end']); ?>"></div>
    <div><label>高中学校：</label><input name="edu_senior_school" type="text" value="<?php echo htmlspecialchars($row['edu_senior_school']); ?>"></div>

    <div><label>当前年级：</label><input name="current_grade" type="number" min="0" value="<?php echo htmlspecialchars($row['current_grade']); ?>"></div>
    <div><label>当前学校：</label><input name="current_school" type="text" value="<?php echo htmlspecialchars($row['current_school']); ?>"></div>

    <h3>家长信息</h3>
    <div><label>父亲姓名：</label><input name="father_name" type="text" value="<?php echo htmlspecialchars($row['father_name']); ?>"></div>
    <div><label>父亲电话：</label><input name="father_tel" type="text" value="<?php echo htmlspecialchars($row['father_tel']); ?>"></div>
    <div><label>父亲单位：</label><input name="father_workplace" type="text" value="<?php echo htmlspecialchars($row['father_workplace']); ?>"></div>
    <div><label>父亲职务：</label><input name="father_position" type="text" value="<?php echo htmlspecialchars($row['father_position']); ?>"></div>

    <div><label>母亲姓名：</label><input name="mother_name" type="text" value="<?php echo htmlspecialchars($row['mother_name']); ?>"></div>
    <div><label>母亲电话：</label><input name="mother_tel" type="text" value="<?php echo htmlspecialchars($row['mother_tel']); ?>"></div>
    <div><label>母亲单位：</label><input name="mother_workplace" type="text" value="<?php echo htmlspecialchars($row['mother_workplace']); ?>"></div>
    <div><label>母亲职务：</label><input name="mother_position" type="text" value="<?php echo htmlspecialchars($row['mother_position']); ?>"></div>

    <div><label>家中是否有科研人员：</label>
        <input name="has_researcher" type="checkbox" value="1" <?php if ($row['has_researcher']) echo 'checked'; ?>>
    </div>

    <div><label>卡种类：</label>
        <input name="card_type" type="number" min="0" value="<?php echo htmlspecialchars($row['card_type']); ?>">
    </div>

    <div class="btn-group">
        <input type="submit" name="submit" value="保存修改">
    </div>
</form>
</body>
</html>
          