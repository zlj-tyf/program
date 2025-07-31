<?php
//$db = @mysqli_connect("数据库地址","用户名","密码","数据库名")
$db = @mysqli_connect("localhost","root","123456","school")
or die("Fail to connect to Server");