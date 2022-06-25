<?php
    function linkDB(){
        return new mysqli(getenv('DB_HOSTNAME'),getenv('DB_USERNAME'),getenv('DB_PASSWORD'),getenv("DB_NAME"));
    }
    function unlinkDB($conn){
        $conn->close();
    }
?>