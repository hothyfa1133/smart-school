<?php

# contacting messages actions

// require connection file
require '../../includes/connection.php';

// function to get data
function getData ($data) {
    global $conn;

    // get data of entered value
    $getData = $conn->prepare("SELECT
    id, name
    FROM
    students
    WHERE
    id = ?
    OR
    name LIKE '%$data%'
    LIMIT 7");
    $getData->execute([$data]);
    if($getData->rowCount() > 0){ // found
        return $getData->fetchAll();
    }else{
        return 0;
    }
}

// function to get data
function checkdata ($data) {
    global $conn;

    // get data of entered value
    $getData = $conn->prepare("SELECT
    id
    FROM
    students
    WHERE
    id = ?
    OR
    name LIKE '%$data%'
    LIMIT 7");
    $getData->execute([$data]);
    if($getData->rowCount() > 0){ // found
        return $getData->fetch();
    }else{
        return 0;
    }
}

if($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(array_key_exists('value', $_GET)){
        $value = trim(htmlentities($_GET['value']));
        if(!empty($value)){

            echo json_encode(getData($value));

        }
    }else if(array_key_exists('check_name', $_GET)){
        $value = $_GET['check_name'];
        if(!empty($value)){
            echo json_encode(checkData($value));
        }
    }
}