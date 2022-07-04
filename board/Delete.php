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
    $stmt = $mysqli->prepare("SELECT title, content, userid,haveFile from Bulletin where bid = ?");
    $stmt->bind_param("i",$bid);
    $stmt->bind_result($title,$content,$author,$haveFile);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    if(strcmp($userid,$author) != 0){
        unlinkDB($mysqli);
        exit();
    }
    echo $haveFile;
    if($haveFile=="Y"){
        $stmt = $mysqli->prepare("SELECT filepath from File where File.bid = ?");
        $stmt->bind_param("i",$bid);
        $stmt->bind_result($filepath);
        $success = $stmt->execute();
        if($success){
            $stmt->fetch();
            if(!unlink("/home/junsu/Web/file/$filepath")){
                echo "<script>alert('Fail');location.href='/board/Main.php'</script>";
                exit();
            }
            $stmt->close();
        }else{
            echo "<script>alert('Fail');location.href='/board/Main.php'</script>";
            exit();
        }
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