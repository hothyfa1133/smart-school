<?php
session_start();
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel</title>

    <!-- Meta -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Icon -->
    <link rel="shortcut icon" href="#" type="image/x-icon" />
    <link rel="apple-touch-icon" href="#">

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> <!-- Icons -->
    <link href="css/fontawesome.css" rel="stylesheet"> <!-- Icons -->
    <link rel="stylesheet" href="css/bootstrap.min.css"> <!-- Bootstrap -->
    <link rel="stylesheet" href="style.css"> <!-- Main Style -->

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,600;0,700;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <script src="js/jquery.js"></script>
</head>
<body>
   <?php
    // including aside
    if(!isset($noAside)){
        include 'templates/_aside.php';
    }
    ?>
    <main class="main-main" <?php if(isset($noAside)){echo 'style="margin-left: 0px;"';}?>>
