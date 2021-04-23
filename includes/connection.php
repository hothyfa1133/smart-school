<?php

/*
    connectiong to mysql databse
    using pdo class
*/

$host = 'localhost';
$username = 'root';
$password = '';
$db = 'smart_school';

try{
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
}
catch (PDOException $e) {
    die($e->getMessage());
}