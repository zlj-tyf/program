<?php
session_start();
require_once '../config/database.php';

// 清空日志操作
if (isset($_POST['action']) && $_POST['action'] === 'clear_log') {
    file_put_contents(__DIR__ . '/../config/sql_log.txt', '');
    echo json_encode(['success' => true]);
    exit;
}

// 读取日志
$logs = [];
$log_file = __DIR__ . '/../config/sql_log.txt';
if (file_exists($log_file)) {
    $lines = array_reverse(file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)); // 时间逆序
    foreach ($lines as $line) {
        if (preg_match('/\[(.*?)\] \[User: (.*?)\] \[Page: (.*?)\] (.*)/', $line, $matches)) {
            $logs[] = [
                'time' => $matches[1],
                'user' => $matches[2],
                'page' => $matches[3],
                'type' => strpos($matches[4], 'SQL') !== false ? 'SQL' : '其他',
                'content' => $matches[4]
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<title>日志管理</title>
<link href="https://cdn.jsdelivr.net/npm/gridjs/dist/theme/mermaid.min.css" rel="stylesheet" />
<style>
body { font-family: Arial, sans-serif; padding: 20px; }
button { margin-bottom: 10px; }
</style>
</head>
<body>

<h2>日志管理</h2>

<div>
    <label>显示条目：
        <select id="limitSelect">
            <option value="10">10</option>
            <option value="30">30</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="all">全部</option>
        </select>
    </label>
    <button id="clearLog">清空日志</button>
</div>

<div id="grid"></div>

<script src="https://cdn.jsdelivr.net/npm/gridjs/dist/gridjs.umd.js"></script>
<script>
const logData = <?php echo json_encode($logs, JSON_UNESCAPED_UNICODE); ?>;
let limit = '10';

function renderGrid() {
    new gridjs.Grid({
        columns: [
            { name: '时间', width: '5%' },
            { name: '用户名', width: '5%' },
            { name: '页面', width: '15%' },
            { name: '操作类型', width: '5%' },
            { name: 'SQL内容', width: '50%' }
        ],
        data: logData.map(log => [log.time, log.user, log.page, log.type, log.content]),
        pagination: limit === 'all' ? false : { limit: parseInt(limit) },
        sort: true,
        search: true,
        style: {
            table: { 'width': '100%', 'border-collapse': 'collapse' },
            th: { 'background-color': '#343a40', 'color': '#fff', 'text-align':'left', 'padding':'6px' },
            td: { 'padding': '6px', 'word-break': 'break-word' }
        }
    }).render(document.getElementById("grid"));
}

document.getElementById('limitSelect').addEventListener('change', e => {
    limit = e.target.value;
    document.getElementById('grid').innerHTML = '';
    renderGrid();
});

document.getElementById('clearLog').addEventListener('click', () => {
    if (!confirm('确定要清空日志吗？')) return;
    fetch('', { method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: 'action=clear_log' })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                alert('日志已清空');
                logData.length = 0;
                document.getElementById('grid').innerHTML = '';
                renderGrid();
            } else {
                alert('清空失败');
            }
        });
});

// 初始渲染
renderGrid();
</script>

</body>
</html>
