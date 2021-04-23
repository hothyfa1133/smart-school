<?php
$pageName = 'courses';
$login = true;
require 'includes/init.php';

// redirect if wrong page
$allowed_links = [7, 8, 9];
if(!in_array($_GET['grade'], $allowed_links)){header("location:" . $_SERVER['PHP_SELF'] . '?grade=7');}

// get courses function
function getCourses ($grade) {
	global $conn;

	// get courses
	$getCourses = $conn->prepare("SELECT
	image, title, description
	FROM
	courses
	WHERE grade = ?
	ORDER BY id DESC");
	$getCourses->execute([$grade]);
	if($getCourses->rowCount() > 0){ // found
		return $getCourses->fetchAll();
	}else{ // not found
		return 0;
	}
}
?>

<div class="all-title-box">
	<div class="container text-center">
		<?php
		if($_GET['grade'] == 7){$val = 'Seventh';}
		if($_GET['grade'] == 8){$val = 'Eighth';}
		if($_GET['grade'] == 9){$val = 'Ninth';}
		?>
		<h1><?php echo $val;?> grade courses <span class="m_1">Welcome our students! There is a set of courses for various subjects of study here.</span></h1>
	</div>
</div>

<div id="overviews" class="section wb">
    <div class="container">
        <div class="section-title row text-center">
            
        </div><!-- end title -->

        <hr class="invis"> 
        <div class="row"> 
        	<?php
	        $courses = getCourses($_GET['grade']);
	        if($courses !== 0){ // not empty result

	        	// loop on result
	        	foreach ($courses as $course) {
	    			?>
	    			<div class="col-lg-6 col-md-6 col-12 mb-3">
		                <div class="course-item border p-1 rounded">
							<div class="image-blog">
								<img src="images/courses/<?php echo $course['image'];?>" alt="<?php echo $course['title'] . ' Image';?>" class="img-fluid">
							</div>
							<div class="course-br">
								<div class="course-title">
									<h2><?php echo $course['title'];?></h2>
								</div>
								<div class="course-desc">
									<p><?php echo nl2br($course['description']);?></p>
								</div>
							</div>
							
						</div>
		            </div><!-- end col -->
	    			<?php
	        	}

	    	}else{ // empty result
	    		?>
	    		<div class="col-12">
	    			<div class="alert alert-info">There Is No Courses For Grade <?php echo $_GET['grade'];?></div>
	    		</div>
	    		<?php
	    	}
	        ?>
        </div><!-- end row -->
		
		<hr class="hr3"> 
    </div><!-- end container -->
</div><!-- end section -->

<?php include 'templates/_footer.php';?>