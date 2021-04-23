<?php ob_start();?>
<!DOCTYPE html>
<!--choose the language -->
<html lang="en">

<head>
    <!-- Basic -->
    
    <!--character set  -->
    <meta charset="utf-8">
    <!--choose what version of Internet Explorer the page should be viewed as.-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">   
   
    <!-- Mobile Metas -->
    <!--This gives the browser instructions how to control the page's dimensions and scaling.-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
 
    <!-- Site Metas -->
    <!--The <meta> tag defines metadata about an HTML document.-->
    <title>Smart School</title>  
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Site Icons -->
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">

    <!-- (load)Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Site CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- ALL VERSION CSS -->
    <link rel="stylesheet" href="css/versions.css">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="css/responsive.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/custom.css">

    <!-- Modernizer for Portfolio ( is a JavaScript library)-->
    <script src="js/modernizer.js"></script>
</head>
<body class="host_version">
    <!-- LOADER -->
    <div id="preloader">
        <div class="loader-container">
            <div class="progress-br float shadow">
                <div class="progress__item"></div>
            </div>
        </div>
    </div>
    <!-- END LOADER -->	
    <!-- Start header -->
    <header class="top-navbar">
        <?php include '_navbar.php';?>
    </header>