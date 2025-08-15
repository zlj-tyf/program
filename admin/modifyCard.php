<?php
session_start();
require_once '../config/database.php';

// 获取所有卡片
$cards = [];
$result = $db->query("SELECT id, name FROM card ORDER BY id");
while($row = $result->fetch_assoc()){
    $cards[] = $row;
}

// 处理通过ID加载卡片信息
$loaded_card = null;
$error = null;
$selectedID = 0;

if(isset($_GET['id'])){
    $selectedID = intval($_GET['id']);
} elseif(isset($_POST['id'])){
    $selectedID = intval($_POST['id']);
}

// 搜索处理
$searchResults = [];
$searchQuery = '';
if(isset($_POST['search_query']) && !empty(trim($_POST['search_query']))){
    $searchQuery = trim($_POST['search_query']);
    $stmt = $db->prepare("SELECT id, name FROM card WHERE name LIKE ?");
    $likeQuery = "%$searchQuery%";
    $stmt->bind_param("s", $likeQuery);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()){
        $searchResults[] = $row;
    }
    $stmt->close();
}

// 加载选中卡片
if($selectedID > 0){
    $stmt = $db->prepare("SELECT * FROM card WHERE id=?");
    $stmt->bind_param("i", $selectedID);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res->num_rows > 0){
        $loaded_card = $res->fetch_assoc();
        $allowed_courses = explode(",", $loaded_card["allowed_courses"]);
    } else {
        $error = "未找到对应的卡片 ID。";
    }
    $stmt->close();
}

// 获取所有课程列表
$all_courses = [];
$res_courses = $db->query("SELECT cid, competition_name FROM course ORDER BY cid");
while($row = $res_courses->fetch_assoc()){
    $all_courses[] = $row;
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>卡片管理 >> 修改卡片</title>
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
            width: 260px;
            background: #fff;
            border-right: 1px solid #ddd;
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;
        }
        .sidebar h3 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .sidebar form input[type="number"],
        .sidebar form input[type="text"],
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
            position: relative;
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
        .allowed-courses {
            max-height: 300px; /* 可视高度，可根据需要调整 */
            overflow-y: auto;
            padding: 5px;
            border: 1px solid #ddd;
            background: #fff;
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
        .save-btn {
            position: sticky;
            bottom: 10px;
            display: block;
            margin-top: 10px;
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
        <h3>选择卡片</h3>
        <form method="POST">
            <label>输入卡片 ID:</label>
            <input type="number" name="id" min="1" required value="<?php echo $selectedID; ?>">
            <button type="submit" class="btn">确认</button>
        </form>

        <!-- 搜索功能 -->
        <form method="POST">
            <label>搜索卡片名称:</label>
            <input type="text" name="search_query" value="<?php echo htmlspecialchars($searchQuery); ?>">
            <button type="submit" class="btn">搜索</button>
        </form>

        <hr>
        <h4>所有卡片</h4>
        <?php
        $displayCards = !empty($searchResults) ? $searchResults : $cards;
        foreach($displayCards as $card): ?>
            <a href="?id=<?php echo $card['id']; ?>">
                <?php echo $card['id']." - ".htmlspecialchars($card['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="main">
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if($loaded_card): ?>
            <h3>编辑卡片信息</h3>

            <form action="./fun/deleteCard.php" method="get" onsubmit="return confirm('确定要删除此卡片吗？')" style="margin-bottom:20px;">
                <input type="hidden" name="id" value="<?= $loaded_card["id"] ?>">
                <button type="submit" class="btn btn-danger">删除卡片</button>
            </form>

            <form action="./fun/editCard.php" method="post">
                <input type="hidden" name="id" value="<?= $loaded_card["id"] ?>">

                <div class="form-group">
                    <label>卡片名称：</label>
                    <input name="name" type="text" value="<?= htmlspecialchars($loaded_card["name"]) ?>" required>
                </div>

                <div class="form-group">
                    <label>最大选课额度：</label>
                    <input name="max_courses" type="number" min="1" value="<?= htmlspecialchars($loaded_card["max_courses"] ?? 0) ?>" required>
                </div>

                <div class="form-group">
                    <label>允许使用课程：</label>
                    <div class="allowed-courses">
                        <?php foreach($all_courses as $course): ?>
                            <label>
                                <input type="checkbox" name="allowed_courses[]" value="<?= $course['cid'] ?>" 
                                    <?= in_array($course['cid'], $allowed_courses ?? []) ? "checked" : "" ?>>
                                <?= htmlspecialchars($course['competition_name']) ?>
                            </label><br>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn save-btn">保存修改</button>
            </form>
        <?php else: ?>
            <p>请从左侧选择或输入卡片 ID 进行加载。</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
