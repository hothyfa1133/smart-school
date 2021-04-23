<?php

// get admin info function
function getAdmin () {
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

?>

<aside class="main-aside p-4">
    <div class="admin-box p-2 border-bottom pb-1">
        <div class="image rounded-circle">
            <?php
            if(empty(getAdmin()['image'])){

                if(getAdmin()['gender'] == 0){ // male
                    $image = 'assests/admin/default0.jpg';
                }else{
                    $image = 'assests/admin/default1.jpg';
                }

            }else{
                $image = '../images/teachers/' . getAdmin()['image'];
            }
            ?>
            <img src="<?php echo $image;?>" class="img-thumbnail" alt="<?php echo getAdmin()['name'];?>">
        </div>
        <div class="info">
            <span class="d-block"><?php echo getAdmin()['name'];?></span>
            <span>
                <a href="user.php?page=logout">
                    <i class="fas fa-sign-out-alt" title="Log Out" style="cursor: pointer;"></i>
                </a>
            </span>
            <span>
                <a href="teachers.php?page=teacher&id=<?php echo getAdmin()['id'];?>&edit">
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
        if(getAdmin()['position'] == 1){ // admin
            ?>
            <li <?php if(isset($pageName) && $pageName === "dashboard"){echo 'class="active"';}?>>
                <a href="index.php">
                    <span class="material-icons">dashboard</span>
                    Dashboard
                </a>
            </li>
            <li <?php if(isset($pageName) && $pageName === "courses"){echo 'class="active"';}?>>
               <a href="courses.php">
                   <span class="material-icons">class</span>
                    Courses
               </a>
            </li>
            <li <?php if(isset($pageName) && $pageName === "news"){echo 'class="active"';}?>>
               <a href="news.php">
                   <span class="material-icons">feed</span>
                    News
               </a>
            </li>
            <li <?php if(isset($pageName) && $pageName === "teachers"){echo 'class="active"';}?>>
               <a href="teachers.php">
                   <span class="material-icons">account_circle</span>
                    Teachers
               </a>
            </li>
            <li <?php if(isset($pageName) && $pageName === "students"){echo 'class="active"';}?>>
               <a href="students.php">
                   <span class="material-icons">groups</span>
                    Students
               </a>
            </li>
            <li <?php if(isset($pageName) && $pageName === "marks"){echo 'class="active"';}?>>
               <a href="marks.php">
                   <span class="material-icons">task_alt</span>
                    Set Marks
               </a>
            </li>
            <li <?php if(isset($pageName) && $pageName === "exams"){echo 'class="active"';}?>>
               <a href="exams.php">
                   <span class="material-icons">quiz</span>
                    Exams
               </a>
            </li>
            <li <?php if(isset($pageName) && $pageName === "subjects"){echo 'class="active"';}?>>
               <a href="subjects.php">
                   <span class="material-icons">auto_stories</span>
                    Subjects
               </a>
            </li>
            <li <?php if(isset($pageName) && $pageName === "contact"){echo 'class="active"';}?>>
               <a href="contact.php">
                   <span class="material-icons">contact_support</span>
                    Contact
               </a>
            </li>
            <li <?php if(isset($pageName) && $pageName === "database"){echo 'class="active"';}?>>
               <a href="database.php">
                   <span class="material-icons">storage</span>
                    Database
               </a>
            </li>
            <?php
        }else if(getAdmin()['position'] == 0){ // teacher
            ?>
            <li <?php if(isset($pageName) && $pageName === "dashboard"){echo 'class="active"';}?>>
                <a href="index.php">
                    <span class="material-icons">dashboard</span>
                    Dashboard
                </a>
            </li>
            <li <?php if(isset($pageName) && $pageName === "students"){echo 'class="active"';}?>>
               <a href="students.php">
                   <span class="material-icons">groups</span>
                    Students
               </a>
            </li>
            <li <?php if(isset($pageName) && $pageName === "marks"){echo 'class="active"';}?>>
               <a href="marks.php">
                   <span class="material-icons">task_alt</span>
                    Set Marks
               </a>
            </li>
            <li <?php if(isset($pageName) && $pageName === "exams"){echo 'class="active"';}?>>
               <a href="exams.php">
                   <span class="material-icons">quiz</span>
                    Exams
               </a>
            </li>
            <?php
        }
        ?>
    </ul>
</aside>
