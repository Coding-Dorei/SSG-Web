<?php
    session_start();
    set_include_path('/home/junsu/Web');
    include_once 'header.php';
    include "db_connect.php";
    $mysqli = linkDB();
    $stmt = $mysqli->prepare("SELECT bid, title, content,User.userid, name,haveFile from Bulletin,User where Bulletin.userid = User.userid AND bid = ?");
    $stmt->bind_param("i",$_GET['bid']);
    $stmt->bind_result($bid,$title,$content,$userid,$name,$haveFile);
    $stmt->execute();
    $success = $stmt->fetch();
    $stmt->close();
    unlinkDB($mysqli);
    if($success == NULL){//bid에 해당하는 문서 없음
        echo "<h1>bid Not Found</h1>";
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? $title : "Not Found";?></title>
</head>
<body>
    <div id='buttons' style='display:inline;float:right'>
        <?php
            if(!strcmp($_SESSION[$_COOKIE['sessionid']],$userid)){
                $_SESSION['token'] = bin2hex(random_bytes(8));
                echo "<button id='update' onclick=\"location.href=`\${location.origin}/board/Update.php\${location.search}`\">수정</button>";
                echo "<button id='delete' onclick=\"location.href=`\${location.origin}/board/Delete.php\${location.search}&token=$_SESSION[token]`\">삭제</button>";
            }
        ?>
    </div>
    <div>
        <?php
            echo "<h3>$title by $name</h3>";
            if($haveFile=="Y"){
                echo "debug";
                $mysqli = linkDB();
                $stmt = $mysqli->prepare("SELECT filename, filepath from File where bid = ?");
                $stmt->bind_param("i",$bid);
                $stmt->bind_result($filename,$filepath);
                $stmt->execute();
                $stmt->fetch();
                echo "<h5><a href='/file/$filepath' download='$filename'>$filename</a></h5>";
            }
        ?>
        <div>
            <?php
                $tok = strtok($content,"\n");
                while($tok != false){
                    echo "<p>$tok</p>";
                    $tok = strtok("\n");
                }
            ?>
        </div>
    </div>
    <div>
        <h3>Comments</h3>
        <div>
            <?php
                if(isset($_SESSION[$_COOKIE['sessionid']])){
                    echo "<form action='/comment/write.php' method='post'><input type='hidden' name='token' value=$_SESSION[token]>
                        <input type='hidden' name='bid' value=$bid>
                        <textarea name='comment' cols='120' rows='4' style='display:block'></textarea><button type='submit'>작성</button>
                    </form>";
                }
            ?>
        </div>
        <div style='width:100%'>
            <?php
                $mysqli = linkDB();
                $stmt = $mysqli->prepare("SELECT cid,Comment.userid,name, content, created_at from User, Comment where Comment.userid = User.userid AND Comment.bid = ? order by created_at desc");
                $stmt->bind_param("i",$bid);
                $stmt->bind_result($cid,$userid,$username, $content,$created_at);
                $stmt->execute();
                while($stmt->fetch()){
                    echo "<script>console.log('$content')</script>";
                    echo "<div'>
                    <h4>$username at $created_at</h4>";
                    if(!strcmp($_SESSION[$_COOKIE['sessionid']],$userid)){
                        // <button onclick='location.href=\"/comment/update.php?cid=$cid&token=$_SESSION[token]\"'>수정</button>
                        echo "<div style='float:right;display:inline'>
                        <button onclick='location.href=\"/comment/delete.php?cid=$cid&token=$_SESSION[token]\"'>삭제</button>
                        </div>";
                    } 
                    echo "<pre>$content</pre>
                    </div>";
                }
            ?>
        </div>
    </div>
</body>
</html>