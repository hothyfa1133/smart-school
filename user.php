<?php
require 'includes/init.php';

// redirect if wrong page
$allowed_links = ['login', 'signin', 'logout'];
if(!in_array($_GET['page'], $allowed_links)){header("location:" . $_SERVER['PHP_SELF'] . '?page=login');}

// set login function
function setLogin () {
	global $conn;

	// check info
	$email = trim(htmlentities($_POST['email']));
	$password = trim(htmlentities($_POST['password']));

	$checkStmt = $conn->prepare("SELECT
		id
		FROM
		students
		WHERE
		email = ? AND password = ?");
	$checkStmt->execute([$email, sha1($password)]);

	if($checkStmt->rowCount() > 0){ // found

		$student = $checkStmt->fetch();
		$_SESSION['smart_school_user'] = $student['id'];
		$_SESSION['smart_school_position'] = 0;
		header("location:index.php");

	}else{ // not found

		$checkStmt2 = $conn->prepare("SELECT
			id
			FROM
			teachers
			WHERE
			email = ? AND password = ?");
		$checkStmt2->execute([$email, sha1($password)]);
		if($checkStmt2->rowCount() > 0){ // found
			$student = $checkStmt2->fetch();
			$_SESSION['smart_school_user'] = $student['id'];
			$_SESSION['smart_school_position'] = 1;
			header("location:index.php");
		}else{ // not found
			echo message("Please Check Your Email And Password Then Try Again");
		}

	}

}

// add stuudent function
function addStudent () {
    global $conn;
    
    // check info
    $name           = trim(htmlentities($_POST['name']));
    $email          = trim(htmlentities($_POST['email']));
    $student_id     = trim(htmlentities($_POST['student_id']));
    $date           = trim(htmlentities($_POST['birth_date']));
    $password       = trim(htmlentities($_POST['password']));
    $errors         = [];
    
    if(!preg_match("/^[a-zA-Z _]{5,}$/", $name)){$errors[] = 'name must contain 5 charachters at least';}
    if(!preg_match("/^\S+@\S+\.\S+$/", $email)){$errors[] = 'plase write a valid email';}
    if(!preg_match("/^[0-9]+$/", $student_id)){$errors[] = 'plase write a valid id';}
    if(empty($date)){$errors[] = 'please enter the birth date';}
    if(!preg_match("/^(.){8,}$/", $password)){$errors[] = 'password must contain 8 charachters at least';}
    if(!isset($_POST['grade'])){$errors[] = 'Choose Student Grade';}
    
    // check errors
    if(empty($errors)){ // check true
        $grade = trim(htmlentities($_POST['grade']));
    
        try{
            $addStudent = $conn->prepare("INSERT
            INTO
            students
            (name, email, grade, student_id, birth_date, password, date)
            VALUES
            (?, ?, ?, ?, ?, ?, NOW())");
            $addStudent->execute([$name, $email, $grade, $student_id, $date, sha1($password)]);
            if($addStudent->rowCount() > 0){ // success
                echo message("student has added succesfully", true);
            }else{
                echo message("student hasn't add succesfully");
            }
        }
        catch(PDOException $e){
            echo message("Dublicated Student");
        }
        
    }else{ // check false
        
        // loop in errors
        foreach($errors as $error){
            echo message(ucfirst($error));
        }
        
    }
    
}

// handling postr requests
if($_SERVER['REQUEST_METHOD'] === 'POST'){
	if(array_key_exists("login", $_POST)){
		setLogin();
	}else if(array_key_exists('signin', $_POST)){
		addStudent();
	}
}
?>

<div class="container py-5">
	<div class="row">
		<?php
		if($_GET['page'] === 'login'){ // log in page

			// redirect if looged in
			if(isset($_SESSION['smart_school_user'])){
				header("location:index.php");
			}

			?>
			<div class="col-lg-4 col-md-5 col-sm-6 col-12 mx-auto">
				<div class="rounded border">
					<div class="bg-light p-2 pt-3 text-center">
						<h4 style="font-weight: bold">Login To School</h4>
					</div>
					<div class="p-3">
						<form action="<?php echo $_SERVER['PHP_SELF'];?>?page=login" method="post" id="login">
							<div class="mt-3">
								<label class="text-dark" for="email">Email</label>
								<input type="text" class="form-control" id="email" name="email" autofocus placeholder="Email">
							</div>
							<div class="mt-3">
								<label class="text-dark" for="password">Password</label>
								<input type="password" class="form-control" id="password" name="password" autofocus placeholder="Password" autocomplete="new-password">
							</div>
							<div class="mt-3 gap-2 d-grid">
								<button class="btn btn-primary" name="login">Login</button>
								<span class="pl-3"><a href="user.php?page=signin">Sign In</a></span>
							</div>
						</form>
					</div>
				</div>
			</div>
			<?php
		}else if($_GET['page'] === 'signin'){

			?>
			<div class="col-md-8 col-12 mx-auto">
				<div class="rounded border">
					<div class="bg-light p-2 pt-3 text-center">
						<h4 style="font-weight: bold">Student Registeration</h4>
					</div>
					<div class="p-3">
						<form action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'];?>" method="post" id="login">
							<div class="row">
								<div class="col-md-6 col-12 mb-3 mb-md-0">
									<label class="text-dark" for="name">Name</label>
									<input type="text" name="name" placeholder="Name" autofocus id="name" class="form-control">
								</div>
								<div class="col-md-6 col-12">
		                            <label class="text-dark" for="email">Email</label>
		                            <input type="email" name="email" placeholder="Teacher's Email" id="email" class="form-control">
		                            <small class="err-msg email"></small>
		                        </div>
							</div>
		                    <div class="row mt-3">
		                    	<div class="col-md-6 col-12 mb-3 mb-md-0">
                                    <label class="text-dark" for="student_id">Student Id</label>
                                    <input type="number" name="student_id" id="student_id" placeholder="Student Id" class="form-control" value="<?php echo $student['student_id'];?>">
                                    <small class="err-msg id"></small>
                                </div>
                                <div class="col-md-6 col-12 mb-3 mb-md-0">
                                    <label class="text-dark" for="birth_date">Birth Date</label>
                                    <input type="date" name="birth_date" id="birth_date" placeholder="Student Birth" class="form-control" value="<?php echo $student['birth_date'];?>">
                                    <small class="err-msg date"></small>
                                </div>
		                    </div>
		                    <div class="row mt-3">
	                            <div class="col-md-6 col-12">
	                                <label class="text-dark" for="password">Password</label>
	                                <input type="password" name="password" placeholder="Student Password" id="password" class="form-control" autocomplete="new-password">
	                                <small class="err-msg password"></small>
	                            </div>
	                            <div class="col-md-6 col-12">
	                                <label class="text-dark" for="grade">Grade</label>
	                                <select name="grade" id="grade" class="form-control">
	                                    <option value="NULL" disabled selected>Choose One</option>
	                                    <option value="7">Grade 7</option>
	                                    <option value="8">Grade 8</option>
	                                    <option value="9">Grade 9</option>
	                                </select>
	                                <small class="err-msg grade"></small>
	                            </div>
	                        </div>
							<div class="mt-3 gap-2 d-grid">
								<button class="btn btn-primary" name="signin">Sign In</button>
								<span class="pl-3"><a href="user.php">Login</a></span>
							</div>
						</form>
					</div>
				</div>
			</div>
			<?php

		}else if($_GET['page'] === 'logout'){ // log out page

			// redirect if not looged in
			if(!isset($_SESSION['smart_school_user'])){
				header("location:index.php");
			}

			session_destroy();
			unset($_SESSION);
			header("location:index.php");

		}
		?>
	</div>
</div>

<?php include 'templates/_footer.php';?>