<?php
    require_once 'login.php';
    
    function createDatabase($hn, $un, $pw, $db)
    {
        $conn = new mysqli($hn, $un, $pw);
        if ($conn->connect_error) die($conn->connect_error);
        $drop = "DROP DATABASE ". $db;
        $create = "CREATE DATABASE ". $db;
        
        // Uncomment these line if user wants to DROP the old database
//        if ($conn->query($drop) === TRUE)             
//        {
//            echo "Dropped the old database <br>";
//        }

        if ($conn->query($create) === TRUE)
        {
            echo "Database has been created <br>";    
        }

        $conn->close();

    }

    function createTable($hn, $un, $pw, $db, $qr)
    {
        $conn = new mysqli($hn, $un, $pw, $db);
        if ($conn->connect_error) die($conn->connect_error);
        if ($conn->query($qr) === TRUE)
        {
            echo "A table has been created <br><br><br>";
        }
        $conn->close();
    }

    function addUser($conn, $username, $email, $pass)
    {
        $a = 3;
        $qr = "INSERT INTO users(username, email, password) VALUES('$username', '$email', '$pass')";
        $result = $conn->query($qr);
        if (!$result) die ($conn->error);
    }


    $db = "users";
    $qr1 = 'CREATE TABLE users(
                    id INT NOT NULL AUTO_INCREMENT KEY,
                    username varchar(50) NOT NULL,
                    email varchar(50) NOT NULL,
                    password varchar(50) NOT NULL
            )';
    $qr2 = 'CREATE TABLE files(
                    userid INT NOT NULL,
                    name varchar(20) NOT NULL,
                    content TEXT
            )';
    createDatabase($hn, $un, $pw,$db);
    createTable($hn, $un, $pw, $db, $qr1);
    createTable($hn, $un, $pw, $db, $qr2)
    
?>