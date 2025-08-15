<?php
session_start();
$sid = $_SESSION["user"];
require_once("../config/database.php");

// 辅助函数
function getPostValue($db, $key) {
    if (isset($_POST[$key]) && $_POST[$key] !== '') {
        return "'" . mysqli_real_escape_string($db, $_POST[$key]) . "'";
    } else {
        return "NULL";
    }
}

function getCheckboxValue($key) {
    return isset($_POST[$key]) ? "1" : "0";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = getPostValue($db, 'name');
    $sex = getPostValue($db, 'sex');
    $birth = getPostValue($db, 'birth');

    // 自动计算年龄和当前年级
    $age = "NULL";
    $current_grade = "NULL";
    if (isset($_POST['birth']) && $_POST['birth'] !== '') {
        $birthDate = new DateTime($_POST['birth']);
        $today = new DateTime();
        $ageYears = $today->diff($birthDate)->y;
        $age = intval($ageYears);
        $grade = max(1, $ageYears - 5);
        $current_grade = intval($grade);
    }

    $age = $age !== "NULL" ? "'" . mysqli_real_escape_string($db, $age) . "'" : "NULL";
    $current_grade = $current_grade !== "NULL" ? "'" . mysqli_real_escape_string($db, $current_grade) . "'" : "NULL";

    $current_school = getPostValue($db, 'current_school');

    $father_name = getPostValue($db, 'father_name');
    $father_tel = getPostValue($db, 'father_tel');
    $father_workplace = getPostValue($db, 'father_work_unit');
    $father_position = getPostValue($db, 'father_title');

    $mother_name = getPostValue($db, 'mother_name');
    $mother_tel = getPostValue($db, 'mother_tel');
    $mother_workplace = getPostValue($db, 'mother_work_unit');
    $mother_position = getPostValue($db, 'mother_title');

    $has_researcher = getCheckboxValue('has_researcher');

    $edu_primary_start = getPostValue($db, 'primary_start_year');
    $edu_primary_end = getPostValue($db, 'primary_end_year');
    $edu_primary_school = getPostValue($db, 'primary_school_name');

    $edu_junior_start = getPostValue($db, 'junior_start_year');
    $edu_junior_end = getPostValue($db, 'junior_end_year');
    $edu_junior_school = getPostValue($db, 'junior_school_name');

    $edu_senior_start = getPostValue($db, 'senior_start_year');
    $edu_senior_end = getPostValue($db, 'senior_end_year');
    $edu_senior_school = getPostValue($db, 'senior_school_name');

    $com = "UPDATE student SET
        name=$name,
        sex=$sex,
        birth=$birth,
        age=$age,
        current_grade=$current_grade,
        current_school=$current_school,
        father_name=$father_name,
        father_tel=$father_tel,
        father_workplace=$father_workplace,
        father_position=$father_position,
        mother_name=$mother_name,
        mother_tel=$mother_tel,
        mother_workplace=$mother_workplace,
        mother_position=$mother_position,
        has_researcher=$has_researcher,
        edu_primary_start=$edu_primary_start,
        edu_primary_end=$edu_primary_end,
        edu_primary_school=$edu_primary_school,
        edu_junior_start=$edu_junior_start,
        edu_junior_end=$edu_junior_end,
        edu_junior_school=$edu_junior_school,
        edu_senior_start=$edu_senior_start,
        edu_senior_end=$edu_senior_end,
        edu_senior_school=$edu_senior_school
        WHERE sid='$sid'";

    $result = mysqli_query($db, $com);

    if ($result) {
        echo '<h4 style="margin:30px;">提示：信息更改成功！</h4>';
    } else {
        echo '<h4 style="margin:30px;">注意：数据未更改！</h4>';
        echo "SQL 错误: " . mysqli_error($db);
    }

    mysqli_close($db);
    echo '<div style="width: 90%;height: 55px;margin: 50px">
        <div style="margin: 0 auto;width: 90px;height: 30px;background-color: #117700">
            <a style="text-decoration: none;padding:3px;color: #f3f3f3;text-align: center;display: block"
               href="#" onclick="window.parent.location.reload();">返回</a>
        </div>
    </div>';
    exit();
}

