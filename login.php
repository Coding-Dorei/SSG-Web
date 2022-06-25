<html>
    <head>
        <title>Log In</title>
    </head>
    <body>
        <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
            <fieldset>
                <legend>Log In</legend>
                <table>
                    <tr>
                        <td><label for="">ID</label></td>
                        <td><input type="text" name="uid"></td>
                    </tr>
                    <tr>
                        <td><label for="">Password</label></td>
                        <td><input type="password" name="upw"></td>
                    </tr>
                </table>
                <input type="submit" value="SUBMIT">
            </fieldset>
        </form>
        <a href="./sign-up.php">sign up</a>
        <?php
            include 'db_connect.php';
            session_start();
            if(isset($_SESSION[$_COOKIE['sessionid']])){
                echo "<script>location.href='./index.php'</script>";
                echo "<a href='./logout.php'>log out</a>";
            }
            else if($_SERVER["REQUEST_METHOD"] == "POST"){
                echo "debug";
                $mysqli = linkDB();
                $uid = $_POST['uid'];
                $upw = $_POST['upw'];
                $stmt = $mysqli->prepare("SELECT userpw FROM User WHERE userid = ?");
                $stmt->bind_param("s",$uid);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                if($result->num_rows == 1){
                    $row = $result->fetch_assoc();
                    if(!strcmp(hash('sha256',$upw),$row['userpw'])){
                        $randomHex = bin2hex(random_bytes(8));
                        setcookie("sessionid",$randomHex,time() + 60*60);
                        $_SESSION[$randomHex] = $uid;
                        echo "<script>location.href='/board/Main.php'</script>";
                    }
                }else{
                    echo "<script>alert('로그인 실패')</script>";
                }
                unlinkDB($mysqli);
            }
        ?>
    </body>
</html>
