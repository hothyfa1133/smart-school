<?php
$forAdmin = true;
$pageName = 'courses';
require 'includes/init.php';

// redirect if wrong page
$allowed_links = ['courses', 'add_course'];
if(!in_array($_GET['page'], $allowed_links)){header("location:" . $_SERVER['PHP_SELF'] . '?page=courses');}

// get courses function
function getCourses ($limit = 10, $order = 'DESC') {
    global $conn;

    // get courses
    $getCourses = $conn->prepare("SELECT
        id, title, description, teacher, grade, date
        FROM
        courses
        ORDER BY id $order
        LIMIT $limit");
    $getCourses->execute();
    if($getCourses->rowCount() > 0){
        return $getCourses->fetchAll();
    }else{
        return 0;
    }
}

// get teachers names
function getTeacherName ($id) {
    global $conn;

    // get teacher name
    $getName = $conn->prepare("SELECT
        name, subject
        FROM
        teachers
        WHERE
        id = ?");
    $getName->execute([$id]);
    if($getName->rowCount() > 0){
        return $getName->fetch();
    }else{
        return 'UNKNOWN';
    }
}

// get subject name function
function getSubName ($subject) {
    global $conn;

    // get subject
    $getSub = $conn->prepare("SELECT
        subject
        FROM
        subjects
        WHERE
        id = ?");
    $getSub->execute([$subject]);
    if($getSub->rowCount() > 0){
        return $getSub->fetchColumn();
    }else{
        return 'UNKNOWN';
    }
}

// get teachers function
function getTeachers () {
    global $conn;

    // get teachers
    $getTeachers = $conn->prepare("SELECT
        name, id
        FROM
        teachers
        ORDER BY name ASC");
    $getTeachers->execute();
    if($getTeachers->rowCount() > 0){
        return $getTeachers->fetchAll();
    }else{
        return 0;
    }
}

// add course function
function addCourse () {
    global $conn;

    // validate inputs
    $title    = trim(htmlentities($_POST['title']));
    $description  = trim(htmlentities($_POST['description']));
    $image    = $_FILES['image'];
    $errors   = [];

    // validating
    if(!isset($_POST['grade']) || !is_numeric($_POST['grade'])){$errors[] = 'Choose The Grade Of The Course';}
    if(!isset($_POST['teacher']) || !is_numeric($_POST['teacher'])){$errors[] = 'Choose The Teacher Of The Course';}
    if(!preg_match("/^(.){4,}$/", $title)){$errors[] = 'Title Of The Course Must Contain 4 Charachters At Least';}
    if(!preg_match("/(.){8,}/", $description)){$errors[] = 'Description Of The Course Must Contain 8 Charachters At Least';}

    // check on image
    if(empty($image['name'])){ // empty
        $errors[] = 'You Must Choose Image For The Course';
    }else{ // not empty, start validating

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
        
        $grade = trim(htmlentities($_POST['grade']));
        $teacher = trim(htmlentities($_POST['teacher']));

        // upload image
        $image_name = rand(1000, 80000) . '_' . $image['name'];
        try{
            move_uploaded_file($image['tmp_name'], '../images/courses/' . $image_name);
        }
        catch(Exception $e){
            echo message("Error During Moving Image");
        }

        // insert info
        try{
            $insertStmt = $conn->prepare("INSERT
                INTO
                courses
                (grade, teacher, image, title, description, date)
                VALUES
                (?, ?, ?, ?, ?, NOW())");
            $insertStmt->execute([$grade, $teacher, $image_name, $title, $description]);
            if($insertStmt->rowCount() > 0){
                echo message("Course Has Added Succesfully", true);
            }else{
                echo message("Course Has Not Added Succesfully");
            }
        }
        catch(PDOException $e){
            echo message("Error While Adding The Course, It Might Be Dublicated");
        }

    }else{ // check false

        // loop on errors
        foreach ($errors as $error) {
            echo message($error);
        }

    }
}

// get course function 
function getCourse ($id) {
    global $conn;

    // get course
    $getCourse = $conn->prepare("SELECT
        grade, teacher, title, description, image
        FROM
        courses
        WHERE
        id = ?");
    $getCourse->execute([$id]);
    if($getCourse->rowCount() > 0){
        return $getCourse->fetch();
    }else{
        return 0;
    }
}

// update course info function
function updateCourse () {
    global $conn;

    // validate inputs
    $title    = trim(htmlentities($_POST['title']));
    $description  = trim(htmlentities($_POST['description']));
    $image    = $_FILES['image'];
    $errors   = [];

    // validating
    if(!isset($_POST['grade'])){$errors[] = 'Choose The Grade Of The Course';}
    if(!isset($_POST['teacher'])){$errors[] = 'Choose The Teacher Of The Course';}
    if(!preg_match("/^(.){4,}$/", $title)){$errors[] = 'Title Of The Course Must Contain 4 Charachters At Least';}
    if(!preg_match("/(.){8,}/", $description)){$errors[] = 'Description Of The Course Must Contain 8 Charachters At Least';}

    // check on image
    if(!empty($image['name'])){ // empty

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

        $grade = trim(htmlentities($_POST['grade']));
        $teacher = trim(htmlentities($_POST['teacher']));

        // upload image
        try{
            if($image['name'] === ""){ // empty
                $image_name = $_POST['old_image'];
            }else{
                
                if(file_exists("../images/courses/" . $_POST['old_image'])){
                    @unlink("../images/courses/" . $_POST['old_image']);
                }
                
                $image_name = rand(1000, 80000) . '_' . $image['name'];
                move_uploaded_file($image['tmp_name'], "../images/courses/" . $image_name);
                
            }
        }
        catch(Exception $e){
            echo message("Error During Uploading Image");
        }
        
        try{
            
            $updateStmt = $conn->prepare("UPDATE
                courses
                SET
                grade = ?,
                teacher = ?,
                title = ?,
                description = ?,
                image = ?
                WHERE id = ?");
            $updateStmt->execute([
                $grade,
                $teacher,
                $title,
                $description,
                $image_name,
                $_POST['id']
            ]);
            if($updateStmt->rowCount() > 0){
                echo message("Course Info Has Updated Succesfully", true);
            }else{
                echo message("Course Info Has Not Updated Succesfully");
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
            <div class="bg-white rounded border p-2 page-links">
                <ul class="list-unstyled pe-0 mb-0">
                    <li <?php if($_GET['page'] === 'courses'){echo 'class="active"';}?>>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?page=courses';?>">All Courses</a>
                    </li>
                    <li <?php if($_GET['page'] === 'add_course'){echo 'class="active"';}?>>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?page=add_course';?>">Add Course</a>
                    </li>
                </ul>
            </div>
            <?php
            if($_GET['page'] === 'courses'){ // courses page

                // redirect if wrong page
                $allowed_orders = ['DESC', 'ASC'];
                if(!is_numeric($_GET['limit']) || !in_array($_GET['order'], $allowed_orders)){
                    header("location:" . $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&limit=10&order=DESC');
                }

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
                if($_GET['page'] === 'courses'){ // courses page

                    // handling post requests
                    if($_SERVER['REQUEST_METHOD'] === 'POST'){
                        if(array_key_exists("edit_course", $_POST)){ // update course request
                            updateCourse();
                        }else if(array_key_exists("delete_id", $_POST)){

                            // get image and delete it
                            $getImage = $conn->prepare("SELECT
                                image
                                FROM
                                courses
                                WHERE
                                id = ?");
                            $getImage->execute([$_POST['delete_id']]);
                            $d_image = $getImage->fetchColumn();
                            if(file_exists("../images/courses/" . $d_image)){
                                @unlink("../images/courses/" . $d_image);
                            }

                            $deleteStmt = $conn->prepare("DELETE
                            FROM
                            courses
                            WHERE
                            id = ?");
                            $deleteStmt->execute([$_POST['delete_id']]);

                        }
                    }

                    // check if is set edit course in get request
                    if(isset($_GET['edit'])){
                        if(is_numeric($_GET['edit'])){
                            $course = getCourse($_GET['edit']);
                            if($course !== 0){ // found
                                ?>
                                <div class="full-page">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-8 col-12 mx-md-auto content">
                                                <div class="bg-white rounded border p-3">
                                                    <h6 class="main">Edit Course</h6>
                                                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&limit=' . $_GET['limit'] . '&order=' . $_GET['order'];?>" method="post" id="edit-course" enctype="multipart/form-data">
                                                        <input type="hidden" name="id" value="<?php echo $_GET['edit'];?>">
                                                        <input type="hidden" name="old_image" value="<?php echo $course['image'];?>">
                                                        <div class="row">
                                                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                                                <label for="grade">Grade</label>
                                                                <select name="grade" id="grade" class="form-select">
                                                                    <option value="NULL" disabled>Choose One</option>
                                                                    <option value="7" <?php if($course['grade'] == '7'){echo 'selected';}?>>Grade 7</option>
                                                                    <option value="8" <?php if($course['grade'] == '8'){echo 'selected';}?>>Grade 8</option>
                                                                    <option value="9" <?php if($course['grade'] == '9'){echo 'selected';}?>>Grade 9</option>
                                                                </select>
                                                                <small class="err-msg grade"></small>
                                                            </div>
                                                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                                                <label for="teacher">Teacher</label>
                                                                <select name="teacher" id="teacher" class="form-select">
                                                                    <option value="NULL" disabled>Choose One</option>
                                                                    <?php
                                                                    $teachers = getTeachers();
                                                                    if($teachers !== 0){ // not empty result
                                                                    
                                                                    // loop in result
                                                                    foreach ($teachers as $teacher) {
                                                                        ?>
                                                                        <option <?php if($teacher['id'] == $course['teacher']){echo 'selected';}?> value="<?php echo $teacher['id'];?>"><?php echo $teacher['name'];?></option>
                                                                        <?php
                                                                    }

                                                                    }
                                                                    ?>
                                                                </select>
                                                                <small class="err-msg teacher"></small>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-12">
                                                                <label for="title">Title</label>
                                                                <input type="text" name="title" placeholder="Title Of The Course" id="title" class="form-control" autocomplete="off" value="<?php echo $course['title'];?>">
                                                                <small class="err-msg title"></small>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-12">
                                                                <label for="description">Description</label>
                                                                <textarea name="description" id="description" cols="30" rows="5" class="form-control" placeholder="Description Of The Course"><?php echo $course['description'];?></textarea>
                                                                <small class="err-msg description"></small>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-12">
                                                                <label for="image">Course Image</label>
                                                                <input class="form-control" type="file" id="image" name="image">
                                                                <small class="err-msg image"></small>
                                                            </div>
                                                        </div>
                                                        <div class="gap-2 d-grid mt-3">
                                                            <button class="btn btn-success" name="edit_course">Add</button>
                                                        </div>
                                                    </form>
                                                    <script>
                                                        const form = document.getElementById("edit-course");
                                                        form.onsubmit = function (e) {

                                                            // validate inputs
                                                            let grade    = form.querySelector("select#grade"),
                                                                teacher   = form.querySelector("select#teacher"),
                                                                title    = form.querySelector("input#title"),
                                                                description  = form.querySelector("textarea#description");

                                                            if(grade.value === "NULL"){
                                                                e.preventDefault();
                                                                grade.focus();
                                                                grade.classList.add("input-alert");
                                                                form.querySelector("small.grade").textContent = 'Choose The Grade Of The Course';
                                                            }else{
                                                                grade.classList.remove("input-alert");
                                                                form.querySelector("small.grade").textContent = '';

                                                                if(teacher.value === "NULL"){
                                                                    e.preventDefault();
                                                                    teacher.focus();
                                                                    teacher.classList.add("input-alert");
                                                                    form.querySelector("small.teacher").textContent = 'Choose The Teacher Of The Course';
                                                                }else{
                                                                    teacher.classList.remove("input-alert");
                                                                    form.querySelector("small.teacher").textContent = '';

                                                                    if(title.value.match(/^(.){4,}$/g) === null){
                                                                        e.preventDefault();
                                                                        title.focus();
                                                                        title.classList.add("input-alert");
                                                                        form.querySelector("small.title").textContent = 'Title Of The Course Must Contain 4 Charachters At Least';
                                                                    }else{
                                                                        title.classList.remove("input-alert");
                                                                        form.querySelector("small.title").textContent = '';

                                                                        if(description.value.match(/(.){8,}/g) === null){
                                                                            e.preventDefault();
                                                                            description.focus();
                                                                            description.classList.add("input-alert");
                                                                            form.querySelector("small.description").textContent = 'Description Of The Course Must Contain 8 Charachters At Least';
                                                                        }else{
                                                                            description.classList.remove("input-alert");
                                                                            form.querySelector("small.description").textContent = '';
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
                                <div class="alert alert-danger">Check Your Link And Try Again, Course Is Not Found</div>
                                <?php
                            }

                        }else{
                            ?>
                            <div class="alert alert-danger">Check Your Link And Try Again, Error In Id</div>
                            <?php
                        }
                    }

                    ?>
                    <h6 class="main">All Courses</h6>
                    <table class="table table-striped text-center mb-0">
                        <thead>
                            <tr>
                                <td>#ID</td>
                                <td>Teacher</td>
                                <td>Title</td>
                                <td>Grade</td>
                                <td>Date</td>
                                <td>Options</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $courses = getCourses($_GET['limit'], $_GET['order']);
                            if($courses !== 0){ // not empty result

                                // loop on result
                                foreach ($courses as $course) {
                                    ?>
                                    <tr>
                                        <td><?php echo $course['id'];?></td>
                                        <td class="i-con">
                                            <?php echo getTeacherName($course['teacher'])['name'];?>
                                            <div class="td-content position-absolute bg-white rounded border py-2 ps-2 pe-5">
                                                <?php echo 'Teacher Of: ' . getSubName(getTeacherName($course['teacher'])['subject']);?>
                                            </div>
                                        </td>
                                        <td class="i-con">
                                            <?php echo $course['title'];?>
                                            <div class="td-content position-absolute bg-white rounded border py-2 ps-2 pe-5">
                                                <?php echo nl2br($course['description']);?>
                                            </div>
                                        </td>
                                        <td><?php echo $course['grade'];?></td>
                                        <td><?php echo $course['date'];?></td>
                                        <td>
                                            <a href="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&limit=' . $_GET['limit'] . '&order=' . $_GET['order'] . '&edit=' . $course['id'];?>">
                                                <i class="fas fa-edit text-success me-1" title="Edit"></i>
                                            </a>
                                            <i class="fas fa-trash text-danger delete-course" data-id="<?php echo $course['id'];?>" title="Delete"></i>
                                        </td>
                                    </tr>
                                    <?php
                                }

                                ?>
                                <script>
                                    const btns = document.querySelectorAll("i.delete-course");

                                    for (let i = 0; i < btns.length; i++) {
                                        btns[i].onclick = function () {
                                            var deleteObj = new XMLHttpRequest();
                                            deleteObj.open("POST", "<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&limit=' . $_GET['limit'] . '&order=' . $_GET['order'];?>");
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
                                <?php

                            }else{ // empty result
                                ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="alert alert-info mb-0">There Is No Courses Yet</div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php

                }else if($_GET['page'] === 'add_course'){ // add course page

                    // handling post requests
                    if($_SERVER['REQUEST_METHOD'] === 'POST'){
                        if(array_key_exists("add_course", $_POST)){ // add course request
                            addCourse();
                        }
                    }

                    ?>
                    <h6 class="main">Add Course</h6>
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'];?>" method="post" id="add-course" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                <label for="grade">Grade</label>
                                <select name="grade" id="grade" class="form-select">
                                    <option value="NULL" disabled selected>Choose One</option>
                                    <option value="7">Grade 7</option>
                                    <option value="8">Grade 8</option>
                                    <option value="9">Grade 9</option>
                                </select>
                                <small class="err-msg grade"></small>
                            </div>
                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                <label for="teacher">Teacher</label>
                                <select name="teacher" id="teacher" class="form-select">
                                    <option value="NULL" disabled selected>Choose One</option>
                                    <?php
                                    $teachers = getTeachers();
                                    if($teachers !== 0){ // not empty result
                                    
                                    // loop in result
                                    foreach ($teachers as $teacher) {
                                        ?>
                                        <option value="<?php echo $teacher['id'];?>"><?php echo $teacher['name'];?></option>
                                        <?php
                                    }

                                    }
                                    ?>
                                </select>
                                <small class="err-msg teacher"></small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <label for="title">Title</label>
                                <input type="text" name="title" placeholder="Title Of The Course" id="title" class="form-control" autocomplete="off">
                                <small class="err-msg title"></small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" cols="30" rows="5" class="form-control" placeholder="Description Of The Course"></textarea>
                                <small class="err-msg description"></small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <label for="image">Course Image</label>
                                <input class="form-control" type="file" id="image" name="image">
                                <small class="err-msg image"></small>
                            </div>
                        </div>
                        <div class="gap-2 d-grid mt-3">
                            <button class="btn btn-success" name="add_course">Add</button>
                        </div>
                    </form>
                    <script>
                    const form = document.getElementById("add-course");
                    form.onsubmit = function (e) {

                        // validate inputs
                        let grade    = form.querySelector("select#grade"),
                            teacher   = form.querySelector("select#teacher"),
                            title    = form.querySelector("input#title"),
                            description  = form.querySelector("textarea#description"),
                            image    = form.querySelector("input#image");

                        if(grade.value === "NULL"){
                            e.preventDefault();
                            grade.focus();
                            grade.classList.add("input-alert");
                            form.querySelector("small.grade").textContent = 'Choose The Grade Of The Course';
                        }else{
                            grade.classList.remove("input-alert");
                            form.querySelector("small.grade").textContent = '';

                            if(teacher.value === "NULL"){
                                e.preventDefault();
                                teacher.focus();
                                teacher.classList.add("input-alert");
                                form.querySelector("small.teacher").textContent = 'Choose The Teacher Of The Course';
                            }else{
                                teacher.classList.remove("input-alert");
                                form.querySelector("small.teacher").textContent = '';

                                if(title.value.match(/^(.){4,}$/g) === null){
                                    e.preventDefault();
                                    title.focus();
                                    title.classList.add("input-alert");
                                    form.querySelector("small.title").textContent = 'Title Of The Course Must Contain 4 Charachters At Least';
                                }else{
                                    title.classList.remove("input-alert");
                                    form.querySelector("small.title").textContent = '';

                                    if(description.value.match(/(.){8,}/g) === null){
                                        e.preventDefault();
                                        description.focus();
                                        description.classList.add("input-alert");
                                        form.querySelector("small.description").textContent = 'Description Of The Course Must Contain 8 Charachters At Least';
                                    }else{
                                        description.classList.remove("input-alert");
                                        form.querySelector("small.description").textContent = '';

                                        if(image.value === ""){
                                            e.preventDefault();
                                            image.focus();
                                            image.classList.add("input-alert");
                                            form.querySelector("small.image").textContent = 'You Must Choose Image For The Course';
                                        }else{
                                            image.classList.remove("input-alert");
                                            form.querySelector("small.image").textContent = '';
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

<?php include 'templates/_footer.php';?>