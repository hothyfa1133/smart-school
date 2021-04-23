<?php
$noLogin = '';
$noAside = true;
require 'includes/init.php';

// redirect if wrong page
$allowed_links = ['login', 'logout'];
if(!isset($_GET['page'])){header("location:" . $_SERVER['PHP_SELF'] . '?page=login');}

// set login function
function setLogin () {
	global $conn;

	// check info
	$email    = trim(htmlentities($_POST['email']));
	$password = trim(htmlentities($_POST['password']));

	$checkStmt = $conn->prepare('SELECT
		id
		FROM
		teachers
		WHERE
		email = ? AND password = ?');
	$checkStmt->execute([$email, sha1($password)]);
	if($checkStmt->rowCount() > 0){ // found
		$_SESSION['smart_school_id'] = $checkStmt->fetch()['id'];
		header('location:index.php');
	}else{
		echo message('Please Check Your Name And Password');
	}
}
?>

<img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?ixid=MXwxMjA3fDB8MHxzZWFyY2h8MXx8aGlnaCUyMHNjaG9vbHxlbnwwfHwwfA%3D%3D&ixlib=rb-1.2.1&w=1000&q=80" style="position: fixed;
    top: 0px;
    right: 0px;
    left: 0px;
    bottom: 0px;
    width: 100%;" alt="background image">
<div class="container mt-5 position-relative">
	<div class="row" style="margin-top: 120px;">
		<div class="col-lg-4 col-md-5 col-sm-9 col-12 mx-auto">
			<div class="bg-white rounded border overflow-hidden shadow">
				<?php
				if($_GET['page'] === 'login'){ // login page

					if(isset($_SESSION['smart_school_id'])){
						header('location:index.php');
					}

					// handling post requests
					if($_SERVER['REQUEST_METHOD'] === 'POST'){
						if(array_key_exists("login", $_POST)){ // login request
							setLogin();
						}
					}

					?>
					<div class="bg-light p-2 pt-3 text-center">
						<h4 class="fw-bold">Login To School</h4>
					</div>
					<div class="p-3">
						<form action="<?php echo $_SERVER['PHP_SELF'];?>?page=login" method="post" id="login">
							<div class="mt-3">
								<label for="email">Email</label>
								<input type="text" class="form-control" id="email" name="email" autofocus placeholder="Email" value="<?php if(isset($_POST['email'])){echo $_POST['email'];}?>">
							</div>
							<div class="mt-3">
								<label for="password">Password</label>
								<input type="password" class="form-control" id="password" name="password" autofocus placeholder="Password" autocomplete="new-password">
							</div>
							<button class="btn btn-primary mt-3" name="login">Login</button>
						</form>
					</div>
					<?php
				}else if($_GET['page'] === 'forget_password'){ // forget password page
					// code gone here
				}else if($_GET['page'] === 'logout'){ // logout page
					unset($_SESSION);
					session_destroy();
					header("location:" . $_SERVER['PHP_SELF']);
				}
				?>
			</div>
		</div>
	</div>
</div>

<?php include 'templates/_footer.php';?>