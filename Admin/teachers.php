<?php
$pageName = 'teachers';
$forAdmin = true;
require 'includes/init.php';

// redirect if wrong page
$allowed_links = ['teachers', 'add_teacher', 'teacher', 'admins'];
if(!in_array($_GET['page'], $allowed_links)){header("location:" . $_SERVER['PHP_SELF'] . '?page=teachers');}

// get teachers function
function getTeachers ($limit = 10, $order = 'DESC') {
    global $conn;
    
    // get teachers
    $getTeachers = $conn->prepare("SELECT
    id, name, subject, phone, email
    FROM
    teachers
    WHERE visibility = 1
    ORDER BY id $order
    LIMIT $limit");
    $getTeachers->execute();
    if($getTeachers->rowCount() > 0){
        return $getTeachers->fetchAll();
    }else{
        return 0;
    }
}

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

// get subjects name
function subName ($subject){
    global $conn;
    
    if(is_numeric($subject)){
        
        // get subject
        $getSubject = $conn->prepare("SELECT
        subject
        FROM
        subjects
        WHERE
        id = ?");
        $getSubject->execute([$subject]);
        if($getSubject->rowCount() > 0){
            return $getSubject->fetchColumn();
        }else{
            return 'UNKNOWN';
        }
        
    }else{
        return 'UNKNOWN';
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

// add teacher function
function addTeacher () {
    global $conn;
    
    // validate inputs
    $name       = trim(htmlentities($_POST['name']));
    $phone      = trim(htmlentities($_POST['phone']));
    $email      = trim(htmlentities($_POST['email']));
    $password   = trim(htmlentities($_POST['password']));
    $fb         = trim(htmlentities($_POST['fb']));
    $image      = $_FILES['image'];
    $errors     = [];
    
    if(!preg_match("/^([a-zA-Z_ ]+)$/", $name)){$errors[] = 'Enter Teacher Valid Name';}
    if(!isset($_POST['subject'])){$errors[] = 'Choose Teacher\'s Subject';}
    if(!preg_match("/^([0-9]){6,}$/", $phone)){$errors[] = 'Please Enter Correct Phone Number';}
    if(!preg_match("/^\S+@\S+\.\S+$/", $email)){$errors[] = 'Please Enter Correct Email';}
    if(!isset($_POST['gender'])){$errors[] = 'Choose Teacher\'s Gender';}
    if(!preg_match("/^(.){8,}$/", $password)){$errors[] = 'Password Must Contain 8 Charachters At Least';}
    
    // check image
    if($image['name'] !== ""){ // has uploaded image
        
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
    
    // check errors
    if(empty($errors)){ // check true
        
        $subject = trim(htmlentities($_POST['subject']));
        $gender = trim(htmlentities($_POST['gender']));

        if($image['name'] !== ""){
            // upload image
            $image_name = rand(1000, 80000) . '_' . $image['name'];
            try{
                move_uploaded_file($image['tmp_name'], '../images/teachers/' . $image_name);
            }
            catch(Exception $e){
                echo message("Error During Moving Image");
            }
        }else{
            $image_name = '';
        }

        try{
            
            $insertStmt = $conn->prepare("INSERT
            INTO
            teachers
            (name, image, subject, phone, email, password, gender, fb_link, date)
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $insertStmt->execute([
                $name,
                $image_name,
                $subject,
                $phone,
                $email,
                sha1($password),
                $gender,
                $fb
            ]);
            if($insertStmt->rowCount() > 0){
                echo message("Teacher Has Added Succesfully", true);
            }else{
                echo message("Teacher Has Not Added Succesfully");
            }
        }
        catch(PDOException $e){
            echo message("Unexpected Error Has Happened");
        }
        
    }else{ // check false
        // loop in errors
        foreach($errors as $error){
            echo message($error);
        }
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
                
                if(file_exists("../images/teachers/" . $_POST['old_image'])){
                    unlink("../images/teachers/" . $_POST['old_image']);
                }
                
                $t_image = rand(1000, 80000) . '_' . $image['name'];
                move_uploaded_file($image['tmp_name'], "../images/teachers/" . $t_image);
                
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

// add admin function
function addAdmin () {
    global $conn;

    // validate inputs
    $name       = trim(htmlentities($_POST['name']));
    $phone      = trim(htmlentities($_POST['phone']));
    $email      = trim(htmlentities($_POST['email']));
    $password   = trim(htmlentities($_POST['password']));
    $image      = $_FILES['image'];
    $errors     = [];

    if(!preg_match("/^([a-zA-Z_ ]+)$/", $name)){$errors[] = 'Enter Admin\'s Valid Name';}
    if(!preg_match("/^([0-9]){6,}$/", $phone)){$errors[] = 'Please Enter Correct Phone Number';}
    if(!preg_match("/^\S+@\S+\.\S+$/", $email)){$errors[] = 'Please Enter Correct Email';}
    if(!isset($_POST['gender'])){$errors[] = 'Choose Admin\'s Gender';}
    if(!preg_match("/^(.){8,}$/", $password)){$errors[] = 'Password Must Contain 8 Charachters At Least';}

    // check image
    if($image['name'] !== ""){ // has uploaded image
        
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

    // check errors
    if(empty($errors)){ // check true
        
        $gender = trim(htmlentities($_POST['gender']));

        if($image['name'] !== ""){
            // upload image
            $image_name = rand(1000, 80000) . '_' . $image['name'];
            try{
                move_uploaded_file($image['tmp_name'], '../images/teachers/' . $image_name);
            }
            catch(Exception $e){
                echo message("Error During Moving Image");
            }
        }else{
            $image_name = '';
        }

        try{
            
            $insertStmt = $conn->prepare("INSERT
            INTO
            teachers
            (name, phone, email, gender, password, image, position, visibility, subject, date)
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $insertStmt->execute([
                $name,
                $phone,
                $email,
                $gender,
                sha1($password),
                $image_name,
                1,
                0,
                NULL,
            ]);
            if($insertStmt->rowCount() > 0){
                echo message("Admin Has Added Succesfully", true);
            }else{
                echo message("Admin Has Not Added Succesfully");
            }
        }
        catch(PDOException $e){
            echo message("Unexpected Error Has Happened");
        }
        
    }else{ // check false
        // loop in errors
        foreach($errors as $error){
            echo message($error);
        }
    }
}

// get admins function
function getAdmins () {
    global $conn;

    // get admins
    $getAdmins = $conn->prepare("SELECT
        id, name, phone, email, date
        FROM
        teachers
        WHERE
        position = 1
        ORDER BY id DESC");
    $getAdmins->execute();
    return $getAdmins->fetchAll();
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
                
                if(file_exists("../images/teachers/" . $_POST['old_image'])){
                    unlink("../images/teachers/" . $_POST['old_image']);
                }
                
                $t_image = rand(1000, 80000) . '_' . $image['name'];
                move_uploaded_file($image['tmp_name'], "../images/teachers/" . $t_image);
                
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
?>

<div class="container mt-3">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-12 mb-3 mb-md-0">
            <div class="bg-white p-2 rounded border page-links">
                <ul class="list-unstyled pe-0 mb-0">
                    <li <?php if($_GET['page'] === 'teachers' || $_GET['page'] === 'teacher'){echo 'class="active"';}?>>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?page=teachers'?>">Normal Teachers</a>
                    </li>
                    <li <?php if($_GET['page'] === 'admins'){echo 'class="active"';}?>>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?page=admins'?>">Admins</a>
                    </li>
                    <li <?php if($_GET['page'] === 'add_teacher'){echo 'class="active"';}?>>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?page=add_teacher'?>">Add Teacher</a>
                    </li>
                </ul>
            </div>
            <?php
            if($_GET['page'] === 'teachers'){ // printing page options
                
                // redirect if wrong page
                $allowed_orders = ['DESC', 'ASC'];
                if(!in_array($_GET['order'], $allowed_orders) || !is_numeric($_GET['limit'])){header("location:" . $_SERVER['PHP_SELF'] . '?page=teachers&limit=10&order=DESC');}

                ?>
                <div class="rounded border bg-white p-2 mt-3">
                    <form id="search-form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
                       <input type="hidden" name="page" value="<?php echo $_GET['page'];?>">
                        <div class="mb-3">
                            <label for="limit">Limit</label>
                            <input type="number" name="limit" placeholder="Limit" value="<?php echo $_GET['limit'];?>" id="limit" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="limit">Order</label>
                            <select name="order" id="order" class="form-select">
                                <option value="NULL" disabled>Choose One</option>
                                <option <?php if($_GET['order'] === 'DESC'){echo 'selected';}?> value="DESC">DESC</option>
                                <option <?php if($_GET['order'] === 'ASC'){echo 'selected';}?> value="ASC">ASC</option>
                            </select>
                        </div>
                        <div class="gap-2 d-grid">
                            <button class="btn btn-success">Submit</button>
                        </div>
                    </form>
                </div>
                <?php
                
            }
            ?>
        </div>
        <div class="col-lg-9 col-md-8 col-12">
            <div class="bg-white rounded border p-3">
        <?php
        if($_GET['page'] === 'teachers'){ // teachers page

                // redirect if wrong page
                $allowed_orders = ['DESC', 'ASC'];
                if(!is_numeric($_GET['limit']) || !in_array($_GET['order'], $allowed_orders)){
                    header("location:" . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&limit=10&order=DESC');
                }

                ?>
                <h6 class="main">All Teachers</h6>
                <table class="table table-striped mb-0 text-center">
                    <thead>
                        <tr>
                            <td>#ID</td>
                            <td>Name</td>
                            <td>Subject</td>
                            <td>Phone</td>
                            <td>Email</td>
                            <td>Show</td>
                        </tr>
                    </thead>
                    <tbody>
                       <?php
                        $teachers = getTeachers($_GET['limit'], $_GET['order']);
                        if($teachers !== 0){ // not empty result
                            
                            // loop in result
                            foreach($teachers as $teacher){
                                ?>
                                <tr>
                                    <td><?php echo $teacher['id'];?></td>
                                    <td><?php echo $teacher['name'];?></td>
                                    <td><?php echo subName($teacher['subject']);?></td>
                                    <td><?php echo $teacher['phone'];?></td>
                                    <td><?php echo $teacher['email'];?></td>
                                    <td>
                                        <a href="teachers.php?page=teacher&id=<?php echo $teacher['id'];?>"><i class="fas text-success fa-eye"></i></a>
                                    </td>
                                </tr>
                                <?php
                            }
                            
                        }else{ // empty result
                            ?>
                            <tr>
                                <td colspan="6">
                                    <div class="alert alert-info mb-0">There Is No Teachers Yet</div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php

            }else if($_GET['page'] === 'add_teacher'){ // add teachers page
            
                // handling post requests
                if($_SERVER['REQUEST_METHOD'] === 'POST'){
                    if(array_key_exists("add_teacher", $_POST)){ // add teacher request
                        addTeacher();
                    }
                }
            
                ?>
                <h6 class="main">Add Teacher</h6>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>?page=add_teacher" method="post" id="add-teacher" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <label for="name">Name</label>
                            <input type="text" name="name" autofocus placeholder="Teacher's Name" id="name" class="form-control">
                            <small class="err-msg name"></small>
                        </div>
                        <div class="col-md-6 col-12">
                            <label for="subject">Subject</label>
                            <select name="subject" id="subject" class="form-select">
                                <option value="NULL" disabled selected>Choose Subject</option>
                                <?php
                                if(getSubjects() != 0){ // not empty result
                                
                                    // loop on result
                                    foreach(getSubjects() as $subject){
                                        ?>
                                        <option value="<?php echo $subject['id'];?>"><?php echo $subject['subject'];?></option>
                                        <?php
                                    }
                                    
                                }
                                ?>
                            </select>
                            <small class="err-msg subject"></small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <label for="phone">Phone</label>
                            <input type="number" name="phone" autofocus placeholder="Teacher's Phone" id="phone" class="form-control">
                            <small class="err-msg phone"></small>
                        </div>
                        <div class="col-md-6 col-12">
                            <label for="email">Email</label>
                            <input type="email" name="email" placeholder="Teacher's Email" id="email" class="form-control">
                            <small class="err-msg email"></small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <label for="gender">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="NULL" disabled selected>Choose Gender</option>
                                <option value="0">Male</option>
                                <option value="1">Female</option>
                            </select>
                            <small class="err-msg gender"></small>
                        </div>
                        <div class="col-md-6 col-12">
                            <label for="password">Password</label>
                            <input type="password" name="password" placeholder="Teacher's Password" id="password" class="form-control" autocomplete="new-password">
                            <small class="err-msg password"></small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="fb">Facebook Link</label>
                            <input type="url" name="fb" id="fb" class="form-control" placeholder="Teacher's Facebook Link" pattern="https://.*">
                            <small class="err-msg fb"></small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="image">Teacher's Image</label>
                            <input class="form-control" type="file" id="image" name="image">
                        </div>
                    </div>
                    <div class="mt-3 gap-2 d-grid">
                        <button class="btn btn-success" name="add_teacher">Add</button>
                    </div>
                </form>
                <script>
                    const form = document.getElementById("add-teacher");
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
                                            
                                            if(password.value.match(/^(.){8,}$/g) === null){
                                                e.preventDefault();
                                                password.focus();
                                                password.classList.add("input-alert");                            
                                                form.querySelector("small.password").textContent = 'Password Must Contain 8 Charachters At Least';
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
                </script>
                <?php
            }else if($_GET['page'] === 'teacher'){ // show teacher info page
                ?>
                <h6 class="main">Teacher's Info</h6>
                <?php
                if(is_numeric($_GET['id'])){ // true
                    $teacher = getTeacher($_GET['id']);
                    if($teacher !== 0){ // found
                        
                        // handling post requests
                        if($_SERVER['REQUEST_METHOD'] === 'POST'){
                            if(array_key_exists("edit_teacher", $_POST)){ // edit teacher request
                                if($_POST['position'] == 0){ // teacher
                                    updateTeacher();
                                }else if($_POST['position'] == 1){ // admin
                                    updateAdmin();
                                }
                            }else if(array_key_exists("delete_id", $_POST)){

                                // get image and delete it
                                $getImage = $conn->prepare("SELECT
                                    image
                                    FROM
                                    teachers
                                    WHERE
                                    id = ?");
                                $getImage->execute([$_POST['delete_id']]);
                                $d_image = $getImage->fetchColumn();
                                if(file_exists("../images/teachers/" . $d_image)){
                                    unlink("../images/teachers/" . $d_image);
                                }

                                $deleteStmt = $conn->prepare("DELETE
                                FROM
                                teachers
                                WHERE
                                id = ?");
                                $deleteStmt->execute([$_POST['delete_id']]);

                                if($_POST['delete_id'] == $_SESSION['smart_school_id']){
                                    header('location:user.php?page=logout');
                                }

                            }
                        }
                        
                        if(isset($_GET['edit'])){ // edit teacher's info
                            ?>
                            <div class="full-page">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-8 col-12 mx-md-auto mx-0 content">
                                            <div class="bg-white rounded border p-3">
                                                <h6 class="main">Edit Teacher</h6>
                                                <form action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&id=' . $_GET['id'];?>" method="post" id="edit-teacher" enctype="multipart/form-data">
                                                   <input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
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
                                                    <div class="row mt-3">
                                                        <div class="col-md-6 col-12">
                                                            <label for="email">Email</label>
                                                            <input type="email" name="email" placeholder="Teacher's Email" id="email" class="form-control" value="<?php echo $teacher['email'];?>">
                                                            <small class="err-msg email"></small>
                                                        </div>
                                                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                                                            <label for="gender">Gender</label>
                                                            <select class="form-select" id="gender" name="gender">
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
                                                        <div class="row mt-3">
                                                            <div class="col-md-6 col-12 mt-3 mt-md-0">
                                                                <label for="subject">Subject</label>
                                                                <select name="subject" id="subject" class="form-select">
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

                                                    if($_SESSION['smart_school_id'] == $_GET['id']){
                                                        ?>
                                                        <div class="row mt-3">
                                                            <div class="col-12">
                                                                <label for="password">New Password</label>
                                                                <input type="password" name="password" placeholder="Write a New Password" id="password" class="form-control">
                                                                <small class="err-msg password"></small>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <label for="image">Teacher's Image</label>
                                                            <input class="form-control" type="file" id="image" name="image">
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="gap-2 d-grid">
                                                            <button class="btn btn-success" name="edit_teacher">Save</button>
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        
                        ?>
                        <div class="row">
                            <div class="col-md-6 col-12 border-end">
                                <div class="p-2 pb-0">
                                    <ul class="list-unstyled pe-0 mb-0">
                                        <li class="py-2 border-bottom">
                                            <span>Name: </span>
                                            <span><?php echo $teacher['name'];?></span>
                                        </li>
                                        <li class="py-2 border-bottom">
                                            <span>Subject: </span>
                                            <span><?php echo subName($teacher['subject']);?></span>
                                        </li>
                                        <li class="py-2 border-bottom">
                                            <span>Gender: </span>
                                            <span><?php if($teacher['gender'] == 0){echo 'Male';}else{echo 'Female';}?></span>
                                        </li>
                                        <li class="py-2 border-bottom">
                                            <span>Phone: </span>
                                            <span><?php echo $teacher['phone'];?></span>
                                        </li>
                                        <li class="py-2 border-bottom">
                                            <span>Email: </span>
                                            <span><?php echo $teacher['email'];?></span>
                                        </li>
                                        <li class="pt-2">
                                            <span>Join Date: </span>
                                            <span><?php echo $teacher['date'];?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="p-2 pb-0">
                                    <div class="row mt-4">
                                        <div class="col-md-6 col-12">
                                            <div class="overflow-hidden position-relative teacher-image-e-p" style="max-height: 200px;">
                                                <?php
                                                if(empty($teacher['image'])){
                                                    if($teacher['gender'] == 0){ // male
                                                        $image = '../images/team-02.png';
                                                    }else if($teacher['gender'] == 1){ // female
                                                        $image = '../images/team-01.png';
                                                    }
                                                }else{
                                                    $image = '../images/teachers/' . $teacher['image'];
                                                }
                                                ?>
                                                <img src="<?php echo $image?>" style="width: 100%; height: 100%;">
                                                <?php
                                                if(!empty($teacher['fb_link'])){
                                                    ?>
                                                    <div class="fb-area">
                                                        <a href="<?php echo $teacher['fb_link'];?>">
                                                            <i class="fab fa-facebook"></i>
                                                        </a>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <?php
                                            if(!empty($teacher['fb_link'])){
                                              ?>
                                              <small class="form-text"><small>Hover Me To Get Facebook</small></small>
                                              <?php  
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-6 col-12 mt-3 mt-md-0">
                                            <a href="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&id=' . $_GET['id'] . '&edit'?>">
                                                <div class="gap-2 d-grid mb-3">
                                                    <button class="btn btn-success">Edit</button>
                                                </div>
                                            </a>
                                            <div class="gap-2 d-grid">
                                                <button class="btn btn-danger delete-teacher" data-id="<?php echo $_GET['id'];?>">Delete</button>
                                            </div>
                                            <script>
                                                document.querySelector("button.delete-teacher").onclick = function () {
                                                    var deleteObj = new XMLHttpRequest();
                                                    deleteObj.open("POST", "<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&id=' . $_GET['id']?>");
                                                    deleteObj.onload = function () {
                                                        if(this.readyState === 4 && this.status === 200){
                                                            location.reload();
                                                        }else{
                                                            alert("Unexpected Error Has Happened");
                                                        }
                                                    }
                                                    deleteObj.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                                    deleteObj.send("delete_id=" + this.dataset.id);
                                                }
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        
                    }else{ // not found
                        ?>
                        <div class="alert alert-danger mb-0">Teacher Is Not Found</div>
                        <?php
                    }
                }else{
                    ?>
                    <div class="alert alert-danger mb-0">Please Check The Link And Try Again</div>
                    <?php
                }
            }else if($_GET['page'] === 'admins'){ // admins page

                // handling post requests
                if($_SERVER['REQUEST_METHOD'] === 'POST'){
                    if(array_key_exists('add_admin', $_POST)){ // add admin request
                        addAdmin();
                    }
                }
                ?>
                <h6 class="main">Add Admin</h6>
                <form action="<?php echo $_SERVER['PHP_SELF'];?>?page=admins" method="post" id="add-admin" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <label for="name">Name</label>
                            <input type="text" name="name" autofocus placeholder="Admin's Name" id="name" class="form-control">
                            <small class="err-msg name"></small>
                        </div>
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <label for="phone">Phone</label>
                            <input type="number" name="phone" autofocus placeholder="Admin's Phone" id="phone" class="form-control">
                            <small class="err-msg phone"></small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 col-12">
                            <label for="email">Email</label>
                            <input type="email" name="email" placeholder="Admin's Email" id="email" class="form-control">
                            <small class="err-msg email"></small>
                        </div>
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <label for="gender">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="NULL" disabled selected>Choose Gender</option>
                                <option value="0">Male</option>
                                <option value="1">Female</option>
                            </select>
                            <small class="err-msg gender"></small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 col-12">
                            <label for="password">Password</label>
                            <input type="password" name="password" placeholder="Admin's Password" id="password" class="form-control" autocomplete="new-password">
                            <small class="err-msg password"></small>
                        </div>
                        <div class="col-md-6 col-12 mb-3 mb-md-0">
                            <label for="image">Admin's Image</label>
                            <input class="form-control" type="file" id="image" name="image">
                        </div>
                    </div>
                    <div class="mt-3 gap-2 d-grid">
                        <button class="btn btn-success" name="add_admin">Add</button>
                    </div>
                </form>
                <script>
                    const form = document.getElementById("add-admin");
                    const inputs = form.querySelectorAll("input[type='text'], input[type='email'], input[type='number']");
                    for(let i = 0; i < inputs.length; i++){
                        inputs[i].setAttribute("autocomplete", "off");
                    }
                    form.onsubmit = function (e) {
                        
                        // validate inputs
                        let name      = form.querySelector("input#name"),
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
                                        
                                        if(password.value.match(/^(.){8,}$/g) === null){
                                            e.preventDefault();
                                            password.focus();
                                            password.classList.add("input-alert");                            
                                            form.querySelector("small.password").textContent = 'Password Must Contain 8 Charachters At Least';
                                        }else{
                                            password.classList.remove("input-alert");                            
                                            form.querySelector("small.password").textContent = '';
                                        }
                                    }
                                }
                            }
                        }
                        
                    }
                </script>
                <hr>
                <h6 class="main">All Admins</h6>
                <table class="table mb-0 text-center table-striped">
                    <thead>
                        <tr>
                            <td>#ID</td>
                            <td>Name</td>
                            <td>Phone</td>
                            <td>Email</td>
                            <td>Date</td>
                            <td>Delete</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // loop on result
                        foreach (getAdmins() as $admin) {
                            ?>
                            <tr>
                                <td><?php echo $admin['id'];?></td>
                                <td><?php echo $admin['name'];?></td>
                                <td><?php echo $admin['phone'];?></td>
                                <td><?php echo $admin['email'];?></td>
                                <td><?php echo $admin['date'];?></td>
                                <td>
                                    <i class="fas fa-trash text-danger delete-admin" data-id="<?php echo $admin['id'];?>"></i>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <script>
                            const btns = document.querySelectorAll('.delete-admin');
                            for (let i = 0; i < btns.length; i++) {
                                btns[i].onclick = function () {
                                    var deleteObj = new XMLHttpRequest();
                                    deleteObj.open("POST", "<?php echo 'teachers.php?page=teacher&id=';?>" + this.dataset.id);
                                    deleteObj.onload = function () {
                                        if(this.readyState === 4 && this.status === 200){
                                            location.reload();
                                        }else{
                                            alert("Unexpected Error Has Happened");
                                        }
                                    }
                                    deleteObj.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                    deleteObj.send("delete_id=" + this.dataset.id);
                                }
                            }
                        </script>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </div>
        </div>
    </div>
</div>

<?php include 'templates/_footer.php';?>