<?php
$con=mysqli_connect("localhost", "root", "", "test");
mysqli_set_charset($con, "utf8mb4");
if(mysqli_connect_errno()){
    echo "Connection Fail".mysqli_connect_error();
}
