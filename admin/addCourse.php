<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>课程管理 >> 新增申报项目</title>
    <!-- 引入 CKEditor 5 Classic -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <style>
        /* 页面基础样式 */
        body {
            font-family: "Microsoft YaHei", Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }

        h3.subtitle {
            font-size: 20px;
            margin-bottom: 20px;
            color: #333;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 900px;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .inputbox {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .inputbox span {
            font-weight: bold;
            margin-bottom: 8px;
            color: #555;
        }

        input[type="text"], input[type="number"], textarea {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
            outline: none;
            transition: border 0.3s;
        }

        input[type="text"]:focus, input[type="number"]:focus, textarea:focus {
            border-color: #4a90e2;
        }

        textarea {
            resize: vertical;
        }

        /* 设置指定高度 */
        textarea[name="submit_requirements"],
        textarea[name="student_requirements"] {
            height: 100px;
        }

        textarea[name="default_content"] {
            height: 500px;
        }

        .clickbox {
            display: flex;
            gap: 10px;
        }

        .clickbox input {
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            border: none;
            transition: background-color 0.3s;
        }

        .clickbox input[type="submit"] {
            background-color: #4a90e2;
            color: #fff;
        }

        .clickbox input[type="submit"]:hover {
            background-color: #357ABD;
        }

        .clickbox input[type="reset"] {
            background-color: #ccc;
        }

        .clickbox input[type="reset"]:hover {
            background-color: #999;
        }

        iframe {
            margin-top: 30px;
            border: 1px solid #ccc;
            width: 100%;
            height: 150px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h3 class="subtitle">课程管理 &gt;&gt; 新增申报项目</h3>

    <form action="./fun/addCourse.php" method="post" target="resultbox">
        <div class="inputbox">
            <span>比赛名称：</span>
            <input name="competition_name" type="text" required>
        </div>

        <div class="inputbox">
            <span>比赛简称：</span>
            <input name="competition_short_name" type="text" placeholder="可选">
        </div>

        <div class="inputbox">
            <span>比赛级别：</span>
            <input name="competition_level" type="text" required>
        </div>

        <div class="inputbox">
            <span>申报时间：</span>
            <input name="submit_time" type="text" required>
        </div>

        <div class="inputbox">
            <span>申报要求：</span>
            <textarea name="submit_requirements" required></textarea>
        </div>

        <div class="inputbox">
            <span>学生需提交材料：</span>
            <textarea name="student_requirements" required></textarea>
        </div>

        <div class="inputbox">
            <span>默认页面内容：</span>
            <textarea name="default_content" id="default_content"></textarea>
        </div>

        <div class="clickbox">
            <input name="submit" type="submit" value="提交">
            <input name="reset" type="reset" value="清除">
        </div>
    </form>

    <iframe name="resultbox"></iframe>

    <script>
        ClassicEditor
            .create(document.querySelector('#default_content'))
            .catch(error => console.error(error));
    </script>
</body>
</html>
