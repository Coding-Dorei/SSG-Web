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
        include_once 'header.php';
        session_start();
        if(!$_SESSION[$_COOKIE['sessionid']]){//로그인 안하면 못 씀
            echo "<script>location.href = '/login.php'</script>";
        }
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(strcmp($_SESSION['token'],$_POST['token'])) exit();
            $mysqli = linkDB();
            $stmt = $mysqli->prepare("INSERT INTO Bulletin(title,content,userid,haveFile) values(?,?,?,?)");
            $stmt->bind_param("ssss",$title,$content,$uid,$haveFile);
            $haveFile = "N";
            $title = htmlentities($_POST['title']);
            $content = htmlentities($_POST['content']);
            $uid = $_SESSION[$_COOKIE['sessionid']];
            if($_FILES['file']['name']){//file있음
                try{
                    $haveFile = "Y";
                    $hashname = hash('sha256',$_FILES['file']['name']);
                    $uploaddir = "../file/".$hashname;
                    if(move_uploaded_file($_FILES['file']['tmp_name'],$uploaddir)) {
                        $uploadSuccess = TRUE;
                        echo "<script>alert('success')</script>";
                    }
                    else{
                        echo "<script>alert`$_FILES[file][error]`</script>";
                        $uploaddir = NULL;
                    } 
                }
                catch(Exception $e){
                    echo "<script>alert('File Upload Error')</script>";
                    $uploaddir = NULL;
                }
            }
            $stmt->execute();
            $stmt->close();
            if($uploadSuccess){
                echo "debug";
                $stmt = $mysqli->prepare("INSERT INTO File(bid,filename,filepath) values(?,?,?)");
                $stmt->bind_param("iss",$bid,$_FILES['file']['name'],$hashname);
                $bid = $mysqli->insert_id;
                $stmt->execute();
                $stmt->close();
            }
            unlinkDB($mysqli);
            echo "<script>location.href = '/board/Main.php'</script>";
        }else if($_SERVER['REQUEST_METHOD'] == "GET"){
            $_SESSION['token'] = bin2hex(random_bytes(8));
        }
    ?>
    <form action="" method="post" enctype='multipart/form-data'>
        <input type="hidden" name="token" value=<?php echo $_SESSION['token']; ?>>
        <table>
            <tbody>
                <tr>
                    <td><label for="">제목</label><input type="text" name="title" id="" style="width:85%"><button type="submit" style="width:10%">작성</button></td>
                </tr>
                <tr>
                    <td>
                        <label>첨부파일</label><input type="file" name="file">
                    </td>
                </tr>
                <tr>
                    <td><textarea name="content" id="" cols="140" rows="45"></textarea></td>
                </tr>
            </tbody>
        </table>
    </form>
</body>
</html>