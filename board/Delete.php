<?php
    set_include_path('/home/junsu/Web');
    include "db_connect.php";
    session_start();
    if(!$_SESSION[$_COOKIE['sessionid']]){//로그인 안하면 못 씀
        echo "<script>location.href = '/login.php'</script>";
        exit();
    }
    if(strcmp($_SESSION['token'],$_GET['token'])) exit();
    $mysqli = linkDB();
    $userid = $_SESSION[$_COOKIE['sessionid']];
    $bid = $_GET['bid'];
    $stmt = $mysqli->prepare("SELECT title, content, userid from Bulletin where bid = ?");
    $stmt->bind_param("i",$bid);
    $stmt->bind_result($title,$content,$author);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    if(strcmp($userid,$author) != 0){
        unlinkDB($mysqli);
        exit();
    }
    $stmt = $mysqli->prepare("delete from Bulletin where bid = ?");
    $stmt->bind_param("i",$bid);
    $success = $stmt->execute();
    $stmt->close();
    if($success) echo "<script>alert('Success');location.href='/board/Main.php'</script>";
    else{
        echo "<script>alert('Fail');location.href='/board/Main.php'</script>";
    }
    unlinkDB($mysqli);
?>