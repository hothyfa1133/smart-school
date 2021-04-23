<?php
$login = true;
require 'includes/init.php';

// redirect if the user is a teacher
if($_SESSION['smart_school_position'] === 1){ // teacher
	header('location:index.php');
}

// get dates function
function getDates () {
    global $conn;

    // get dates from db
    $getDates = $conn->prepare('SELECT
    DISTINCT
    MONTH(date) as month
    FROM
    marks
    WHERE student = ?');
    $getDates->execute([$_SESSION['smart_school_user']]);
    if($getDates->rowCount() > 0){
        return $getDates->fetchAll();
    }else{
        return 0;
    }
}

// redirect if wrong page
if(isset($_GET['month'])){
    $dates = getDates();
    if($dates !== 0){
        $allowed_months = [];
        foreach ($dates as $date) {
            $allowed_months[] = $date['month'];
        }
        if(!in_array($_GET['month'], $allowed_months)){
            header('location:' . $_SERVER['PHP_SELF']);
        }
    }
}

// get marks
function getMarks ($month) {
    global $conn;
    
    // get marks
    $getMarks = $conn->prepare('SELECT
    subject, mark, full_mark
    FROM
    marks
    WHERE MONTH(date) = ?
    AND
    student = ?
    ORDER BY id DESC');
    $getMarks->execute([$month, $_SESSION['smart_school_user']]);
    if($getMarks->rowCount() > 0){
        return $getMarks->fetchAll();
    }else{
        return 0;
    }
}

// get subjects name
function getSubject ($subject){
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

?>

<div class="all-title-box">
    <div class="container text-center">
        <h1>
           Grades
           <span class="m_1">Showing Your Grades At All Exams</span>
        </h1>
    </div>
</div>
<div class="container">
    <div class="p-3">
        <h1>
			<small><strong>Your Marks</strong></small>
		</h1>
		<div class="row mt-3">
            <div class="col-lg-3 col-md-4 col-12 page-links">
                <div class="bg-white rounded border p-2">
                    <?php
                    $dates = getDates();
                    if($dates !== 0){ // found
                        ?>
                        <ul class="list-unstyled mb-0 ps-0">
                            <?php
                            foreach ($dates as $date) {
                                ?>
                                <li <?php if(isset($_GET['month']) && $_GET['month'] == $date['month']){ echo 'class="active"';}?>>
                                    <a href="<?php echo $_SERVER['PHP_SELF'] . '?month=' . $date['month'];?>"><?php echo 'Month: ' . $date['month'];?></a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                        <?php
                    }else{
                        ?>
                        <div class="alert alert-info mb-0">No Grades Yet</div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="col-lg-9 col-md-4 col-12">
                <?php
                if(!isset($_GET['month'])){
                    ?>
                    <div class="alert alert-warning mb-0">Choose Month To Get It's Grades</div>
                    <?php
                }else{
                    $marks = getMarks(trim(htmlentities($_GET['month'])));
                    if($marks !== 0){
                        ?>
                        <table class="table-bordered table text-center">
                            <thead>
                                <tr>
                                    <?php
                                    foreach ($marks as $key) {
                                        echo '<td>' . getSubject($key['subject']) . '</td>';
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    foreach ($marks as $key) {
                                        echo '<td>' . $key['mark'] . '/' . $key['full_mark'] . '</td>';
                                    }
                                    ?>
                                </tr>
                            </tbody>
                        </table>
                        <?php
                    }else{
                        ?>
                        <div class="alert alert-info">There Is No Marks Yet</div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/_footer.php';?>