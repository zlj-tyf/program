<?php
session_start();
$log_file = __DIR__ . '/sql_log.txt';
$backup_file = __DIR__ . '/sql_log_backup.txt';
$logs = file_exists($log_file) ? file($log_file, FILE_IGNORE_NEW_LINES) : [];

// 筛选条件
$start_time = $_GET['start_time'] ?? '';
$end_time = $_GET['end_time'] ?? '';
$operation = $_GET['operation'] ?? '';
$filtered_logs = [];

// 解析日志
foreach ($logs as $idx => $line) {
    preg_match('/\[(.*?)\] \[User: (.*?)\] \[Page: (.*?)\] (SQL (SUCCESS|FAIL): )?(.*)/', $line, $matches);
    if (!$matches) continue;
    $time = $matches[1];
    $user = $matches[2];
    $page = $matches[3];
    $sql = $matches[6];

    if (($start_time && $time < $start_time) || ($end_time && $time > $end_time)) continue;
    if ($operation && stripos($sql, $operation) !== 0) continue;

    $filtered_logs[$idx] = ['time'=>$time,'user'=>$user,'page'=>$page,'sql'=>$sql];
}

// 生成回档 SQL 函数
function generateRollbackSQL($sql) {
    $sql = trim($sql);
    $type = strtoupper(strtok($sql, " "));
    switch($type) {
        case 'INSERT':
            return "-- 回滚 INSERT: ".$sql."\n-- 需要根据主键删除对应行";
        case 'UPDATE':
            return "-- 回滚 UPDATE: ".$sql."\n-- 需要记录旧值以生成反向 UPDATE";
        case 'DELETE':
            return "-- 回滚 DELETE: ".$sql."\n-- 需要备份删除数据以生成 INSERT";
        default:
            return "-- 其他类型 SQL: ".$sql;
    }
}

// 处理生成回档动作
$rollback_sql = '';
if ($_SERVER['REQUEST_METHOD']=='POST' && !empty($_POST['selected_logs'])) {
    $selected_keys = $_POST['selected_logs'];
    foreach ($selected_keys as $key) {
        if(isset($filtered_logs[$key])){
            $log = $filtered_logs[$key];
            $rollback_sql .= generateRollbackSQL($log['sql'])."\n\n";
            // 写入备份文件
            file_put_contents($backup_file, $log['time']." [User: ".$log['user']."] [Page: ".$log['page']."] ".$log['sql']."\n", FILE_APPEND);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<title>SQL 日志管理</title>
<style>
body{font-family:Arial, sans-serif; margin:20px;}
table{border-collapse:collapse; width:100%;}
th,td{border:1px solid #ccc; padding:5px;}
th{background:#eee;}
input, select, button{margin-right:10px;}
textarea{width:100%;height:250px;}
</style>
</head>
<body>

<h2>SQL 日志管理</h2>

<form method="get">
    开始时间: <input type="datetime-local" name="start_time" value="<?=htmlspecialchars($start_time)?>">
    结束时间: <input type="datetime-local" name="end_time" value="<?=htmlspecialchars($end_time)?>">
    操作类型: 
    <select name="operation">
        <option value="">全部</option>
        <option value="SELECT" <?= $operation=='SELECT'?'selected':''?>>SELECT</option>
        <option value="INSERT" <?= $operation=='INSERT'?'selected':''?>>INSERT</option>
        <option value="UPDATE" <?= $operation=='UPDATE'?'selected':''?>>UPDATE</option>
        <option value="DELETE" <?= $operation=='DELETE'?'selected':''?>>DELETE</option>
    </select>
    <button type="submit">筛选</button>
</form>

<h3>筛选结果 (<?=count($filtered_logs)?> 条)</h3>

<form method="post">
<table>
<tr>
<th><input type="checkbox" id="check_all" onclick="for(c of document.getElementsByName('selected_logs[]')) c.checked=this.checked;"></th>
<th>时间</th><th>用户</th><th>页面</th><th>SQL</th>
</tr>
<?php foreach($filtered_logs as $idx=>$log): ?>
<tr>
<td><input type="checkbox" name="selected_logs[]" value="<?=$idx?>"></td>
<td><?=htmlspecialchars($log['time'])?></td>
<td><?=htmlspecialchars($log['user'])?></td>
<td><?=htmlspecialchars($log['page'])?></td>
<td><pre><?=htmlspecialchars($log['sql'])?></pre></td>
</tr>
<?php endforeach; ?>
</table>
<br>
<button type="submit">生成回档 SQL & 备份选中日志</button>
</form>

<?php if($rollback_sql): ?>
<h3>生成的回档 SQL</h3>
<textarea readonly><?=htmlspecialchars($rollback_sql)?></textarea>
<p>已将选中日志写入备份文件：<b><?=htmlspecialchars($backup_file)?></b></p>
<?php endif; ?>

</body>
</html>
