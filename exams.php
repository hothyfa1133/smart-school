<?php
$login = true;
require 'includes/init.php';

// redirect if the user is a teacher
if($_SESSION['smart_school_position'] === 1){ // teacher
	header('location:index.php');
}

// get grade function
function grade () {
	global $conn;

	// get grade of the user that logged in
	$getGrade = $conn->prepare("SELECT
		grade
		FROM
		students
		WHERE
		id = ?");
	$getGrade->execute([$_SESSION['smart_school_user']]);
	return $getGrade->fetchColumn();
}

// get exams function
function getExams ($grade) {
	global $conn;

	// get exams of custome grade
	$getExams = $conn->prepare("SELECT
		name, at, duration, subject, teacher
		FROM
		exams
		WHERE
		grade = ?");
	$getExams->execute([$grade]);
	if($getExams->rowCount() > 0){ // found
		return $getExams->fetchAll();
	}else{
		return 0;
	}
}

// function to get names
function names ($id, $table) {
	global $conn;

	// get name by id and table name[teachers, subjects]
	if($table === 'teachers'){
		$col = 'name';
	}else if($table === 'subjects'){
		$col = 'subject';
	}else{
		return 'UNKNOWN';
		die();
	}

	$getNames = $conn->prepare("SELECT
		$col
		FROM
		$table
		WHERE
		id = ?");
	$getNames->execute([$id]);
	if($getNames->rowCount() > 0){ // found
		return $getNames->fetchColumn();
	}else{
		return 'UNKNOWN';
	}
}
?>

<div class="all-title-box">
    <div class="container text-center">
        <h1>
           Exams
           <span class="m_1">Showing All Exams</span>
        </h1>
    </div>
</div>
<div class="container">
	<div class="p-3">
		<h1>
			<small><strong>Your Exams</strong></small>
		</h1>
		<div class="row mt-3">
			<?php
			$exams = getExams(grade());
			if($exams !== 0){ // not empty result
				foreach ($exams as $exam) {
					?>
					<div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
						<div class="bg-white border p-3 rounded">
							<ul class="mb-0 ml-3 fw-bold">
								<li>> <?php echo $exam['name'];?></li>
								<li>> Exam Duration: <?php echo $exam['duration'];?> Minuets</li>
								<hr>
								<li>
									<p style="font-weight: normal;" class="mb-0 text-black-50">
										This Exam At <?php echo names($exam['subject'], 'subjects') .
										' With Mr ' . names($exam['teacher'], 'teachers');?>
									</p>
								</li>
							</ul>
							<hr>
							<h4 class="mb-0 text-center fw-bold text-danger">
								<?php echo str_replace(' ', '<br>', $exam['at']);?>
							</h4>
						</div>
					</div>
					<?php
				}
			}else{
				?>
				<div class="col-12">
					<div class="alert alert-info">There Is No News Yet</div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>

<?php include 'templates/_footer.php';?>