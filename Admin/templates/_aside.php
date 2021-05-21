<?php

// get admin info function
function getAdmin()
{
    global $conn;

    // get info
    $getInfo = $conn->prepare("SELECT
      name, image, gender, id, position, subject
      FROM
      teachers
      WHERE
      id = ?");
    $getInfo->execute([$_SESSION['smart_school_id']]);
    return $getInfo->fetch();
}

// get teacher by id function
function getTeacherInfo($id)
{
    global $conn;

    // get teacher by id
    $getTeacher = $conn->prepare("SELECT
    name, image, subject, phone, email, fb_link, gender, date, position
    FROM
    teachers
    WHERE
    id = ?");
    $getTeacher->execute([$id]);
    if ($getTeacher->rowCount() > 0) {
        return $getTeacher->fetch();
    } else {
        return 0;
    }
}

// get subjects function
function getTSubjects()
{
    global $conn;

    // get all subjects
    $getSubjects = $conn->prepare("SELECT
    id, subject
    FROM
    subjects
    ORDER BY subject ASC");
    $getSubjects->execute();
    if ($getSubjects->rowCount() > 0) {
        return $getSubjects->fetchAll();
    } else {
        return 0;
    }
}

// update teacher function
function updateTeacherInfo()
{
    global $conn;

    // validate inputs
    $name       = trim(htmlentities($_POST['name']));
    $phone      = trim(htmlentities($_POST['phone']));
    $email      = trim(htmlentities($_POST['email']));
    $fb         = trim(htmlentities($_POST['fb']));
    $image      = $_FILES['image'];
    $errors     = [];

    if (!preg_match("/^([a-zA-Z_ ]+)$/", $name)) {
        $errors[] = 'Enter Teacher Valid Name';
    }
    if (!isset($_POST['subject'])) {
        $errors[] = 'Choose Teacher\'s Subject';
    }
    if (!preg_match("/^([0-9]){6,}$/", $phone)) {
        $errors[] = 'Please Enter Correct Phone Number';
    }
    if (!preg_match("/^\S+@\S+\.\S+$/", $email)) {
        $errors[] = 'Please Enter Correct Email';
    }
    if (!isset($_POST['gender'])) {
        $errors[] = 'Choose Teacher\'s Gender';
    }
    if (!empty($_POST['password'])) {
        if (!preg_match("/^(.){8,}$/", $_POST['password'])) {
            $errors[] = 'Password Must Contain 8 Charachters At Least';
        }
    }

    if ($image['name'] !== "") { // has update image

        // extension
        @$extension = strtolower(end(explode(".", $image['name'])));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($extension, $allowed_extensions)) { // error in extension
            $errors[] = 'Images Allowed Extinsions Is <br>' . implode(", ", $allowed_extensions) . ' Only';
        }

        // size
        $maxSize = 5;
        if ($image['size'] / 1048576 > $maxSize) {
            $errors[] = 'Max Image Size Is ' . $maxSize . ' Megabytes';
        }

        // errors
        if ($image['error'] > 0) {
            $errors[] = 'Error During Uploading Image';
        }
    }

    if (empty($errors)) { // check true

        // upload image
        try {
            if ($image['name'] === "") { // empty
                $t_image = $_POST['old_image'];
            } else {

                if (file_exists("../images/teachers/" . $_POST['old_image'])) {
                    unlink("../images/teachers/" . $_POST['old_image']);
                }

                $t_image = rand(1000, 80000) . '_' . $image['name'];
                move_uploaded_file($image['tmp_name'], "../images/teachers/" . $t_image);
            }
        } catch (Exception $e) {
            echo message("Error During Uploading Image");
        }

        try {

            // update password if has set
            if (!empty($_POST['password'])) {
                $upPassword = $conn->prepare("UPDATE
                    teachers
                    SET
                    password = ?
                    WHERE
                    id = ?");
                $upPassword->execute([sha1($_POST['password']), $_SESSION['smart_school_id']]);
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
                $_SESSION['smart_school_id']
            ]);
            if ($updateStmt->rowCount() > 0 || (isset($upPassword) && $upPassword->rowCount() > 0)) {
                echo message("Teacher's Info Has Updated Succesfully", true);
            } else {
                echo message("Teacher's Info Has Not Updated Succesfully");
            }
        } catch (PDOException $e) {
            echo message("Unexpected Error Has Happened");
        }
    } else { // check false
        foreach ($errors as $error) {
            echo message($error);
        }
    }
}

if (getAdmin()['position'] == 0) {

    // handling post requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (array_key_exists('edit-teacher-form-no-admin', $_POST)) {
            updateTeacherInfo();
        }
    }

    $teacher = getTeacherInfo($_SESSION['smart_school_id']);
?>
    <div class="modal" tabindex="-1" id="exampleModal" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="edit-teacher" enctype="multipart/form-data">
                        <input type="hidden" name="edit-teacher-form-no-admin">
                        <input type="hidden" name="old_image" value="<?php echo $teacher['image']; ?>">
                        <input type="hidden" name="position" value="<?php echo $teacher['position']; ?>">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <label for="name">Name</label>
                                <input type="text" name="name" autofocus placeholder="Teacher's Name" id="name" class="form-control" value="<?php echo $teacher['name']; ?>">
                                <small class="err-msg name"></small>
                            </div>
                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                <label for="phone">Phone</label>
                                <input type="number" name="phone" autofocus placeholder="Teacher's Phone" id="phone" class="form-control" value="<?php echo $teacher['phone']; ?>">
                                <small class="err-msg phone"></small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6 col-12">
                                <label for="email">Email</label>
                                <input type="email" name="email" placeholder="Teacher's Email" id="email" class="form-control" value="<?php echo $teacher['email']; ?>">
                                <small class="err-msg email"></small>
                            </div>
                            <div class="col-md-6 col-12 mb-3 mb-md-0">
                                <label for="gender">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="NULL" disabled>Choose Gender</option>
                                    <option <?php if ($teacher['gender'] == 0) {
                                                echo 'selected';
                                            } ?> value="0">Male</option>
                                    <option <?php if ($teacher['gender'] == 1) {
                                                echo 'selected';
                                            } ?> value="1">Female</option>
                                </select>
                                <small class="err-msg gender"></small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6 col-12 mt-3 mt-md-0">
                                <label for="subject">Subject</label>
                                <select name="subject" id="subject" class="form-select">
                                    <option value="NULL" disabled>Choose Subject</option>
                                    <?php
                                    if (getTSubjects() != 0) { // not empty result

                                        // loop on result
                                        foreach (getTSubjects() as $subject) {
                                    ?>
                                            <option <?php if ($subject['id'] === $teacher['subject']) {
                                                        echo 'selected';
                                                    } ?> value="<?php echo $subject['id']; ?>"><?php echo $subject['subject']; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <small class="err-msg subject"></small>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="fb">Facebook Link</label>
                                <input type="url" name="fb" id="fb" class="form-control" placeholder="Teacher's Facebook Link" pattern="https://.*" value="<?php echo $teacher['fb_link']; ?>">
                                <small class="err-msg fb"></small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <label for="password">New Password</label>
                                <input type="password" name="password" placeholder="Write a New Password" id="password" class="form-control">
                                <small class="err-msg password"></small>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <label for="image">Teacher's Image</label>
                                <input class="form-control" type="file" id="image" name="image">
                            </div>
                        </div>
                    </form>
                    <script>
                        const form = document.getElementById("edit-teacher");
                        const inputs = form.querySelectorAll("input[type='text'], input[type='email'], input[type='number']");
                        for (let i = 0; i < inputs.length; i++) {
                            inputs[i].setAttribute("autocomplete", "off");
                        }
                        form.onsubmit = function(e) {

                            // validate inputs
                            let name = form.querySelector("input#name"),
                                subject = form.querySelector("select#subject"),
                                phone = form.querySelector("input#phone"),
                                email = form.querySelector("input#email"),
                                gender = form.querySelector("select#gender"),
                                password = form.querySelector("input#password");

                            if (name.value.match(/^([a-zA-Z_ ]+)$/g) === null) {
                                e.preventDefault();
                                name.focus();
                                name.classList.add("input-alert");
                                form.querySelector("small.name").textContent = 'Enter Teacher\'s Valid Name';
                            } else {
                                name.classList.remove("input-alert");
                                form.querySelector("small.name").textContent = '';

                                if (subject.value === "NULL") {
                                    e.preventDefault();
                                    subject.focus();
                                    subject.classList.add("input-alert");
                                    form.querySelector("small.subject").textContent = 'Choose Teacher\'s Subject';
                                } else {
                                    subject.classList.remove("input-alert");
                                    form.querySelector("small.subject").textContent = '';
                                    if (phone.value.match(/^([0-9]){6,}$/g) === null) {
                                        e.preventDefault();
                                        phone.focus();
                                        phone.classList.add("input-alert");
                                        form.querySelector("small.phone").textContent = 'Please Enter Correct Phone Number';
                                    } else {
                                        phone.classList.remove("input-alert");
                                        form.querySelector("small.phone").textContent = '';
                                        if (email.value.match(/^\S+@\S+\.\S+$/g) === null) {
                                            e.preventDefault();
                                            email.focus();
                                            email.classList.add("input-alert");
                                            form.querySelector("small.email").textContent = 'Please Enter Correct Email';
                                        } else {
                                            email.classList.remove("input-alert");
                                            form.querySelector("small.email").textContent = '';
                                            if (gender.value === "NULL") {
                                                e.preventDefault();
                                                gender.focus();
                                                gender.classList.add("input-alert");
                                                form.querySelector("small.gender").textContent = 'Choose Teacher\'s Gender';
                                            } else {
                                                gender.classList.remove("input-alert");
                                                form.querySelector("small.gender").textContent = '';

                                                if (password.value !== "") {
                                                    if (password.value.match(/^(.){8,}$/g) === null) {
                                                        e.preventDefault();
                                                        password.focus();
                                                        password.classList.add("input-alert");
                                                        form.querySelector("small.password").textContent = 'Password Must Contain 8 charachters At Least';
                                                    } else {
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" onclick="document.getElementById('edit-teacher').submit();" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>

<aside class="main-aside p-4">
    <div class="admin-box p-2 border-bottom pb-1">
        <div class="image rounded-circle">
            <?php
            if (empty(getAdmin()['image'])) {

                if (getAdmin()['gender'] == 0) { // male
                    $image = 'assests/admin/default0.jpg';
                } else {
                    $image = 'assests/admin/default1.jpg';
                }
            } else {
                $image = '../images/teachers/' . getAdmin()['image'];
            }
            ?>
            <img src="<?php echo $image; ?>" class="img-thumbnail" alt="<?php echo getAdmin()['name']; ?>">
        </div>
        <div class="info">
            <span class="d-block"><?php echo getAdmin()['name']; ?></span>
            <span>
                <a href="user.php?page=logout">
                    <i class="fas fa-sign-out-alt" title="Log Out" style="cursor: pointer;"></i>
                </a>
            </span>
            <span>
                <?php
                if (getAdmin()['position'] == 0) {
                    $href = 'data-bs-toggle="modal" data-bs-target="#exampleModal"';
                } else {
                    $href = '';
                }
                ?>
                <a <?php echo $href; ?> href="teachers.php?page=teacher&id=<?php echo getAdmin()['id']; ?>&edit">
                    <i class="fas fa-edit ms-1" style="font-size: 15px" title="Edit Info"></i>
                </a>
            </span>
        </div>
    </div>
    <ul class="ps-0 mt-4 list-unstyled">
        <li>
            <a href="../index.php" target="_blank">
                <span class="material-icons">visibility</span>
                Veiw Website
            </a>
        </li>
        <?php
        if (getAdmin()['position'] == 1) { // admin
        ?>
            <li <?php if (isset($pageName) && $pageName === "dashboard") {
                    echo 'class="active"';
                } ?>>
                <a href="index.php">
                    <span class="material-icons">dashboard</span>
                    Dashboard
                </a>
            </li>
            <li <?php if (isset($pageName) && $pageName === "activation") {
                    echo 'class="active"';
                } ?>>
                <a href="activation.php">
                    <span class="material-icons">check</span>
                    Activation Requests
                </a>
            </li>
            <li <?php if (isset($pageName) && $pageName === "courses") {
                    echo 'class="active"';
                } ?>>
                <a href="courses.php">
                    <span class="material-icons">class</span>
                    Courses
                </a>
            </li>
            <li <?php if (isset($pageName) && $pageName === "news") {
                    echo 'class="active"';
                } ?>>
                <a href="news.php">
                    <span class="material-icons">feed</span>
                    News
                </a>
            </li>
            <li <?php if (isset($pageName) && $pageName === "teachers") {
                    echo 'class="active"';
                } ?>>
                <a href="teachers.php">
                    <span class="material-icons">account_circle</span>
                    Teachers
                </a>
            </li>
            <li <?php if (isset($pageName) && $pageName === "students") {
                    echo 'class="active"';
                } ?>>
                <a href="students.php">
                    <span class="material-icons">groups</span>
                    Students
                </a>
            </li>
            <li <?php if (isset($pageName) && $pageName === "marks") {
                    echo 'class="active"';
                } ?>>
                <a href="marks.php">
                    <span class="material-icons">task_alt</span>
                    Set Marks
                </a>
            </li>
            <li <?php if (isset($pageName) && $pageName === "exams") {
                    echo 'class="active"';
                } ?>>
                <a href="exams.php">
                    <span class="material-icons">quiz</span>
                    Exams
                </a>
            </li>
            <li <?php if (isset($pageName) && $pageName === "subjects") {
                    echo 'class="active"';
                } ?>>
                <a href="subjects.php">
                    <span class="material-icons">auto_stories</span>
                    Subjects
                </a>
            </li>
            <li <?php if (isset($pageName) && $pageName === "contact") {
                    echo 'class="active"';
                } ?>>
                <a href="contact.php">
                    <span class="material-icons">contact_support</span>
                    Contact
                </a>
            </li>
        <?php
        } else if (getAdmin()['position'] == 0) { // teacher
        ?>
            <li <?php if (isset($pageName) && $pageName === "dashboard") {
                    echo 'class="active"';
                } ?>>
                <a href="index.php">
                    <span class="material-icons">dashboard</span>
                    Dashboard
                </a>
            </li>
            <li <?php if (isset($pageName) && $pageName === "students") {
                    echo 'class="active"';
                } ?>>
                <a href="students.php">
                    <span class="material-icons">groups</span>
                    Students
                </a>
            </li>
            <li <?php if (isset($pageName) && $pageName === "marks") {
                    echo 'class="active"';
                } ?>>
                <a href="marks.php">
                    <span class="material-icons">task_alt</span>
                    Set Marks
                </a>
            </li>
            <li <?php if (isset($pageName) && $pageName === "exams") {
                    echo 'class="active"';
                } ?>>
                <a href="exams.php">
                    <span class="material-icons">quiz</span>
                    Exams
                </a>
            </li>
            <li <?php if (isset($pageName) && $pageName === "courses") {
                    echo 'class="active"';
                } ?>>
                <a href="courses.php?page=courses_videos">
                    <span class="material-icons">play_circle</span>
                    Courses Videos
                </a>
            </li>
        <?php
        }
        ?>
    </ul>
</aside>