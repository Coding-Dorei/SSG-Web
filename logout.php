<?php
    session_unset();
    session_destroy();
    setcookie('sessionid',$_COOKIE['sessionid'],time()-1);
    echo "<script>location.href='./login.php'</script>";
?>