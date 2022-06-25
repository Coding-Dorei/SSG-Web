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
        }
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(strcmp($_SESSION['token'],$_POST['token'])) exit();
            $mysqli = linkDB();
            $stmt = $mysqli->prepare("INSERT INTO Bulletin(title,content,userid) values(?,?,?)");
            $stmt->bind_param("sss",$title,$content,$uid);
            $title = htmlentities($_POST['title']);
            $content = htmlentities($_POST['content']);
            $uid = $_SESSION[$_COOKIE['sessionid']];
            $stmt->execute();
            $stmt->close();
            unlinkDB($mysqli);
            echo "<script>location.href = '/board/Main.php'</script>";
        }else if($_SERVER['REQUEST_METHOD'] == "GET"){
            $_SESSION['token'] = bin2hex(random_bytes(8));
        }
    ?>
    <form action="" method="post">
        <input type="hidden" name="token" value=<?php echo $_SESSION['token']; ?>>
        <table>
            <tbody>
                <tr>
                    <td><label for="">제목</label><input type="text" name="title" id="" style="width:85%"><button type="submit" style="width:10%">작성</button></td>
                </tr>
                <tr>
                    <td><label for="">첨부파일</label><input type="file" name="file" id=""></td>
                </tr>
                <tr>
                    <td><textarea name="content" id="" cols="140" rows="45"></textarea></td>
                </tr>
            </tbody>
        </table>
    </form>
</body>
</html>