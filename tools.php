<?php
    // include 'db_connect.php';
    function isValid(string $str): bool
    {//id, password length limitation
        if(strlen($str) >= 8 && strlen($str) <= 20)return TRUE;
        else return FALSE;
    }
?>