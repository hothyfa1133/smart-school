<?php

# initialization page

// require connection file
require '../includes/connection.php';

// including messages function
include '../functions/front_messages.php';

// including header
include 'templates/_header.php';

// setting logging in
if(!isset($noLogin)){
	if(isset($_SESSION['smart_school_id'])){ // is logged in

		$checkUser = $conn->prepare("SELECT
			id
			FROM
			teachers
			WHERE
			id = ?");
		$checkUser->execute([$_SESSION['smart_school_id']]);
		if($checkUser->rowCount() == 0){
			header("location:user.php");
		}

	}else{ // not logged in
		header("location:user.php");
	}
}

if(isset($forAdmin)){ // this page will be accesed from the admin only
	// get position of user
	$getPosition = $conn->prepare("SELECT
		position
		FROM
		teachers
		WHERE
		id = ?");
	$getPosition->execute([$_SESSION['smart_school_id']]);
	if($getPosition->fetchColumn() == 0){ // normal teacher
		header('location:index.php');
	}
}