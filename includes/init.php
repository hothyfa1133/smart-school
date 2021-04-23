<?php

# initialization page

// starting session
session_start();

// require connection to database file
require 'connection.php';

// require front end messages
include 'functions/front_messages.php';

// including header
include 'templates/_header.php';

// handling logging in
if(isset($login)){ // if isset login variable
	if(!isset($_SESSION['smart_school_user'])){
		header("location:user.php"); // login file
	}
}