<?php
require_once("../config/database.php");

// 获取所有课程
$courses_result = mysqli_query($db, "SELECT cid, competition_name FROM course ORDER BY cid");

// 获取所有卡片
$cards_result = mysqli_query($db, "SELECT id, name FROM card ORDER BY id");

$loaded_card = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["load_by_id"])) {
    $load_id = intval($_POST["load_by_id"]);
    $card_res = mysqli_query($db, "SELECT * FROM card WHERE id = $load_id");
    if (mysqli_num_rows($card_res) > 0) {
        $loaded_card = mysqli_fetch_assoc($card_res);
        $allowed_courses = explode(",", $loaded_card["allowed_courses"]);
    } else {
        $error = "未找到对应的卡片 ID。";
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8" />
<title>卡片管理 >> 修改卡片</title>
<style>
    /* 仅补充右边面板滚动和调整checkbox列表宽度 */
    .right-panel {
        float: left;
        width: 19%;
        background-color: #DDDDDD;
        height: 700px;
        overflow-y: auto;
        padding: 10px 10px 10px 20px;
        box-sizing: border-box;
        margin-left: 20px;
    }
    .left-panel {
        float: left;
        width: 78%;
        background-color: #ecf0f3;
        padding: 10px 20px;
        box-sizing: border-box;
        min-height: 700px;
    }
    .checkbox-list {
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #ccc;
        background-color: #fff;
        padding: 6px;
        width: 320px; /* 保持宽度 */
    }
    .checkbox-list label {
        display: block;
        margin-bottom: 4px;
        cursor: pointer;
    }
    .error-msg {
        color: red;
        margin-left: 20px;
        float: left;
        clear: both;
    }
    /* 避免inputbox内宽度冲突 */
    .inputbox {
        width: auto !important;
        margin: 20px 0 10px 0 !important;
        float: none !important;
        display: flex !important;
        align-items: center;
        gap: 10px;
    }
    .inputbox span {
        width: 110px !important;
        float: none !important;
        display: inline-block !important;
        text-align: right !important;
    }
    .inputbox input[type="text"],
    .inputbox input[type="number"] {
        width: 300px !important;
        float: none !important;
    }
    input[type="submit"] {
        width: 120px !important;
        margin-left: 110px;
        background-color: #117700 !important;
        color: #fff !important;
        border: none !important;
        border-radius: 4px !important;
        height: 35px !important;
        cursor: pointer !important;
    }
    input[type="submit"]:hover {
        background-color: #0a4d00 !important;
    }
    table {
        width: 100% !important;
        margin: 0 !important;
        font-size: 14px !important;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 6px 8px;
        text-align: left;
    }
    th {
        background-color: #70a0d0;
        color: white;
        position: sticky;
        top: 0;
        z-index: 1;
    }
</style>
</head>
<body>

<h3 class="subtitle">卡片管理 >> 修改卡片</h3>

<div class="container">
    <div class="left-panel">
        <form method="post">
            <div class="inputbox">
                <span>输入卡片 ID：</span>
                <input type="number" name="load_by_id" min="1" required>
                <input type="submit" value="加载">
            </div>
        </form>

        <?php if ($error): ?>
            <p class="error-msg"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($loaded_card): ?>
            <form action="./fun/editCard.php" method="post" onsubmit="return confirm('确定保存修改吗？')">
                <input type="hidden" name="id" value="<?= (int)$loaded_card["id"] ?>">
                
                <div class="inputbox">
                    <span>卡片名称：</span>
                    <input type="text" name="name" required value="<?= htmlspecialchars($loaded_card["name"]) ?>">
                </div>
                
                <div class="inputbox" style="flex-direction: column; align-items: flex-start; width: auto;">
                    <span style="width: auto; margin-bottom: 5px;">允许报名的比赛：</span>
                    <div class="checkbox-list">
                        <?php foreach ($courses_result as $course): ?>
                            <?php
                                $cid = $course["cid"];
                                $cname = $course["competition_name"];
                                $checked = in_array($cid, $allowed_courses) ? "checked" : "";
                            ?>
                            <label>
                                <input type="checkbox" name="allowed_courses[]" value="<?= $cid ?>" <?= $checked ?>>
                                <?= htmlspecialchars($cid . " - " . $cname) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="inputbox">
                    <span>最大可报名课程数：</span>
                    <input type="number" name="max_courses" min="0" required value="<?= (int)$loaded_card["max_courses"] ?>">
                </div>

                <div class="inputbox">
                    <input type="submit" value="保存修改">
                </div>
            </form>
        <?php endif; ?>
    </div>

    <div class="right-panel">
        <h4>已有卡片类型（ID - 名称）</h4>
        <table>
            <thead>
                <tr><th>ID</th><th>名称</th></tr>
            </thead>
            <tbody>
                <?php while ($card = mysqli_fetch_assoc($cards_result)): ?>
                <tr>
                    <td><?= htmlspecialchars($card["id"]) ?></td>
                    <td><?= htmlspecialchars($card["name"]) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="clearfloat"></div>
</div>

</body>
</html>
