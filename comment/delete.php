<?php
    session_start();
    set_include_path('/home/junsu/Web');
    include 'db_connect.php';
    if(!isset($_SESSION[$_COOKIE['sessionid']])) exit();
    if(strcmp($_GET['token'],$_SESSION['token'])) exit();
    if($_SERVER['REQUEST_METHOD'] != "GET") exit();
    $mysqli = linkDB();
    $cid = $_GET['cid'];
    $stmt = $mysqli->prepare("SELECT userid from Comment where cid = ?");
    $stmt->bind_param("i",$cid);
    $stmt->bind_result($userid);
    if(!$stmt->execute()){
        $stmt->close();
        unlinkDB($mysqli);
        exit();
    }
    $stmt->close();
    $stmt = $mysqli->prepare("DELETE FROM Comment where cid = ?");
    $stmt->bind_param("i",$cid);
    $success = $stmt->execute();
    $msg;
    if($success) $msg = "Success";
    else $msg = "Fail";
    $stmt->close();
    unlinkDB($mysqli);
    echo "<script>alert('$msg');history.go(-1)</script>";
?>