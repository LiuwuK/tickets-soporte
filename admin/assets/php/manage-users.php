<?php
    if (isset($_POST['delete'])) {
        $userId = $_POST['user_id'];
    
        if (!empty($userId) && is_numeric($userId)) {
            $query = "delete FROM user WHERE id = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
    
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo "<script>alert('Usuario eliminado con éxito.'); location.reload();</script>";
            };
    
            mysqli_stmt_close($stmt);
        } else {
            echo "<script>alert('ID de usuario no válido.');</script>";
        }
    }
?>