$com = "SELECT * FROM student WHERE sid='$sid'";
$result = mysqli_query($db, $com);
if ($result && $row = mysqli_fetch_object($result)) {
?>
<!DOCTYPE html>
<html lang="cn">
<head>
<meta charset="UTF-8">
<title>编辑信息</title>
<link rel="stylesheet" href="./user.css">
<script>
// 实时计算年龄和年级
function updateAgeAndGrade() {
    const birthInput = document.querySelector('input[name="birth"]');
    const ageInput = document.querySelector('input[name="age"]');
    const gradeSelect = document.querySelector('select[name="current_grade"]');

    if (!birthInput.value) {
        ageInput.value = '';
        gradeSelect.value = '';
        return;
    }

    const birthDate = new Date(birthInput.value);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    ageInput.value = age;

    let grade = age - 5;
    if (grade < 1) grade = 1;
    if (grade > 12) grade = 12;
    gradeSelect.value = grade;
}
</script>
</head>
<body>
<h3>编辑学籍信息</h3>
<form method="post" action="">
    <div><label>姓名：<input class="input-new" name="name" value="<?php echo htmlspecialchars($row->name) ?>"></label></div>
    <div><label>性别：
        <select class="selectbox"  name="sex">
            <option value="男" <?php if($row->sex=='男') echo 'selected'; ?>>男</option>
            <option value="女" <?php if($row->sex=='女') echo 'selected'; ?>>女</option>
        </select>
    </label></div>
    <div><label>出生年月：<input class="input-new" type="date" name="birth" value="<?php echo $row->birth ?>" onchange="updateAgeAndGrade()"></label></div>
    <div><label>年龄：<input class="input-new" name="age" value="<?php echo htmlspecialchars($row->age) ?>" readonly></label></div>

    <h4>教育经历</h4>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr><th>阶段</th><th>起始年月</th><th>结束年月</th><th>学校名称</th></tr>
        <tr>
            <td>小学</td>
            <td><input class="input-new" type="date" name="primary_start_year" value="<?php echo $row->edu_primary_start ?>"></td>
            <td><input class="input-new" type="date" name="primary_end_year" value="<?php echo $row->edu_primary_end ?>"></td>
            <td><input class="input-new" type="text" name="primary_school_name" value="<?php echo htmlspecialchars($row->edu_primary_school) ?>"></td>
        </tr>
        <tr>
            <td>初中</td>
            <td><input class="input-new" type="date" name="junior_start_year" value="<?php echo $row->edu_junior_start ?>"></td>
            <td><input class="input-new" type="date" name="junior_end_year" value="<?php echo $row->edu_junior_end ?>"></td>
            <td><input class="input-new" type="text" name="junior_school_name" value="<?php echo htmlspecialchars($row->edu_junior_school) ?>"></td>
        </tr>
        <tr>
            <td>高中</td>
            <td><input class="input-new" type="date" name="senior_start_year" value="<?php echo $row->edu_senior_start ?>"></td>
            <td><input class="input-new" type="date" name="senior_end_year" value="<?php echo $row->edu_senior_end ?>"></td>
            <td><input class="input-new" type="text" name="senior_school_name" value="<?php echo htmlspecialchars($row->edu_senior_school) ?>"></td>
        </tr>
    </table>

<div>
    <label>当前年级：
        <input class="input-new" type="text" name="current_grade" value="<?php echo htmlspecialchars($row->current_grade) ?> 年级" readonly>
    </label>
</div>
    <div><label>当前学校：<input class="input-new" name="current_school" value="<?php echo htmlspecialchars($row->current_school) ?>"></label></div>

    <h4>家长信息</h4>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr><th></th><th>姓名</th><th>联系方式</th><th>工作单位</th><th>职务职称</th></tr>
        <tr>
            <td>父亲</td>
            <td><input class="input-new" name="father_name" value="<?php echo htmlspecialchars($row->father_name) ?>"></td>
            <td><input class="input-new" name="father_tel" value="<?php echo htmlspecialchars($row->father_tel) ?>"></td>
            <td><input class="input-new" name="father_work_unit" value="<?php echo htmlspecialchars($row->father_workplace) ?>"></td>
            <td><input class="input-new" name="father_title" value="<?php echo htmlspecialchars($row->father_position) ?>"></td>
        </tr>
        <tr>
            <td>母亲</td>
            <td><input class="input-new" name="mother_name" value="<?php echo htmlspecialchars($row->mother_name) ?>"></td>
            <td><input class="input-new" name="mother_tel" value="<?php echo htmlspecialchars($row->mother_tel) ?>"></td>
            <td><input class="input-new" name="mother_work_unit" value="<?php echo htmlspecialchars($row->mother_workplace) ?>"></td>
            <td><input class="input-new" name="mother_title" value="<?php echo htmlspecialchars($row->mother_position) ?>"></td>
        </tr>
    </table>

    <div><label>家中是否有科研人员：
        <input class="input-new" type="checkbox" name="has_researcher" value="1" <?php if($row->has_researcher) echo 'checked'; ?>>
    </label></div>

    <div class="clickbox clearfloat"><input class="input-new" name="submit" type="submit" value="修改信息"></div>
</form>

<script>
// 页面加载时初始化一次
updateAgeAndGrade();
</script>
</body>
</html>
<?php mysqli_close($db); } ?>
