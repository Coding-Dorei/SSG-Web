<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        set_include_path('/home/junsu/Web');
        include "db_connect.php";
        session_start();
        if(!$_SESSION[$_COOKIE['sessionid']]){//로그인 안하면 못 씀
            echo "<script>location.href = '/login.php'</script>";
            exit();
        }
        $mysqli = linkDB();
        $userid = $_SESSION[$_COOKIE['sessionid']];
        $bid = $_GET['bid'];
        $stmt = $mysqli->prepare("SELECT title, content, userid from Bulletin where bid = ?");
        $stmt->bind_param("i",$bid);
        $stmt->bind_result($title,$content,$author);
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();
        unlinkDB($mysqli);
        if(strcmp($userid,$author) != 0) exit();
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(strcmp($_POST['token'],$_SESSION['token'])) exit();
            $newTitle = htmlentities($_POST['title']);
            $newContent = htmlentities($_POST['content']);
            $mysqli = linkDB();
            $stmt = $mysqli->prepare("Update Bulletin set title = ?, content = ? where bid = ?");
            $stmt->bind_param("ssi",$newTitle,$newContent,$bid);
            $stmt->execute();
            $stmt->close();
            unlinkDB($mysqli);
            echo "<script>location.href = '/board/Main.php'</script>";
        }else if($_SERVER['REQUEST_METHOD'] == "GET"){
            $_SESSION['token'] = bin2hex(random_bytes(8));
        }
    ?>
    <form action="" method="post">
        <input type="hidden" name="token" value=<?php echo $_SESSION['token'];?>>
        <table>
            <tbody>
                <tr>
                    <td><label for="">제목</label><input type="text" name="title" id="" style="width:85%" value=<?php echo "'".$title."'";?>><button type="submit" style="width:10%">작성</button></td>
                </tr>
                <tr>
                    <td><textarea name="content" id="" cols="140" rows="45"><?php echo $content;?></textarea></td>
                </tr>
            </tbody>
        </table>
    </form>
</body>
</html>