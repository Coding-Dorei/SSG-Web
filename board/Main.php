<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./board.js"></script>
    <link rel="stylesheet" href="./board.css">
    <title>Board</title>
</head>
<body>
    <?php
        set_include_path('/home/junsu/Web');
        include_once 'header.php';
    ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>제목</th>
                <th>작성일시</th>
                <th>작성자</th>
            </tr>
        </thead>
        <tbody>
            <?php
                include 'db_connect.php';
                session_start();
                $mysqli = linkDB();
                $stmt = $mysqli->prepare("SELECT bid, title, created_at, name from Bulletin,User where Bulletin.userid = User.userid order by bid desc");
                $stmt->bind_result($bid,$title,$created_at,$name);
                $stmt->execute();
                while($stmt->fetch()){
                    echo "<tr class='boardItem' onclick='readDoc(this)'>";
                    echo "<td id='bid'>$bid</td><td>$title</td><td>$created_at</td><td>$name</td>";
                    echo "</tr>";
                }
                $stmt->close();
                unlinkDB($mysqli);
            ?>
        </tbody>
    </table>
</body>
</html>