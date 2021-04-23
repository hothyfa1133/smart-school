<?php
$pageName = 'exams';
require 'includes/init.php';

// redirect if wrong page
$allowed_links = ['exams', 'exam', 'add_exam'];
if(!in_array($_GET['page'], $allowed_links)){header('location:' . $_SERVER['PHP_SELF'] . '?page=exams');}

// get exams function
function getExams ($order = 'DESC', $limit = 10) {
    global $conn;

    // get all exams
    $getExams = $conn->prepare("SELECT
    id, name, grade, subject, at
    FROM
    exams
    ORDER BY id $order
    LIMIT $limit");
    $getExams->execute();
    if($getExams->rowCount() > 0){
        return $getExams->fetchAll();
    }else{
        return 0;
    }
}

// get subject function
function getSubject ($subject) {
    global $conn;

    // get subject
    $getSubject = $conn->prepare("SELECT
    subject
    FROM
    subjects
    WHERE
    id = ?");
    $getSubject->execute([$subject]);
    if($getSubject->rowCount() > 0){ // found
        return $getSubject->fetchColumn();
    }else{
        return 'UNKNOWN';
    }
}

// get exam function
function getExam ($id) {
    global $conn;

    // getting exam by id
    $getExam = $conn->prepare("SELECT
    name, grade, subject, teacher, duration, at, date
    FROM
    exams
    WHERE
    id = ?");
    $getExam->execute([$id]);
    if($getExam->rowCount() > 0){
        return $getExam->fetch();
    }else{
        return 0;
    }
}

// get teacher function
function getTeacher ($teacher) {
    global $conn;

    // get teacher
    $getTeacher = $conn->prepare("SELECT
    name
    FROM
    teachers
    WHERE
    id = ?");
    $getTeacher->execute([$teacher]);
    if($getTeacher->rowCount() > 0){ // found
        return $getTeacher->fetchColumn();
    }else{
        return 'UNKNOWN';
    }
}

// get subjects
function getSubjects () {
    global $conn;

    // get get Subjects
    $getSubjects = $conn->prepare("SELECT
    subject, id
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

// get subjects
function getTeachers () {
    global $conn;

    // get get Teachers
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

// add exam function
function addExam () {
    global $conn;

    // filter exam info
    $name       = trim(htmlentities($_POST['name']));
    $duration   = trim(htmlentities($_POST['duration']));
    $at         = trim(htmlentities($_POST['at']));
    $errors     = [];

    if(!preg_match("/^([a-zA-Z_ ]+)$/", $name)){$errors[] = 'Enter Exam\'s Valid Name';}
    if(!preg_match("/^[0-9]{1,3}$/", $duration)){$errors[] = 'Enter Valid Duration';}
    if(empty($at)){$errors[] = 'Enter Starting Of Exam';}
    if(!isset($_POST['grade'])){$errors[] = 'Choose Exam Grade';}
    if(!isset($_POST['subject'])){$errors[] = 'Choose Exam Subject';}
    if(!isset($_POST['teacher'])){$errors[] = 'Choose Exam Teacher';}

    // check on errors
    if(empty($errors)){ // empty
        $grade      = trim(htmlentities($_POST['grade']));
        $subject    = trim(htmlentities($_POST['subject']));
        $teacher    = trim(htmlentities($_POST['teacher']));

        try {
            $insertStmt = $conn->prepare("INSERT
                INTO
                exams
                (name, at, duration, grade, subject, teacher, date)
                VALUES
                (?, ?, ?, ?, ?, ?, NOW())");
            $insertStmt->execute([
                $name,
                $at,
                $duration,
                $grade,
                $subject,
                $teacher
            ]);
            if($insertStmt->rowCount() > 0){
                echo message("Exam Has Added Succesfully", true);
            }else{
                echo message("Exam Has Not Added Succesfully");
            }
        } catch (PDOException $e) {
            echo message("Unexpected Error Has Happened");
        }
    }else{

        // loop on errors
        foreach ($errors as $error) {
            echo message($error);
        }

    }
}

// update exam function
function updateExam () {
    global $conn;

    // filter exam info
    $name       = trim(htmlentities($_POST['name']));
    $duration   = trim(htmlentities($_POST['duration']));
    $at         = trim(htmlentities($_POST['at']));
    $id         = trim(htmlentities($_POST['id']));
    $errors     = [];

    if(!preg_match("/^([a-zA-Z_ ]+)$/", $name)){$errors[] = 'Enter Exam\'s Valid Name';}
    if(!preg_match("/^[0-9]{1,3}$/", $duration)){$errors[] = 'Enter Valid Duration';}
    if(empty($id)){$errors[] = 'Error In Exam Id';}
    if(empty($at)){$errors[] = 'Enter Starting Of Exam';}
    if(!isset($_POST['grade'])){$errors[] = 'Choose Exam Grade';}
    if(!isset($_POST['subject'])){$errors[] = 'Choose Exam Subject';}
    if(!isset($_POST['teacher'])){$errors[] = 'Choose Exam Teacher';}

    // check on errors
    if(empty($errors)){ // empty
        $grade      = trim(htmlentities($_POST['grade']));
        $subject    = trim(htmlentities($_POST['subject']));
        $teacher    = trim(htmlentities($_POST['teacher']));

        try {
            $insertStmt = $conn->prepare("UPDATE
                exams
                SET
                name = ?, at = ?, duration = ?, grade = ?, subject = ?, teacher = ?
                WHERE
                id = ?");
            $insertStmt->execute([
                $name,
                $at,
                $duration,
                $grade,
                $subject,
                $teacher,
                $id
            ]);
            if($insertStmt->rowCount() > 0){
                echo message("Exam Has Updated Succesfully", true);
            }else{
                echo message("Exam Has Not Updated Succesfully");
            }
        } catch (PDOException $e) {
            echo message("Unexpected Error Has Happened");
        }
    }else{

        // loop on errors
        foreach ($errors as $error) {
            echo message($error);
        }

    }
}
?>

<div class="container mt-3">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-12 mb-3 mb-md-0">
            <div class="bg-white rounded border page-links p-2">
				<ul class="list-unstyled pe-0 mb-0">
					<li <?php if($_GET['page'] === 'exams' || $_GET['page'] === 'exam'){echo 'class="active"';}?>>
						<a href="<?php echo $_SERVER['PHP_SELF'] . '?page=exams'?>">All Exams</a>
					</li>
					<li <?php if($_GET['page'] === 'add_exam'){echo 'class="active"';}?>>
						<a href="<?php echo $_SERVER['PHP_SELF'] . '?page=add_exam'?>">Add Exam</a>
					</li>
				</ul>
			</div>
            <?php
            if($_GET['page'] === 'exams'){ // printing page options

                // redirect if wrong page
                $allowed_orders = ['DESC', 'ASC'];
                if(!in_array($_GET['order'], $allowed_orders) || !is_numeric($_GET['limit'])){header("location:" . $_SERVER['PHP_SELF'] . '?page=exams&limit=10&order=DESC');}

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
                if($_GET['page'] === 'exams'){ // exams page
                    ?>
                    <h6 class="main">All Exams</h6>
                    <table class="table table-striped text-center mb-0">
                        <thead>
                            <tr>
                                <td>#ID</td>
                                <td>Ex Name</td>
                                <td>Ex Grade</td>
                                <td>Ex subject</td>
                                <td>Ex Date</td>
                                <td>Options</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $exams = getExams($_GET['order'], $_GET['limit']);
                            if($exams !== 0){ // not empty result

                                // loop on result
                                foreach($exams as $exam){
                                    ?>
                                    <tr>
                                        <td><?php echo $exam['id'];?></td>
                                        <td><?php echo $exam['name'];?></td>
                                        <td><?php echo $exam['grade'];?></td>
                                        <td><?php echo getSubject($exam['subject']);?></td>
                                        <td><?php echo $exam['at'];?></td>
                                        <td>
                                            <a href="<?php echo $_SERVER['PHP_SELF'] . '?page=exam&id=' . $exam['id'];?>">
                                                <i class="fas fa-eye text-success" title="Show Info"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }

                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                }else if($_GET['page'] === 'exam'){ // show exam info page

                    // handling post requests
                    if($_SERVER['REQUEST_METHOD'] === 'POST'){
                        if(array_key_exists("edit_exam", $_POST)){ // edit exam request
                            updateExam();
                        }else if(array_key_exists("delete", $_POST)){
                            $deleteExam = $conn->prepare("DELETE
                                FROM
                                exams
                                WHERE 
                                id = ?");
                                $deleteExam->execute([$_POST['delete']]);
                        }
                    }

                    if(is_numeric($_GET['id'])){
                        $exam = getExam($_GET['id']);
                        ?>
                        <h6 class="main">Exam's Info</h6>
                        <?php
                        if($exam !== 0){ // found
                            ?>
                            <div class="row">
                                <div class="col-9">
                                    <ul class="list-square mt-3 mb-0" style="padding-left: 20px;">
                                        <li class="pb-2">
                                            Exam Name:
                                            <strong><?php echo $exam['name'];?></strong>
                                        </li>
                                        <li class="border-top py-2">
                                            Exam Grade:
                                            <strong><?php echo $exam['grade'] . ' Grade';?></strong>
                                        </li>
                                        <li class="border-top py-2">
                                            Exam subject:
                                            <strong><?php echo getSubject($exam['subject']);?></strong>
                                        </li>
                                        <li class="border-top py-2">
                                            Exam Teacher:
                                            <strong><?php echo getTeacher($exam['teacher']);?></strong>
                                        </li>
                                        <li class="border-top py-2">
                                            Exam duration:
                                            <strong><?php echo $exam['duration'] . ' Minuets';?></strong>
                                        </li>
                                        <li class="border-top py-2">
                                            Exam Date&Time:
                                            <strong><?php echo $exam['at'];?></strong>
                                        </li>
                                        <li class="border-top pt-2">
                                            Added At
                                            <strong><?php echo $exam['date'];?></strong>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-3 mt-3">
                                    <div class="gap-2 d-grid">
                                        <button class="btn btn-success">
                                            <a href="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&id=' . $_GET['id'] . '&edit';?>">
                                                Edit Exam
                                            </a>
                                        </button>
                                    </div>
                                    <div class="gap-2 d-grid mt-3">
                                        <button class="btn btn-danger" onclick="deleteExam()">Delete Exam</button>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }else{
                            ?>
                            <div class="alert alert-danger mb-0">Exam Not Found</div>
                            <?php
                        }
                        ?>
                        <script>
                            function deleteExam () {
                                if(confirm("Are You Sure Of This Step?")){ // true
                                    var deleteObj = new XMLHttpRequest();
                                    deleteObj.open("POST", "<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&id=' . $_GET['id']?>");
                                    deleteObj.onload = function () {

                                        if(this.readyState === 4 && this.status === 200){ // success
                                             location.reload();
                                        }else{
                                            alert("An Error Has Happened");
                                        }

                                    }
                                    deleteObj.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                    deleteObj.send("delete=" + "<?php echo $_GET['id'];?>");

                                }
                            }
                        </script>
                        <?php

                        if(isset($_GET['edit'])){ // edit exam info

                            if(getExam($_GET['id']) !== 0){
                                ?>
                                <div class="full-page">
                                    <div class="container">
                                        <div class="row">
                                            <div class="content col-lg-6 col-md-8 col-12 mx-auto">
                                                <div class="bg-white rounded border p-3">
                                                    <h6 class="main">Edit Exam</h6>
                                                    <?php $exam = getExam($_GET['id']);?>
                                                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&id=' . $_GET['id'];?>" id="edit-exam" method="post">
                                                        <input type="hidden" name="id" value="<?php echo $_GET['id']?>">
                                                        <div class="row mt-3">
                                                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                                                <label for="name">Exam Name</label>
                                                                <input type="text" name="name" class="form-control" id="name" placeholder="Exam Name" autofocus value="<?php echo $exam['name'];?>">
                                                                <small class="err-msg name"></small>
                                                            </div>
                                                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                                                <label for="grade">Exam Grade</label>
                                                                <select class="form-select" id="grade" name="grade">
                                                                    <option value="NULL" disabled selected>Select One</option>
                                                                    <option
                                                                    <?php if($exam['grade'] == 7){echo 'selected';}?>
                                                                    value="7">
                                                                        Grade 7
                                                                    </option>
                                                                    <option
                                                                    <?php if($exam['grade'] == 8){echo 'selected';}?>
                                                                    value="8">
                                                                        Grade 8
                                                                    </option>
                                                                    <option
                                                                    <?php if($exam['grade'] == 9){echo 'selected';}?>
                                                                    value="9">
                                                                        Grade 9
                                                                    </option>
                                                                </select>
                                                                <small class="err-msg grade"></small>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                                                <label for="subject">Exam Subject</label>
                                                                <select class="form-select" id="subject" name="subject">
                                                                    <option value="NULL" disabled>Select One</option>
                                                                    <?php
                                                                    $subjects = getSubjects();
                                                                    if($subjects !== 0){ // not empty result
                                                                        foreach ($subjects as $subject) {
                                                                            ?>
                                                                            <option 
                                                                            <?php if($subject['id'] == $exam['subject']){echo 'selected';}?>
                                                                            value="<?php echo $subject['id'];?>">
                                                                                <?php echo $subject['subject'];?>
                                                                            </option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <small class="err-msg subject"></small>
                                                            </div>
                                                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                                                <label for="teacher">Exam Teacher</label>
                                                                <select class="form-select" id="teacher" name="teacher">
                                                                    <option value="NULL" disabled>Select One</option>
                                                                    <?php
                                                                    $teachers = getTeachers();
                                                                    if($teachers !== 0){ // not empty result
                                                                        foreach ($teachers as $teacher) {
                                                                            ?>
                                                                            <option 
                                                                            <?php if($teacher['id'] == $exam['teacher']){echo 'selected';}?>
                                                                            value="<?php echo $teacher['id'];?>">
                                                                                <?php echo $teacher['name'];?>
                                                                            </option>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <small class="err-msg teacher"></small>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                                                <label for="duration">Exam Duration</label>
                                                                <input type="number" name="duration" class="form-control" id="duration" placeholder="Exam Duration" value="<?php echo $exam['duration'];?>">
                                                                <small class="err-msg duration"></small>
                                                            </div>
                                                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                                                <label for="at">Exam Starts At</label>
                                                                <input type="datetime-local" name="at" class="form-control" id="at" value="<?php echo str_replace(" ", "T", $exam['at']);?>">
                                                                <small class="err-msg at"></small>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 gap-2 d-grid">
                                                            <button class="btn btn-success" name="edit_exam">Update Exam</button>
                                                        </div>
                                                    </form>
                                                    <script>
                                                        const form = document.getElementById('edit-exam');
                                                        form.onsubmit = function (e) {

                                                            let name = form.querySelector('input#name'),
                                                                grade = form.querySelector('select#grade'),
                                                                subject = form.querySelector('select#subject'),
                                                                teacher = form.querySelector('select#teacher'),
                                                                duration = form.querySelector('input#duration'),
                                                                at = form.querySelector('input#at');

                                                            if(name.value.match(/^([a-zA-Z_ ]+)$/g) === null){
                                                                e.preventDefault();
                                                                name.focus();
                                                                name.classList.add("input-alert");
                                                                form.querySelector("small.name").textContent = 'Enter Exam\'s Valid Name';
                                                            }else{
                                                                name.classList.remove("input-alert");
                                                                form.querySelector("small.name").textContent = '';

                                                                if(grade.value === "NULL"){
                                                                    e.preventDefault();
                                                                    grade.focus();
                                                                    grade.classList.add("input-alert");
                                                                    form.querySelector("small.grade").textContent = 'Choose Exam Grade';
                                                                }else{
                                                                    grade.classList.remove("input-alert");
                                                                    form.querySelector("small.grade").textContent = '';

                                                                    if(subject.value === "NULL"){
                                                                        e.preventDefault();
                                                                        subject.focus();
                                                                        subject.classList.add("input-alert");
                                                                        form.querySelector("small.subject").textContent = 'Choose Exam Subject';
                                                                    }else{
                                                                        subject.classList.remove("input-alert");
                                                                        form.querySelector("small.subject").textContent = '';

                                                                        if(teacher.value === "NULL"){
                                                                            e.preventDefault();
                                                                            teacher.focus();
                                                                            teacher.classList.add("input-alert");
                                                                            form.querySelector("small.teacher").textContent = 'Choose Exam Teacher';
                                                                        }else{
                                                                            teacher.classList.remove("input-alert");
                                                                            form.querySelector("small.teacher").textContent = '';

                                                                            if(duration.value.match(/^[0-9]{1,3}$/g) === null){
                                                                                e.preventDefault();
                                                                                duration.focus();
                                                                                duration.classList.add("input-alert");
                                                                                form.querySelector("small.duration").textContent = 'Enter Valid Duration';
                                                                            }else{
                                                                                duration.classList.remove("input-alert");
                                                                                form.querySelector("small.duration").textContent = '';

                                                                                if(at.value === ""){
                                                                                    e.preventDefault();
                                                                                    at.focus();
                                                                                    at.classList.add("input-alert");
                                                                                    form.querySelector("small.at").textContent = 'Enter Starting Of Exam';
                                                                                }else{
                                                                                    at.classList.remove("input-alert");
                                                                                    form.querySelector("small.at").textContent = '';
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

                        }

                    }
                }else if($_GET['page'] === 'add_exam'){ // add exam page

                    // handling post requests
                    if($_SERVER['REQUEST_METHOD'] === 'POST'){
                        if(array_key_exists("add_exam", $_POST)){ // add exam request
                            addExam();
                        }
                    }

                    ?>
                    <h6 class="main">Add Exam</h6>
                    <form action="<?php echo $_SERVER['PHP_SELF'] . '?page=add_exam';?>" id="add-exam" method="post">
                        <div class="row mt-3">
                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                <label for="name">Exam Name</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Exam Name" autofocus>
                                <small class="err-msg name"></small>
                            </div>
                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                <label for="grade">Exam Grade</label>
                                <select class="form-select" id="grade" name="grade">
                                    <option value="NULL" disabled selected>Select One</option>
                                    <option value="7">Grade 7</option>
                                    <option value="8">Grade 8</option>
                                    <option value="9">Grade 9</option>
                                </select>
                                <small class="err-msg grade"></small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                <label for="subject">Exam Subject</label>
                                <select class="form-select" id="subject" name="subject">
                                    <option value="NULL" disabled selected>Select One</option>
                                    <?php
                                    $subjects = getSubjects();
                                    if($subjects !== 0){ // not empty result
                                        foreach ($subjects as $subject) {
                                            ?>
                                            <option value="<?php echo $subject['id'];?>"><?php echo $subject['subject'];?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <small class="err-msg subject"></small>
                            </div>
                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                <label for="teacher">Exam Teacher</label>
                                <select class="form-select" id="teacher" name="teacher">
                                    <option value="NULL" disabled selected>Select One</option>
                                    <?php
                                    $teachers = getTeachers();
                                    if($teachers !== 0){ // not empty result
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
                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                <label for="duration">Exam Duration</label>
                                <input type="number" name="duration" class="form-control" id="duration" placeholder="Exam Duration">
                                <small class="err-msg duration"></small>
                            </div>
                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                <label for="at">Exam Starts At</label>
                                <input type="datetime-local" name="at" class="form-control" id="at">
                                <small class="err-msg at"></small>
                            </div>
                        </div>
                        <div class="mt-3 gap-2 d-grid">
                            <button class="btn btn-success" name="add_exam">Add Exam</button>
                        </div>
                    </form>
                    <script>
                        const form = document.getElementById('add-exam');
                        form.onsubmit = function (e) {

                            let name = form.querySelector('input#name'),
                                grade = form.querySelector('select#grade'),
                                subject = form.querySelector('select#subject'),
                                teacher = form.querySelector('select#teacher'),
                                duration = form.querySelector('input#duration'),
                                at = form.querySelector('input#at');

                            if(name.value.match(/^([a-zA-Z_ ]+)$/g) === null){
                                e.preventDefault();
                                name.focus();
                                name.classList.add("input-alert");
                                form.querySelector("small.name").textContent = 'Enter Exam\'s Valid Name';
                            }else{
                                name.classList.remove("input-alert");
                                form.querySelector("small.name").textContent = '';

                                if(grade.value === "NULL"){
                                    e.preventDefault();
                                    grade.focus();
                                    grade.classList.add("input-alert");
                                    form.querySelector("small.grade").textContent = 'Choose Exam Grade';
                                }else{
                                    grade.classList.remove("input-alert");
                                    form.querySelector("small.grade").textContent = '';

                                    if(subject.value === "NULL"){
                                        e.preventDefault();
                                        subject.focus();
                                        subject.classList.add("input-alert");
                                        form.querySelector("small.subject").textContent = 'Choose Exam Subject';
                                    }else{
                                        subject.classList.remove("input-alert");
                                        form.querySelector("small.subject").textContent = '';

                                        if(teacher.value === "NULL"){
                                            e.preventDefault();
                                            teacher.focus();
                                            teacher.classList.add("input-alert");
                                            form.querySelector("small.teacher").textContent = 'Choose Exam Teacher';
                                        }else{
                                            teacher.classList.remove("input-alert");
                                            form.querySelector("small.teacher").textContent = '';

                                            if(duration.value.match(/^[0-9]{1,3}$/g) === null){
                                                e.preventDefault();
                                                duration.focus();
                                                duration.classList.add("input-alert");
                                                form.querySelector("small.duration").textContent = 'Enter Valid Duration';
                                            }else{
                                                duration.classList.remove("input-alert");
                                                form.querySelector("small.duration").textContent = '';

                                                if(at.value === ""){
                                                    e.preventDefault();
                                                    at.focus();
                                                    at.classList.add("input-alert");
                                                    form.querySelector("small.at").textContent = 'Enter Starting Of Exam';
                                                }else{
                                                    at.classList.remove("input-alert");
                                                    form.querySelector("small.at").textContent = '';
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

<?php include 'templates/_footer.php';?>
