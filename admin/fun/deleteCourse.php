<?php
require_once("../../config/database.php");

$id = $_GET["id"];
$sql = "DELETE FROM course WHERE id = $id";

$result = mysqli_query($db, $sql);

if ($result) {
    echo "<script>alert('删除成功'); window.location.href='../modifyCourse.php';</script>";
} else {
    echo "<script>alert('删除失败'); window.location.href='../modifyCourse.php';</script>";
}

mysqli_close($db);
?>
