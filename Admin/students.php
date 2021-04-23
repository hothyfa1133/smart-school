<?php
$pageName = 'students';
require 'includes/init.php';

$allowed_links = ['students', 'add_student'];
if(!in_array($_GET['page'], $allowed_links)){header("location:" . $_SERVER['PHP_SELF'] . '?page=students');}

// get students function
function getStudents ($order = 'DESC', $limit = 10) {
    global $conn;
    
    // getting students
    $getStudents = $conn->prepare("SELECT
    id, name, email, student_id, grade
    FROM
    students
    ORDER BY date $order
    LIMIT $limit");
    $getStudents->execute();
    if($getStudents->rowCount() > 0){
        return $getStudents->fetchAll();
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

// add stuudent function
function addStudent () {
    global $conn;
    
    // check info
    $name           = trim(htmlentities($_POST['name']));
    $email          = trim(htmlentities($_POST['email']));
    $student_id     = trim(htmlentities($_POST['id']));
    $date           = trim(htmlentities($_POST['date']));
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
            echo message($error);
        }
        
    }
    
}
?>

<div class="container mt-3">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-12 mt-3 mt-md-0">
            <div class="rounded border bg-white p-2 page-links">
                <ul class="list-unstyled ps-0 mb-0">
                    <li <?php if($_GET['page'] === 'students'){echo 'class="active"';}?>>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?page=students';?>">All Students</a>
                    </li>
                    <li <?php if($_GET['page'] === 'add_student'){echo 'class="active"';}?>>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?page=add_student';?>">Add Student</a>
                    </li>
                </ul>
            </div>
            <?php
            if($_GET['page'] === 'students'){ // printing page options
                
                // redirect if wrong page
                $allowed_orders = ['DESC', 'ASC'];
                if(!in_array($_GET['order'], $allowed_orders) || !is_numeric($_GET['limit'])){header("location:" . $_SERVER['PHP_SELF'] . '?page=students&limit=10&order=DESC');}

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
            <div class="rounded border bg-white p-3">
                <?php
                if($_GET['page'] === 'students'){ // students page
                    
                    // handling post request
                    if($_SERVER['REQUEST_METHOD'] === 'POST'){
                        if(array_key_exists("edit-student", $_POST)){
                            updateStudent();
                        }else if(array_key_exists("delete_id", $_POST)){
                            if(is_numeric($_POST['delete_id'])){
                                $deleteStudent = $conn->prepare("DELETE
                                FROM
                                students
                                WHERE 
                                id = ?");
                                $deleteStudent->execute([$_POST['delete_id']]);
                            }
                        }
                    }
                    
                    // edit student page
                    if(isset($_GET['edit'])){
                        if(is_numeric($_GET['edit'])){
                            if(getStudent($_GET['edit']) !== 0){ // found
                                $student = getStudent($_GET['edit']);
                                ?>
                                <div class="full-page">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-8 col-12 mx-md-auto mx-0 content">
                                                <div class="rounded bg-white p-3">
                                                    <h6 class="main">Edit Student</h6>
                                                    <form id="edit-form" action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&limit=' . $_GET['limit'] . '&order=' . $_GET['order'];?>" method="post">
                                                       <input type="hidden" name="id" id="id" value="<?php echo $student['id'];?>">
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
                                                                <input type="number" name="student_id" id="student_id" placeholder="Student Id" class="form-control" value="<?php echo $student['student_id'];?>">
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
                                                                <select name="grade" id="grade" class="form-select">
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
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }else{ // not found
                                ?>
                                <div class="alert alert-danger">Student With This Id Is Not Found</div>
                                <?php
                            }
                        }else{
                            ?>
                            <div class="alert alert-danger">Student With This Id Is Not Found</div>
                            <?php
                        }
                    }
                    
                    ?>
                    
                    <h6 class="main">All Students</h6>
                    <table class="table table-striped text-center mb-0">
                        <thead>
                            <tr>
                                <td>#ID</td>
                                <td>Name</td>
                                <td>Email</td>
                                <td>St_ID</td>
                                <td>Grade</td>
                                <td>Options</td>
                            </tr>
                        </thead>
                        <tbody>
                           <?php
                            $students = getStudents($_GET['order'], $_GET['limit']);
                            if($students !== 0){ // not empty result
                                
                                // loop on result
                                foreach($students as $student){
                                    ?>
                                    <tr>
                                        <td><?php echo $student['id'];?></td>
                                        <td><?php echo $student['name'];?></td>
                                        <td><?php echo $student['email'];?></td>
                                        <td><?php echo $student['student_id'];?></td>
                                        <td><?php echo $student['grade'];?></td>
                                        <td>
                                            <a href="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&limit=' . $_GET['limit'] . '&order=' . $_GET['order'] . '&edit=' . $student['id'];?>">
                                                <i class="fas fa-edit text-success me-2" title="Edit"></i>
                                            </a>
                                            <i class="fas fa-trash text-danger delete-btn" data-id="<?php echo $student['id'];?>" title="Delete"></i>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                
                                ?>
                                <script>
                                    const deleteBtns = document.querySelectorAll("i.delete-btn");
                                    for(let i = 0; i < deleteBtns.length; i++){
                                        deleteBtns[i].onclick = function () {

                                            if(confirm("Do You Want To Delete This Student? ")){ // yes

                                                var deleteObj = new XMLHttpRequest();
                                                deleteObj.open("POST", "<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&limit=' . $_GET['limit'] . '&order=' . $_GET['order'];?>");
                                                deleteObj.onload = function () {

                                                    if(this.readyState === 4 && this.status === 200){ // success
                                                         location.reload();
                                                    }else{
                                                        alert("An Error Has Happened");
                                                    }

                                                }
                                                deleteObj.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                                deleteObj.send("delete_id=" + this.dataset.id);

                                            }

                                        }
                                    }
                                </script>
                                <?php
                                
                            } else { // empty result
                                ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="alert alert-info mb-0">No Students Yet</div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    
                    <?php
                }else if($_GET['page'] === 'add_student'){ // add student page
                    if($_SERVER['REQUEST_METHOD'] === 'POST'){
                        addStudent();
                    }
                    ?>
                    <h6 class="main">Add Student</h6>
                    <form action="<?php echo $_SERVER['PHP_SELF'];?>?page=add_student" method="post" id="add-student">
                        <div class="row">
                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                <label for="name">Name</label>
                                <input autofocus type="text" name="name" placeholder="Student Name" id="name" class="form-control">
                                <small class="err-msg name"></small>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="email">Email</label>
                                <input type="email" name="email" placeholder="Student Email" id="email" class="form-control">
                                <small class="err-msg email"></small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                <label for="id">Id</label>
                                <input type="number" name="id" placeholder="Student Id" id="id" class="form-control">
                                <small class="err-msg id"></small>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="date">Birth Date</label>
                                <input type="date" name="date" id="date" class="form-control">
                                <small class="err-msg date"></small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6 col-12">
                                <label for="password">Password</label>
                                <input type="password" name="password" placeholder="Student Password" id="password" class="form-control" autocomplete="new-password">
                                <small class="err-msg password"></small>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="grade">Grade</label>
                                <select name="grade" id="grade" class="form-select">
                                    <option value="NULL" disabled selected>Choose One</option>
                                    <option value="7">Grade 7</option>
                                    <option value="8">Grade 8</option>
                                    <option value="9">Grade 9</option>
                                </select>
                                <small class="err-msg grade"></small>
                            </div>
                        </div>
                        <div class="gap-2 d-grid mt-3">
                            <button class="btn btn-success">Add</button>
                        </div>
                    </form>
                    <script>
                        const form = document.getElementById("add-student");
                        form.onsubmit = function (e) {
                            let name     = form.querySelector("input#name"),
                                id       = form.querySelector("input#id"),
                                email    = form.querySelector("input#email"),
                                date     = form.querySelector("input#date"),
                                password = form.querySelector("input#password"),
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
                                            
                                            if(password.value.match(/^(.){8,}$/g) === null){
                                                e.preventDefault();
                                                password.classList.add("input-alert");
                                                password.focus();
                                                form.querySelector("small.err-msg.password").textContent = 'password must contain 8 charachters';
                                            }else{
                                                password.classList.remove("input-alert");
                                                form.querySelector("small.err-msg.password").textContent = '';

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

                        }
                    </script>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $("input[type='text'], input[type='number'], textarea, input[type='email']").attr("autocomplete", "off");
        $("input[type='password']").attr("autocomplete", "new-password");
    })
</script>

<?php include 'templates/_footer.php';?>