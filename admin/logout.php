<?php
session_start();
session_unset();
session_destroy();
?>
<script language="javascript">
document.location="../logout.php";
</script>
