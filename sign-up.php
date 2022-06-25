<html>
    <head>
        <title>Sign Up</title>
    </head>
    <body>
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
            <fieldset>
                <legend>Sign Up</legend>
                <table>
                    <tr>
                        <td><label for="">ID</label></td>
                        <td><input type="text" name="uid"></td>
                    </tr>
                    <tr>
                        <td><label for="">Password</label></td>
                        <td><input type="password" name="upw"></td>
                    </tr>
                    <tr>
                        <td><label for="">confirm</label></td>
                        <td><input type="password" name="confirm"></td>
                    </tr>
                    <tr>
                        <td><label for="">name</label></td>
                        <td><input type="text" name="name"></td>
                    </tr>
                </table>
                <input type="submit" value="SUBMIT">
            </fieldset>
        </form>
        <?php
            include 'db_connect.php';
            include 'tools.php';
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                $mysqli = linkDB();
                $uid = $_POST['uid'];
                $upw = $_POST['upw'];
                $confirm = $_POST['confirm'];
                $name = htmlentities($_POST['name']);
                if(!isValid($uid) || !isValid($upw)){
                    echo "<script>alert('아이디:8~20자 비밀번호:8~20자')</script>";
                    exit();
                }
                $stmt = $mysqli->prepare("SELECT userid FROM User WHERE User.userid = ?");
                $stmt->bind_param("s",$uid);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                if($result->num_rows!=0){
                    echo "<script>alert('이미 존재하는 아이디입니다')</script>";
                }else{
                    if(!strcmp($upw,$confirm)){
                        $signUp = $mysqli->prepare("INSERT INTO User(userid,userpw,name,role) VALUES(?,?,?,'user')");
                        $signUp->bind_param("sss",$uid,hash("sha256",$upw),$name);
                        $signUp->execute();
                        $signUp->close();
                        echo "<script>alert('회원가입성공 로그인 페이지로 이동합니다');location.href='./login.php'</script>";
                    }else{
                        echo "<script>alert('비밀번호를 다시 확인해주세요')</script>";
                    }
                }
                unlinkDB($mysqli);
            }
        ?>
    </body>
</html>
