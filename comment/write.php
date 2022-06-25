<?php
    set_include_path('/home/junsu/Web');
    include "db_connect.php";
    session_start();
    if(!isset($_SESSION[$_COOKIE['sessionid']]))exit();
    $bid = $_POST['bid'];
    $comment = htmlspecialchars($_POST['comment']);
    $token = $_POST['token'];
    if(strcmp($_SESSION['token'],$token)) exit();
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $mysqli = linkDB();
        $stmt = $mysqli->prepare("INSERT INTO Comment(userid,bid,content) Values(?,?,?)");
        $stmt->bind_param("sis",$_SESSION[$_COOKIE['sessionid']],$bid,$comment);
        $success = $stmt->execute();
        if($success){
            echo "<script>location.href='/board/Read.php?bid=$bid'</script>";
        }
        $stmt->close();
        unlinkDB($mysqli);
    }
?>