<?php
    session_start();
    include 'db_connect.php';
    include 'tools.php';
    function authorize(){
        $mysqli = linkDB();
        $stmt = $mysqli->prepare("SELECT role FROM User WHERE userid=?");
        $stmt->bind_param("s",$_SESSION[$_COOKIE['sessionid']]);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        unlinkDB($mysqli);
        return $result->fetch_assoc()['role'];
    }
    function UserList(){
        if(!strcmp(authorize(),"admin")){
            echo "<thead>
                    <tr>
                        <th>
                            User ID
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Role
                        </th>
                    </tr>
                </thead>";
            $mysqli = linkDB();
            $stmt = $mysqli->prepare("SELECT * FROM User");
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            unlinkDB($mysqli);
            echo "<tbody>";
            while($row = $result->fetch_assoc()){
                printf("<tr><td><input type='submit' value='%s' name='userManage'></td><td>%s</td><td>%s</td></tr>\n",$row['userid'],$row['name'],$row['role']);
            }
            $csrftoken = bin2hex(random_bytes(8));
            $_SESSION['csrftoken'] = $csrftoken;
            printf("<input type='text' value='%s' name='csrftoken' hidden>",$csrftoken);
            echo "</tbody>";
        }else{
            echo "<script>alert('안됨')</script>";
        }
    }
    if(isset($_SESSION[$_COOKIE['sessionid']])){
        // echo authorize();
    }else{//not logged in
        echo "<script>location.href='./login.php'</script>";
    }
?>
<html>
    <head>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <header>
            <h1></h1>
        </header>
        <nav>
            <ul>
                <li class="nav-item">
                    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
                        <input type="submit" value="UserList" name="management">
                    </form>
                </li>
            </ul>
        </nav>
        <section>
            <h2><?php echo $_POST['management'];?></h2>
            <form action="" method="post">
                <table class="infoTable">
                    <?php
                        if(isset($_POST['management'])){
                            UserList();
                        }else if(isset($_POST['userManage'])){
                            if(!strcmp(authorize(),"admin") && !strcmp($_SESSION['csrftoken'],$_POST['csrftoken'])){
                                $mysqli = linkDB();
                                $stmt = $mysqli->prepare("SELECT * FROM User WHERE userid=?");
                                $stmt->bind_param("s",$_POST['userManage']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                echo "<thead>
                                <tr><th>User ID</th><th>Name</th><th>New Password</th><th>Role</th></tr>
                                </thead><tbody>";
                                while($row = $result->fetch_assoc()){
                                    printf("<tr><td><input type='text' value='%s' name='uid'></td><td><input type='text' value='%s' name='name'></td><td><input type='password' name='upw'></td><td><input type='text' value='%s' name='role'></td></tr>\n",$row['userid'],$row['name'],$row['role']);
                                }
                                printf("</tbody><input type='submit' value='submit' name='change'><input type='submit' value='delete' name='delete'><input type='text' value='%s' name='target' hidden><input type='text' name='csrftoken' value='%s' hidden>",$_POST['userManage'],$_SESSION['csrftoken']);
                                $stmt->close();
                                unlinkDB($mysqli);
                            }
                        }else if(isset($_POST['change'])){
                            if(!strcmp(authorize(),"admin") && !strcmp($_SESSION['csrftoken'],$_POST['csrftoken'])){
                                $uid = $_POST['target'];
                                $new_uid = $_POST['uid'];
                                $new_upw = $_POST['upw'];
                                $new_name = $_POST['name'];
                                $new_role = $_POST['role'];
                                if(!isValid($new_uid) || (!(strlen($new_upw)==0) && !isValid($new_upw))){
                                    printf("<script>alert('아이디:8~20자 비밀번호:8~20자 %d')</script>",isValid($new_uid));
                                    exit();
                                }
                                $mysqli = linkDB();
                                $stmt;
                                if(!isValid($new_upw)){//blank
                                    $stmt = $mysqli->prepare("UPDATE User SET userid=?,name=?,role=? WHERE userid=?");
                                    $stmt->bind_param("ssss",$new_uid,$new_name,$new_role,$uid);
                                }else{
                                    $stmt = $mysqli->prepare("UPDATE User SET userid=?,userpw=?,name=?,role=? WHERE userid=?");
                                    $stmt->bind_param("sssss",$new_uid,hash('sha256',$new_upw),$new_name,$new_role,$uid);
                                }
                                $stmt->execute();
                                $stmt->close();
                                unlinkDB($mysqli);
                            }
                        }else if($_POST['delete']){
                            $mysqli = linkDB();
                            $stmt = $mysqli->prepare("DELETE FROM User WHERE userid = ?");
                            $stmt->bind_param("s",$_POST['target']);
                            $stmt->execute();
                            $stmt->close();
                            unlinkDB($mysqli);
                        }
                    ?>
                </table>
            </form>
        </section>
    </body>
</html>