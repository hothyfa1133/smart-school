<?php
$pageName = 'dashboard';
require 'includes/init.php';

// get courses number
function getNum ($table) {
	global $conn;

	// get all numbers
	$getNum = $conn->prepare("SELECT
		COUNT(id)
		FROM
		$table");
	$getNum->execute();
	return $getNum->fetchColumn();
}

function getTeachersNums ($visibility = 1) {
	global $conn;

	// get teachers nums
	$getTeachersNums = $conn->prepare("SELECT
		COUNT(id)
		FROM
		teachers
		WHERE
		visibility = ?");
	$getTeachersNums->execute([$visibility]);
	return $getTeachersNums->fetchColumn();
}

// get courses number by grade
function getCoursesBGrade ($grade) {
	global $conn;

	// get courses by grade
	$getNum = $conn->prepare("SELECT
		COUNT(id)
		FROM
		courses
		WHERE
		grade = ?");
	$getNum->execute([$grade]);
	return $getNum->fetchColumn();
}

// get teachers
function getTeachersSubs () {
	global $conn;

	// getting subjects
	$getSubjects = $conn->prepare("SELECT
		subject, id
		FROM
		subjects");
	$getSubjects->execute();

	$subjects = [];

	foreach ($getSubjects->fetchAll() as $key => $value) {
		$getTeachers = $conn->prepare("SELECT
			COUNT(id)
			FROM
			teachers
			WHERE
			subject = ?");
		$getTeachers->execute([$value['id']]);
		$subjects[$value['subject']] = $getTeachers->fetchColumn();
	}
	return json_encode($subjects);
}

// get student grade
function getStudentGrades () {
	global $conn;

	// get student grade
	$grades = [7, 8, 9];
	$gradesNum = [];

	foreach ($grades as $grade) {
		$getGrade = $conn->prepare("SELECT
			COUNT(id)
			FROM
			students
			WHERE
			grade = ?");
		$getGrade->execute([$grade]);
		$gradesNum[$grade] = $getGrade->fetchColumn();
	}

	return json_encode($gradesNum);

}
?>

<div class="container mt-3">
	<div class="rounded border p-3 bg-white">
		<div class="row">
			<div class="col-12 mb-3">
				<div class="rounded border p-3">
					<h6 class="main">Courses Number</h6>
					<div class="row">
						<div class="col-md-5 col-12 mb-3 mb-md-0">
							<div class="row">	
								<div class="col-4 border-end">
									<span class="in-number d-block"><?php echo getNum('courses');?></span>
									<span>Number Of All Grades Courses</span>
								</div>
								<div class="col-8">
									<ul class="list-unstyled ps-0 mb-0">
										<li class="py-2 border-bottom">
											Grade 7 =>  
											<span class="text-primary fw-bold"><?php echo getCoursesBGrade(7)?> Course</span>
										</li>
										<li class="py-2 border-bottom">
											Grade 8 =>  
											<span class="text-primary fw-bold"><?php echo getCoursesBGrade(8)?> Course</span>
										</li>
										<li class="py-2 border-bottom">
											Grade 9 =>  
											<span class="text-primary fw-bold"><?php echo getCoursesBGrade(9)?> Course</span>
										</li>
									</ul>
								</div>
							</div>	
						</div>
						<div class="border-start col-md-7 col-12">
							<div class="row">
							    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
							    <script type="text/javascript">
							      google.charts.load('current', {'packages':['corechart']});
							      google.charts.setOnLoadCallback(drawChart);

							      function drawChart() {

							        var data = google.visualization.arrayToDataTable([
							          ['Task', 'Hours per Day'],
							          ['Grade 7', <?php echo getCoursesBGrade(7)?>],
							          ['Grade 8', <?php echo getCoursesBGrade(8)?>],
							          ['Grade 9', <?php echo getCoursesBGrade(9)?>]
							        ]);

							        var options = {
							          title: 'Courses Chart'
							        };

							        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

							        chart.draw(data, options);
							      }
							    </script>
							    <div id="piechart" style="width: 100%; height: 200px;"></div>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-12 mb-3 mb-sm-0">
				<div class="rounded border p-3">
					<h6 class="main">Teachers Number</h6>
					<div class="row">
						<div class="col-4">
							<span class="in-number d-block"><?php echo getTeachersNums();?></span>
							<span>Number Of All Teachers</span>
						</div>
						<div class="col-8">
							<script>
								google.charts.setOnLoadCallback(drawChart2);

							      function drawChart2() {

							        var data = google.visualization.arrayToDataTable([
							        	['Task', 'Hours per Day'],
							        	<?php
										$subs = json_decode(getTeachersSubs(), true);
										foreach ($subs as $key => $value) {
											?>
											["<?php echo $key?>", <?php echo $value?>],
											<?php
										}
							        	?>
							        ]);

							        var options = {
							          title: 'Subjects Chart'
							        };

							        var chart = new google.visualization.PieChart(document.getElementById('piechart2'));

							        chart.draw(data, options);
							      }
							</script>
							<div id="piechart2" style="width: 100%; height: 200px;"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-12 mb-3 mb-sm-0">
				<div class="rounded border p-3">
					<h6 class="main">Students Number</h6>
					<div class="row">
						<div class="col-4">
							<span class="in-number d-block"><?php echo getNum('students');?></span>
							<span>Number Of All Students</span>
						</div>
						<div class="col-8">
							<script>
								google.charts.setOnLoadCallback(drawChart3);

							      function drawChart3() {

							        var data = google.visualization.arrayToDataTable([
							        	['Task', 'Hours per Day'],
							        	<?php
										$grades = json_decode(getStudentGrades(), true);
										foreach ($grades as $key => $value) {
											?>
											["<?php echo 'Grade ' . $key?>", <?php echo $value?>],
											<?php
										}
							        	?>
							        ]);

							        var options = {
							          title: ' Students Chart'
							        };

							        var chart = new google.visualization.PieChart(document.getElementById('piechart3'));

							        chart.draw(data, options);
							      }
							</script>
							<div id="piechart3" style="width: 100%; height: 200px;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include 'templates/_footer.php';?>