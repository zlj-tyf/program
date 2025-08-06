<?php
session_start();
require_once("../../config/database.php");

if (!isset($_SESSION["user"])) {
    echo json_encode([]);
    exit();
}

$sid = $_SESSION["user"];

$sql = "SELECT sc.cid, c.competition_name 
        FROM student_course sc 
        JOIN course c ON sc.cid = c.cid 
        WHERE sc.sid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $sid);
$stmt->execute();
$result = $stmt->get_result();

$options = [];
while ($row = $result->fetch_assoc()) {
    $options[] = [
        "cid" => $row["cid"],
        "name" => $row["competition_name"]
    ];
}
echo json_encode($options);
