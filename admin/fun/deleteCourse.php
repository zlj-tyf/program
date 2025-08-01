<?php
require_once("../../config/database.php");

$id = intval($_GET["cid"]);

$sql = "DELETE FROM course WHERE cid = $id";

$result = mysqli_query($db, $sql);

if ($result) {
    echo "<script>alert('删除成功'); window.location.href='../modifyCourse.php';</script>";
} else {
    echo "<script>alert('删除失败'); window.location.href='../modifyCourse.php';</script>";
}

mysqli_close($db);
?>
