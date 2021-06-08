<?php
$login = true;
require 'includes/init.php';

// get teacher by id function
function getTeacher ($id) {
    global $conn;
    
    // get teacher by id
    $getTeacher = $conn->prepare("SELECT
    name, image, subject, phone, email, fb_link, gender, date, position
    FROM
    teachers
    WHERE
    id = ?");
    $getTeacher->execute([$id]);
    if($getTeacher->rowCount() > 0){
        return $getTeacher->fetch();
    }else{
        return 0;
    }
}

// update teacher function
function updateTeacher () {
    global $conn;
    
    // validate inputs
    $name       = trim(htmlentities($_POST['name']));
    $phone      = trim(htmlentities($_POST['phone']));
    $email      = trim(htmlentities($_POST['email']));
    $fb         = trim(htmlentities($_POST['fb']));
    $image      = $_FILES['image'];
    $errors     = [];
    
    if(!preg_match("/^([a-zA-Z_ ]+)$/", $name)){$errors[] = 'Enter Teacher Valid Name';}
    if(!isset($_POST['subject'])){$errors[] = 'Choose Teacher\'s Subject';}
    if(!preg_match("/^([0-9]){6,}$/", $phone)){$errors[] = 'Please Enter Correct Phone Number';}
    if(!preg_match("/^\S+@\S+\.\S+$/", $email)){$errors[] = 'Please Enter Correct Email';}
    if(!isset($_POST['gender'])){$errors[] = 'Choose Teacher\'s Gender';}
    if(!empty($_POST['password'])){
        if(!preg_match("/^(.){8,}$/", $_POST['password'])){
            $errors[] = 'Password Must Contain 8 Charachters At Least';
        }
    }
    
    if($image['name'] !== ""){ // has update image
        
        // extension
        @$extension = strtolower(end(explode(".", $image['name'])));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if(!in_array($extension, $allowed_extensions)){ // error in extension
            $errors[] = 'Images Allowed Extinsions Is <br>' . implode(", ", $allowed_extensions) . ' Only';
        }
        
        // size
        $maxSize = 5;
        if($image['size'] / 1048576 > $maxSize){$errors[] = 'Max Image Size Is ' . $maxSize . ' Megabytes';}
        
        // errors
        if($image['error'] > 0){$errors[] = 'Error During Uploading Image';}
        
    }

    if(empty($errors)){ // check true

        // upload image
        try{
            if($image['name'] === ""){ // empty
                $t_image = $_POST['old_image'];
            }else{
                
                if(file_exists("images/teachers/" . $_POST['old_image'])){
                    unlink("images/teachers/" . $_POST['old_image']);
                }
                
                $t_image = rand(1000, 80000) . '_' . $image['name'];
                move_uploaded_file($image['tmp_name'], "images/teachers/" . $t_image);
                
            }
        }
        catch(Exception $e){
            echo message("Error During Uploading Image");
        }
        
        try{
            
            // update password if has set
            if(!empty($_POST['password'])){
                $upPassword = $conn->prepare("UPDATE
                    teachers
                    SET
                    password = ?
                    WHERE
                    id = ?");
                $upPassword->execute([sha1($_POST['password']), $_POST['id']]);
            }

            $updateStmt = $conn->prepare("UPDATE
            teachers
            SET
            name = ?,
            subject = ?,
            phone = ?,
            email = ?,
            gender = ?,
            fb_link = ?,
            image = ?
            WHERE id = ?");
            $updateStmt->execute([
                $name,
                trim(htmlentities($_POST['subject'])),
                $phone,
                $email,
                trim(htmlentities($_POST['gender'])),
                $fb,
                $t_image,
                $_POST['id']
            ]);
            if($updateStmt->rowCount() > 0 || (isset($upPassword) && $upPassword->rowCount() > 0)){
                echo message("Teacher's Info Has Updated Succesfully", true);
            }else{
                echo message("Teacher's Info Has Not Updated Succesfully");
            }
        }
        catch(PDOException $e){
            echo message("Unexpected Error Has Happened");
        }
        
    }else{ // check false
        foreach($errors as $error){
            echo message($error);
        }
    }
}

// update admin function 
function updateAdmin () {
    global $conn;

    // validate inputs
    $name       = trim(htmlentities($_POST['name']));
    $phone      = trim(htmlentities($_POST['phone']));
    $email      = trim(htmlentities($_POST['email']));
    $image      = $_FILES['image'];
    $errors     = [];
    
    if(!preg_match("/^([a-zA-Z_ ]+)$/", $name)){$errors[] = 'Enter Teacher Valid Name';}
    if(!preg_match("/^([0-9]){6,}$/", $phone)){$errors[] = 'Please Enter Correct Phone Number';}
    if(!preg_match("/^\S+@\S+\.\S+$/", $email)){$errors[] = 'Please Enter Correct Email';}
    if(!isset($_POST['gender'])){$errors[] = 'Choose Teacher\'s Gender';}
    if(!empty($_POST['password'])){
        if(!preg_match("/^(.){8,}$/", $_POST['password'])){
            $errors[] = 'Password Must Contain 8 Charachters At Least';
        }
    }
    
    if($image['name'] !== ""){ // has update image
        
        // extension
        @$extension = strtolower(end(explode(".", $image['name'])));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if(!in_array($extension, $allowed_extensions)){ // error in extension
            $errors[] = 'Images Allowed Extinsions Is <br>' . implode(", ", $allowed_extensions) . ' Only';
        }
        
        // size
        $maxSize = 5;
        if($image['size'] / 1048576 > $maxSize){$errors[] = 'Max Image Size Is ' . $maxSize . ' Megabytes';}
        
        // errors
        if($image['error'] > 0){$errors[] = 'Error During Uploading Image';}
        
    }

    if(empty($errors)){ // check true

        // upload image
        try{
            if($image['name'] === ""){ // empty
                $t_image = $_POST['old_image'];
            }else{
                
                if(file_exists("images/teachers/" . $_POST['old_image'])){
                    unlink("images/teachers/" . $_POST['old_image']);
                }
                
                $t_image = rand(1000, 80000) . '_' . $image['name'];
                move_uploaded_file($image['tmp_name'], "images/teachers/" . $t_image);
                
            }
        }
        catch(Exception $e){
            echo message("Error During Uploading Image");
        }
        
        try{
            
            // update password if has set
            if(!empty($_POST['password'])){
                $upPassword = $conn->prepare("UPDATE
                    teachers
                    SET
                    password = ?
                    WHERE
                    id = ?");
                $upPassword->execute([sha1($_POST['password']), $_POST['id']]);
            }

            $updateStmt = $conn->prepare("UPDATE
            teachers
            SET
            name = ?,
            phone = ?,
            email = ?,
            gender = ?,
            image = ?
            WHERE id = ?");
            $updateStmt->execute([
                $name,
                $phone,
                $email,
                trim(htmlentities($_POST['gender'])),
                $t_image,
                $_POST['id']
            ]);
            if($updateStmt->rowCount() > 0 || (isset($upPassword) && $upPassword->rowCount() > 0)){
                echo message("Admin's Info Has Updated Succesfully", true);
            }else{
                echo message("Admin's Info Has Not Updated Succesfully");
            }
        }
        catch(PDOException $e){
            echo message("Unexpected Error Has Happened");
        }
        
    }else{ // check false
        foreach($errors as $error){
            echo message($error);
        }
    }
}

// get subjects function
function getSubjects () {
    global $conn;
    
    // get all subjects
    $getSubjects = $conn->prepare("SELECT
    id, subject
    FROM
    subjects
    ORDER BY subject ASC");
    $getSubjects->execute();
    if($getSubjects->rowCount() > 0){
        return $getSubjects->fetchAll();
    }else{
        return 0;
    }
}

// get student info by id
function getStudent ($id) {
    global $conn;
    
    // get student by id
    $getStudent = $conn->prepare("SELECT
    name, id, student_id, email, birth_date, grade
    FROM
    students
    WHERE id = ?");
    $getStudent->execute([$id]);
    if($getStudent->rowCount() > 0){
        return $getStudent->fetch();
    }else{
        return 0;
    }
}

// update student info
function updateStudent () {
    global $conn;
    
    // check info
    $name           = trim(htmlentities($_POST['name']));
    $student_id     = trim(htmlentities($_POST['student_id']));
    $email          = trim(htmlentities($_POST['email']));
    $birth_date     = trim(htmlentities($_POST['birth_date']));
    $id             = trim(htmlentities($_POST['id']));
    $errors         = [];
    
    if(!preg_match("/^[a-zA-Z _]{5,}$/", $name)){$errors[] = 'name must contain 5 charachters at least';}
    if(!preg_match("/^\S+@\S+\.\S+$/", $email)){$errors[] = 'plase write a valid email';}
    if(!preg_match("/^[0-9]+$/", $student_id)){$errors[] = 'plase write a valid id';}
    if(!preg_match("/^[0-9]+$/", $id)){$errors[] = 'error in id';}
    if(!isset($_POST['grade'])){$errors[] = 'Choose Student Grade';}
    
    // check in errors
    if(empty($errors)){ // check true
        $grade = trim(htmlentities($_POST['grade']));
        
        $updateStudent = $conn->prepare("UPDATE
        students
        SET
        name = ?,
        student_id = ?,
        email = ?,
        grade = ?,
        birth_date = ?
        WHERE id = ?");
        $updateStudent->execute([$name, $student_id, $email, $grade, $birth_date, $id]);
        
        if($updateStudent->rowCount() > 0){
            echo message("Student Info Has Updated Succesfully", true);
        }else{
            echo message("Student Info Has Not Updated Succesfully");
        }
        
    }else{ // check false
        foreach($errors as $error){
            echo message($error);
        }
    }
    
}

?>

<div class="all-title-box">
    <div class="container text-center">
        <h1>
           Edit Profile
           <span class="m_1">Edit And Update Your Profile Info</span>
        </h1>
    </div>
</div>
<div class="container">
	<div class="p-3">
		<h1>
			<small><strong>Edit Profile</strong></small>
		</h1>
		<?php
		if($_SESSION['smart_school_position'] === 1){ // teacher

			// handling post requests
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                if(array_key_exists("edit_teacher", $_POST)){ // edit teacher request
                    if($_POST['position'] == 0){ // teacher
                        updateTeacher();
                    }else if($_POST['position'] == 1){ // admin
                        updateAdmin();
                    }
                }
            }

			$teacher = getTeacher($_SESSION['smart_school_user']);
			if($teacher !== 0){
				?>
				<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" id="edit-teacher" enctype="multipart/form-data">
		           <input type="hidden" name="id" value="<?php echo $_SESSION['smart_school_user'];?>">
		           <input type="hidden" name="old_image" value="<?php echo $teacher['image'];?>">
		           <input type="hidden" name="position" value="<?php echo $teacher['position'];?>">
		            <div class="row">
		                <div class="col-md-6 col-12">
		                    <label for="name">Name</label>
		                    <input type="text" name="name" autofocus placeholder="Teacher's Name" id="name" class="form-control" value="<?php echo $teacher['name'];?>">
		                    <small class="err-msg name"></small>
		                </div>
		                <div class="col-md-6 col-12 mb-3 mb-md-0">
		                    <label for="phone">Phone</label>
		                    <input type="number" name="phone" autofocus placeholder="Teacher's Phone" id="phone" class="form-control" value="<?php echo $teacher['phone'];?>">
		                    <small class="err-msg phone"></small>
		                </div>
		            </div>
		            <div class="row mt-0">
		                <div class="col-md-6 col-12">
		                    <label for="email">Email</label>
		                    <input type="email" name="email" placeholder="Teacher's Email" id="email" class="form-control" value="<?php echo $teacher['email'];?>">
		                    <small class="err-msg email"></small>
		                </div>
		                <div class="col-md-6 col-12 mb-3 mb-md-0">
		                    <label for="gender">Gender</label>
		                    <select class="form-control" id="gender" name="gender">
		                        <option value="NULL" disabled>Choose Gender</option>
		                        <option <?php if($teacher['gender'] == 0){echo 'selected';}?> value="0">Male</option>
		                        <option <?php if($teacher['gender'] == 1){echo 'selected';}?> value="1">Female</option>
		                    </select>
		                    <small class="err-msg gender"></small>
		                </div>
		            </div>
		            <?php
		            if($teacher['position'] == 0){
		                ?>
		                <div class="row mt-0">
		                    <div class="col-md-6 col-12 mt-0 mt-md-0">
		                        <label for="subject">Subject</label>
		                        <select name="subject" id="subject" class="form-control">
		                            <option value="NULL" disabled>Choose Subject</option>
		                            <?php
		                            if(getSubjects() != 0){ // not empty result

		                                // loop on result
		                                foreach(getSubjects() as $subject){
		                                    ?>
		                                    <option <?php if($subject['id'] === $teacher['subject']){echo 'selected';}?> value="<?php echo $subject['id'];?>"><?php echo $subject['subject'];?></option>
		                                    <?php
		                                }

		                            }
		                            ?>
		                        </select>
		                        <small class="err-msg subject"></small>
		                    </div>
		                    <div class="col-md-6 col-12">
		                        <label for="fb">Facebook Link</label>
		                        <input type="url" name="fb" id="fb" class="form-control" placeholder="Teacher's Facebook Link" pattern="https://.*" value="<?php echo $teacher['fb_link'];?>">
		                        <small class="err-msg fb"></small>
		                    </div>
		                </div>
		                <?php
		            }
		            ?>
		            <div class="row mt-0">
	                    <div class="col-12">
	                        <label for="password">New Password</label>
	                        <input type="password" name="password" placeholder="Write a New Password" id="password" class="form-control">
	                        <small class="err-msg password"></small>
	                    </div>
	                </div>
		            <div class="row mt-0">
		                <div class="col-12">
		                    <label for="image">Teacher's Image</label>
		                    <input class="form-control" type="file" id="image" name="image">
		                </div>
		            </div>
		            <div class="row mt-4">
		                <div class="col-12">
		                	<button class="btn btn-success btn-block" name="edit_teacher">Save</button>
		                </div>
		            </div>
		        </form>
		        <script>
		            const form = document.getElementById("edit-teacher");
		            const inputs = form.querySelectorAll("input[type='text'], input[type='email'], input[type='number']");
		            for(let i = 0; i < inputs.length; i++){
		                inputs[i].setAttribute("autocomplete", "off");
		            }
		            form.onsubmit = function (e) {

		                // validate inputs
		                let name      = form.querySelector("input#name"),
		                    subject   = form.querySelector("select#subject"),
		                    phone     = form.querySelector("input#phone"),
		                    email     = form.querySelector("input#email"),
		                    gender    = form.querySelector("select#gender"),
		                    password  = form.querySelector("input#password");

		                if(name.value.match(/^([a-zA-Z_ ]+)$/g) === null){
		                    e.preventDefault();
		                    name.focus();
		                    name.classList.add("input-alert");                            
		                    form.querySelector("small.name").textContent = 'Enter Teacher\'s Valid Name';
		                }else{
		                    name.classList.remove("input-alert");                            
		                    form.querySelector("small.name").textContent = '';

		                    if(subject.value === "NULL"){
		                        e.preventDefault();
		                        subject.focus();
		                        subject.classList.add("input-alert");
		                        form.querySelector("small.subject").textContent = 'Choose Teacher\'s Subject';
		                    }else{
		                        subject.classList.remove("input-alert");
		                        form.querySelector("small.subject").textContent = '';
		                        if(phone.value.match(/^([0-9]){6,}$/g) === null){
		                            e.preventDefault();
		                            phone.focus();
		                            phone.classList.add("input-alert");
		                            form.querySelector("small.phone").textContent = 'Please Enter Correct Phone Number';
		                        }else{
		                            phone.classList.remove("input-alert");
		                            form.querySelector("small.phone").textContent = '';
		                            if(email.value.match(/^\S+@\S+\.\S+$/g) === null){
		                                e.preventDefault();
		                                email.focus();
		                                email.classList.add("input-alert");
		                                form.querySelector("small.email").textContent = 'Please Enter Correct Email';
		                            }else{
		                                email.classList.remove("input-alert");
		                                form.querySelector("small.email").textContent = '';
		                                if(gender.value === "NULL"){
		                                    e.preventDefault();
		                                    gender.focus();
		                                    gender.classList.add("input-alert");
		                                    form.querySelector("small.gender").textContent = 'Choose Teacher\'s Gender';
		                                }else{
		                                    gender.classList.remove("input-alert");  
		                                    form.querySelector("small.gender").textContent = '';

		                                    if(password.value !== ""){
		                                        if(password.value.match(/^(.){8,}$/g) === null){
		                                            e.preventDefault();
		                                            password.focus();
		                                            password.classList.add("input-alert");
		                                            form.querySelector("small.password").textContent = 'Password Must Contain 8 charachters At Least';
		                                        }else{
		                                            password.classList.remove("input-alert");
		                                            form.querySelector("small.password").textContent = '';
		                                        }
		                                    }
		                                }
		                            }
		                        }
		                    }
		                }

		            }
		        </script>
				<?php
			}else{
				header('location:user.php?page=logout');
			}
		}else if($_SESSION['smart_school_position'] === 0){ // student

                if($_SERVER['REQUEST_METHOD'] === 'POST'){
                    if(array_key_exists("edit-student", $_POST)){
                        updateStudent();
                    }
                }

			$student = getStudent($_SESSION['smart_school_user']);
			if($student !== 0){
				?>
				<form id="edit-form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                   <input type="hidden" name="id" id="id" value="<?php echo $_SESSION['smart_school_user'];?>">
                    <div class="row">
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <label for="name">Name</label>
                            <input autofocus type="text" name="name" placeholder="Student Name" id="name" class="form-control" value="<?php echo $student['name'];?>">
                            <small class="err-msg name"></small>
                        </div>
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <label for="email">Email</label>
                            <input type="email" name="email" placeholder="Student Email" id="email" class="form-control" value="<?php echo $student['email'];?>">
                            <small class="err-msg email"></small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <label for="student_id">Student Id</label>
                            <input type="number" name="student_id" id="student_id" placeholder="Student Id" class="form-control" value="<?php echo $student['student_id'];?>"disabled>
                            <small class="err-msg id"></small>
                        </div>
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <label for="birth_date">Birth Date</label>
                            <input type="date" name="birth_date" id="birth_date" placeholder="Student Birth" class="form-control" value="<?php echo $student['birth_date'];?>">
                            <small class="err-msg date"></small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="grade">Grade</label>
                            <select name="grade" id="grade" class="form-control">
                                <option value="NULL" disabled>Choose One</option>
                                <option value="7" <?php if($student['grade'] == 7){echo 'selected';}?>>Grade 7</option>
                                <option value="8" <?php if($student['grade'] == 8){echo 'selected';}?>>Grade 8</option>
                                <option value="9" <?php if($student['grade'] == 9){echo 'selected';}?>>Grade 9</option>
                            </select>
                            <small class="err-msg grade"></small>
                        </div>
                    </div>
                    <div class="gap-2 d-grid mt-3">
                        <button class="btn btn-success" name="edit-student">Save</button>
                    </div>
                </form>
	            <script>
	                const form = document.getElementById("edit-form");
	                form.onsubmit = function (e) {
	                    let name  = form.querySelector("input#name"),
	                        id    = form.querySelector("input#student_id"),
	                        email = form.querySelector("input#email"),
	                        date  = form.querySelector("input#birth_date"),
	                        grade    = form.querySelector("select#grade");

	                    if(name.value.match(/^[a-zA-Z _]{5,}$/g) === null){
	                        e.preventDefault();
	                        name.classList.add("input-alert");
	                        name.focus();
	                        form.querySelector("small.err-msg.name").textContent = 'name must contain 5 charachters at least';
	                    }else{
	                        name.classList.remove("input-alert");
	                        form.querySelector("small.err-msg.name").textContent = '';

	                        if(id.value.match(/^[0-9]+$/g) === null){
	                            e.preventDefault();
	                            id.classList.add("input-alert");
	                            id.focus();
	                            form.querySelector("small.err-msg.id").textContent = 'please write numeric id';
	                        }else{
	                            id.classList.remove("input-alert");
	                            form.querySelector("small.err-msg.id").textContent = '';

	                            if(email.value.match(/^\S+@\S+\.\S+$/g) === null){
	                                e.preventDefault();
	                                email.classList.add("input-alert");
	                                email.focus();
	                                form.querySelector("small.err-msg.email").textContent = 'please write a valid email';
	                            }else{
	                                email.classList.remove("input-alert");
	                                form.querySelector("small.err-msg.email").textContent = '';

	                                if(date === ""){
	                                    e.preventDefault();
	                                    date.classList.add("input-alert");
	                                    date.focus();
	                                    form.querySelector("small.err-msg.date").textContent = 'please choose a date';
	                                }else{
	                                    date.classList.remove("input-alert");
	                                    form.querySelector("small.err-msg.date").textContent = '';

	                                    if(grade.value === "NULL"){
	                                        e.preventDefault();
	                                        grade.classList.add("input-alert");
	                                        grade.focus();
	                                        form.querySelector("small.err-msg.grade").textContent = 'Choose Student Grade';
	                                    }else{
	                                        grade.classList.remove("input-alert");
	                                        form.querySelector("small.err-msg.grade").textContent = '';
	                                    }
	                                }

	                            }

	                        }

	                    }

	                }
	            </script>
				<?php
			}else{
				header('location:user.php?page=logout');
			}
		}
		?>
	</div>
</div>

<?php include 'templates/_footer.php';?>
