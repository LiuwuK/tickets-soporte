<?php
//usuarios
$query = "SELECT * FROM user";
$userData = $con->prepare($query);
$userData->execute();
$result = $userData->get_result();
?>