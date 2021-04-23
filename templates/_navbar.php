<?php
// function to get admin
function getUser () {
    global $conn;

    // get user of the session
    if($_SESSION['smart_school_position'] === 0){ // student
        $position = 'students';
    }else if ($_SESSION['smart_school_position'] === 1){ // teacher
        $position = 'teachers';
    }else{header('location:user.php?page=logout');}

    $getUser = $conn->prepare("SELECT
        name
        FROM
        $position
        WHERE
        id = ?");
    $getUser->execute([$_SESSION['smart_school_user']]);
    return $getUser->fetch();
}

?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="images/logoo1.png"  alt="" style="width: 150px; height: 70px;"  />
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbars-host" aria-controls="navbars-rs-food" aria-expanded="false" aria-label="Toggle navigation">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbars-host">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item <?php if(isset($pageName) && $pageName == 'home'){echo 'active';}?>"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item <?php if(isset($pageName) && $pageName == 'about'){echo 'active';}?>"><a class="nav-link" href="about.php">About Us</a></li>
                <li class="nav-item dropdown <?php if(isset($pageName) && $pageName == 'courses'){echo 'active';}?>">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown-a" data-toggle="dropdown">Courses </a>
                    <div class="dropdown-menu" aria-labelledby="dropdown-a">
                        <a class="dropdown-item" href="courses.php?grade=7">Seventh grade </a>
                        <a class="dropdown-item" href="courses.php?grade=8">Eighth grade </a>
                        <a class="dropdown-item" href="courses.php?grade=9">Ninth grade </a>
                    </div>
                </li>
                <li class="nav-item <?php if(isset($pageName) && $pageName == 'news'){echo 'active';}?>"><a class="nav-link" href="blog.php">News</a></li>
                <li class="nav-item <?php if(isset($pageName) && $pageName == 'teachers'){echo 'active';}?>"><a class="nav-link" href="teachers.php">Teachers</a></li>

                <li class="nav-item <?php if(isset($pageName) && $pageName == 'contact'){echo 'active';}?>"><a class="nav-link" href="contact.php">Contact</a></li>
                <?php
                if(isset($_SESSION['smart_school_user'])){
                    ?>
                    <li class="ml-0 nav-item">
                        <div class="btn-group">
                          <button class="btn mt-1 text-uppercase text-white bg-transparent dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo getUser()['name'];?>
                          </button>
                          <div class="dropdown-menu">
                            <?php
                            if($_SESSION['smart_school_position'] === 0){
                                ?>
                                <a href="grades.php" class="dropdown-item">Veiw Grades</a>
                                <a href="exams.php" class="dropdown-item">Veiw Exams</a>
                                <?php
                            }
                            ?>
                            <a href="profile.php" class="dropdown-item">Edit Information</a>
                            <?php
                            if(isset($_SESSION['smart_school_user'])){
                                ?>
                                <div class="dropdown-divider"></div>
                                <a href="user.php?page=logout" class="dropdown-item">Log Out</a>
                                <?php
                            }
                            ?>
                          </div>
                        </div>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
            if(!isset($_SESSION['smart_school_user'])){
                ?>
                <ul class="nav navbar-nav navbar-right">
                    <li><a class="hover-btn-new log orange" href="user.php?page=login"><span>Log In</span></a></li>
                </ul>
                <?php
            }
            ?>
        </div>
    </div>
</nav>