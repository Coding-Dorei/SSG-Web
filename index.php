<?php
    include 'db_connect.php';
    include 'tools.php';
    session_start();
    if(!isset($_SESSION[$_COOKIE['sessionid']])){
        echo "<script>location.href='./login.php'</script>";
    }else{
        $uid = $_SESSION[$_COOKIE['sessionid']];
    }
    include_once 'header.php';
?>
<body>
    <h1>CODING_DOREI</h1>
</body>