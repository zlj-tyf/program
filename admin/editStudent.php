<?php
session_start();
require_once '../config/database.php';

// 状态提示
$statusMsg = '';
$selectedSID = '';
$students = [];

// 获取所有学生
$result = $db->query("SELECT sid, name FROM student ORDER BY sid");
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// 处理GET或POST选择的学生
if (isset($_GET['sid'])) {
    $selectedSID = $_GET['sid'];
} elseif (isset($_POST['sid'])) {
    $selectedSID = $_POST['sid'];
}

// 处理提交修改
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
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
            $updates[] = "$field = $value";
        } else {
            $updates[] = "$field = '$value'";
        }
    }

    $sql_update = "UPDATE student SET " . implode(", ", $updates) . " WHERE sid = '" . mysqli_real_escape_string($db, $selectedSID) . "'";
    if (mysqli_query($db, $sql_update)) {
        $statusMsg = "信息更新成功！";
    } else {
        $statusMsg = "更新失败：" . mysqli_error($db);
    }
}

// 查询学生信息用于显示
$studentInfo = [];
if ($selectedSID) {
    $sql_select = "SELECT * FROM student WHERE sid = '" . mysqli_real_escape_string($db, $selectedSID) . "'";
    $result_select = mysqli_query($db, $sql_select);
    if ($result_select && mysqli_num_rows($result_select) > 0) {
        $studentInfo = mysqli_fetch_assoc($result_select);
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<title>编辑学生信息</title>
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; }
.container { display:flex; min-height:100vh; }
.sidebar { width:220px; padding:10px; border-right:1px solid #ccc; background:#f9f9f9; }
.sidebar h3 { margin-top:0; }
.sidebar a { display:block; margin:3px 0; text-decoration:none; color:#333; }
.main { flex:1; padding:20px; }
form div { margin-bottom:12px; }
label { display:inline-block; width:160px; font-weight:bold; vertical-align:top; }
input[type="text"], input[type="number"], input[type="date"], select { width:200px; padding:5px; }
input[type="checkbox"] { transform: scale(1.3); vertical-align:middle; margin-left:4px; }
.message { margin:15px 0; color:green; font-weight:bold; }
input[type="submit"] { padding:8px 20px; font-size:16px; cursor:pointer; }
</style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h3>选择学生</h3>
        <form method="POST">
            <label>输入学号:</label><br>
            <input type="text" name="sid" required value="<?php echo htmlspecialchars($selectedSID); ?>"><br><br>
            <button type="submit">确认</button>
        </form>
        <hr>
        <h4>所有学生</h4>
        <?php foreach($students as $stu): ?>
            <a href="?sid=<?php echo urlencode($stu['sid']); ?>">
                <?php echo htmlspecialchars($stu['sid']) . " - " . htmlspecialchars($stu['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="main">
        <?php if($selectedSID && $studentInfo): ?>
            <h3>编辑学生信息 - 学号：<?php echo htmlspecialchars($selectedSID); ?></h3>
            <h3>对于所有日期的编辑，必须遵循yyyy-mm-dd的格式！否则将无法解析进而导致数据库异常。</h3>
            <?php if($statusMsg): ?>
                <div class="message"><?php echo $statusMsg; ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="sid" value="<?php echo htmlspecialchars($selectedSID); ?>">
                <?php
                $fields = [
                    'name'=>'姓名', 'sex'=>'性别', 'birth'=>'出生日期', 'age'=>'年龄',
                    'edu_primary_start'=>'小学入学', 'edu_primary_end'=>'小学毕业', 'edu_primary_school'=>'小学学校',
                    'edu_junior_start'=>'初中入学', 'edu_junior_end'=>'初中毕业', 'edu_junior_school'=>'初中学校',
                    'edu_senior_start'=>'高中入学', 'edu_senior_end'=>'高中毕业', 'edu_senior_school'=>'高中学校',
                    'current_grade'=>'当前年级', 'current_school'=>'当前学校', 'father_name'=>'父亲姓名', 'father_tel'=>'父亲电话',
                    'father_workplace'=>'父亲单位', 'father_position'=>'父亲职务', 'mother_name'=>'母亲姓名', 'mother_tel'=>'母亲电话',
                    'mother_workplace'=>'母亲单位', 'mother_position'=>'母亲职务', 'has_researcher'=>'科研经历', 'card_type'=>'卡类型'
                ];
                foreach($fields as $key=>$label):
                    $value = isset($studentInfo[$key]) ? htmlspecialchars($studentInfo[$key]) : '';
                    if($key === 'sex'):
                ?>
                    <div>
                        <label><?php echo $label; ?>:</label>
                        <select name="<?php echo $key; ?>">
                            <option value="男" <?php echo $value==='男'?'selected':''; ?>>男</option>
                            <option value="女" <?php echo $value==='女'?'selected':''; ?>>女</option>
                        </select>
                    </div>
                <?php elseif($key==='has_researcher'): ?>
                    <div>
                        <label><?php echo $label; ?>:</label>
                        <input type="checkbox" name="<?php echo $key; ?>" <?php echo $value==1?'checked':''; ?>>
                    </div>
                <?php else: ?>
                    <div>
                        <label><?php echo $label; ?>:</label>
                        <input type="text" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
                    </div>
                <?php endif; endforeach; ?>
                <input type="submit" name="submit" value="保存修改">
            </form>
        <?php elseif($selectedSID): ?>
            <p>未找到学号为 <?php echo htmlspecialchars($selectedSID); ?> 的学生信息。</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
