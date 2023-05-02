<?php
    include('secrets.php');

    $dsn = "mysql:host=courses;dbname=z1937658";
    $db1 = new PDO($dsn, $username, $password);
    $db2 = new PDO("mysql:host=blitz.cs.niu.edu;dbname=csci467", 'student', 'student');
?>