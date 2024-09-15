<?php
    $server = 'localhost';
    $user = 'root';
    $pass = '';
    $database = 'xettuyen';
    $connect = new mysqli($server, $user,$pass, $database);
    if($connect){
        mysqli_query($connect, "SET NAMES 'utf8' ");
    } else {
        echo "connect unsuccessful";
    }

    
?>