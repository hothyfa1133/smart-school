<?php
$pageName = 'marks';
require 'includes/init.php';

// redirect if wrong page
$allowed_links = ['marks', 'add_marks'];
if(!in_array($_GET['page'], $allowed_links)){header('location:' . $_SERVER['PHP_SELF'] . '?page=marks');}

// get marks function
function getMarks ($limit = 10, $order = 'DESC') {
	global $conn;

	// get marks
	$getMarks = $conn->prepare('SELECT
	id, student, subject, mark, full_mark, date
	FROM
	marks
	WHERE
	teacher = ?');
	$getMarks->execute([$_SESSION['smart_school_id']]);
	if($getMarks->rowCount() > 0){
		return $getMarks->fetchAll();
	}else{
		return 0;
	}
}

// get subjects function
function getSubjects () {
	global $conn;

	// get all subjects
	$getSubjects = $conn->prepare('SELECT
	subject, id
	FROM
	subjects
	ORDER BY subject ASC');
	$getSubjects->execute();
	if($getSubjects->rowCount() > 0){
		return $getSubjects->fetchAll();
	}else{
		return 0;
	}
}

// add marks function
function addMarks () {
	global $conn;

	// add marks function
	$student_id = trim(htmlentities($_POST['student-id']));
	$student_name = trim(htmlentities($_POST['student']));
	$mark = trim(htmlentities($_POST['mark']));
	$subject = trim(htmlentities($_POST['subject']));
	$errors = [];

	// check info
	if(!preg_match('/^[0-9]+$/', $student_id)){$errors[] = 'Error In Student Information';}
	if(empty($student_name)){$errors[] = 'Error In Student Name';}
	if(!preg_match('/[0-9]{1,3}\/[0-9]{1,3}/', $mark)){$errors
	[] = 'Please Write The Mark Like The Examble Below';}

	// validate subject
	if(getAdmin()['position'] == 0){ // teacher
		$getSubject = $conn->prepare('SELECT
		subject
		FROM
		teachers
		WHERE
		id = ?');
		$getSubject->execute([$_SESSION['smart_school_id']]);
		if($getSubject->fetchColumn() != $subject){
			$errors[] = 'Please Choose Your Subject Only';
		}
	}

	// validate student
	$getStudent = $conn->prepare("SELECT
	id
	FROM
	students
	WHERE
	id = ?
	AND
	name = ?");
	$getStudent->execute([$student_id, $student_name]);
	if($getStudent->rowCount() == 0){
		$errors[] = 'Student Not Found';
	}

	// check on errors
	if(empty($errors)){ // check true

		$marks = explode('/', $mark);

		try {
			$insertQ = $conn->prepare('INSERT
			INTO
			marks
			(student, mark, full_mark, subject, teacher, date)
			VALUES
			(?, ?, ?, ?, ?, NOW())');
			$insertQ->execute([$student_id, $marks[0], $marks[1], $subject, $_SESSION['smart_school_id']]);
			if($insertQ->rowCount() > 0){
				echo message('Mark Has Added Succesfully', true);
			}else{
				echo message('Mark Has Not Added Succesfully');
			}
		} catch (PDOException $e) {
			echo message('Unexpected Error Has Happened');
		}
	}else{

		// loop on errors
		foreach ($errors as $error) {
			echo message($error);
		}

	}

}

// add marks function
function updateMark () {
	global $conn;

	// update marks function
	$student_id = trim(htmlentities($_POST['id']));
	$mark = trim(htmlentities($_POST['mark']));
	$subject = trim(htmlentities($_POST['subject']));
	$errors = [];

	// check info
	if(!preg_match('/^[0-9]+$/', $student_id)){$errors[] = 'Error In Student Information';}
	if(!preg_match('/[0-9]{1,3}\/[0-9]{1,3}/', $mark)){$errors
	[] = 'Please Write The Mark Like The Examble Below';}

	// validate subject
	if(getAdmin()['position'] == 0){ // teacher
		$getSubject = $conn->prepare('SELECT
		subject
		FROM
		teachers
		WHERE
		id = ?');
		$getSubject->execute([$_SESSION['smart_school_id']]);
		if($getSubject->fetchColumn() != $subject){
			$errors[] = 'Please Choose Your Subject Only';
		}
	}

	// check on errors
	if(empty($errors)){ // check true

		$marks = explode('/', $mark);

		try {
			$updateQ = $conn->prepare('UPDATE
			marks
			SET
			mark = ?, full_mark = ?, subject = ?
			WHERE id = ?');
			$updateQ->execute([$marks[0], $marks[1], $subject, $student_id]);
			if($updateQ->rowCount() > 0){
				echo message('Mark Has Updated Succesfully', true);
			}else{
				echo message('Mark Has Not Updated Succesfully');
			}
		} catch (PDOException $e) {
			echo message('Unexpected Error Has Happened');
		}
	}else{

		// loop on errors
		foreach ($errors as $error) {
			echo message($error);
		}

	}

}

// get student info by id
function getStudent ($id) {
    global $conn;
    
    // get student by id
    $getStudent = $conn->prepare("SELECT
    name
    FROM
    students
    WHERE id = ?");
    $getStudent->execute([$id]);
    if($getStudent->rowCount() > 0){
        return $getStudent->fetchColumn();
    }else{
        return 'UNKNOWN';
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

// get mark function
function getMark ($id) {
	global $conn;
    
    if(is_numeric($id)){
        
        // get subject
        $getMark = $conn->prepare("SELECT
        mark, full_mark, subject, id
        FROM
        marks
        WHERE
        id = ?");
        $getMark->execute([$id]);
        if($getMark->rowCount() > 0){
            return $getMark->fetch();
        }else{
            return 0;
        }
        
    }else{
        return 0;
    }
}
?>

<div class="container mt-3">
	<div class="row">
		<div class="col-lg-3 col-md-4 col-12 mb-3 mb-md-0">
			<div class="bg-white rounded border page-links p-2">
				<ul class="list-unstyled pe-0 mb-0">
					<li <?php if($_GET['page'] === 'marks'){echo 'class="active"';}?>>
						<a href="<?php echo $_SERVER['PHP_SELF'] . '?page=marks'?>">Recent Added Marks</a>
					</li>
					<li <?php if($_GET['page'] === 'add_marks'){echo 'class="active"';}?>>
						<a href="<?php echo $_SERVER['PHP_SELF'] . '?page=add_marks'?>">Add Marks</a>
					</li>
				</ul>
			</div>
			<?php
			if($_GET['page'] === 'marks'){

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
			<div class="rounded border p-3 bg-white">
				<?php
				if($_GET['page'] === 'marks'){ // recent marks page

					// handling post requests
					if($_SERVER['REQUEST_METHOD'] === 'POST'){
						if(array_key_exists('update', $_POST)){
							updateMark();
						}else if(array_key_exists("delete_id", $_POST)){
							if(is_numeric($_POST['delete_id'])){
								$deleteStudent = $conn->prepare("DELETE
								FROM
								marks
								WHERE 
								id = ?");
								$deleteStudent->execute([$_POST['delete_id']]);
							}else{
							}
						}
					}

					// if isset edit page
					if(isset($_GET['edit'])){
						if(is_numeric($_GET['edit'])){

							$mark = getMark(trim(htmlentities($_GET['edit'])));
							if($mark !== 0){ // found
								
								?>
								<div class="full-page">
									<div class="container">
										<div class="row">
											<div class="col-lg-6 col-md-8 content col-12 mx-auto">
												<div class="bg-white rounded border p-3">
													<h6 class="main">Edit Mark</h6>
													<form action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&limit=' . $_GET['limit'] . '&order=' . $_GET['order'];?>" method="post" id="edit-mark">
														<input type="hidden" name="id" value="<?php echo $_GET['edit'];?>">
														<div class="row mb-3">
															<div class="col-md-6 col-12 mb-3 mb-md-0">
																<label for="mark">Mark</label>
																<input value="<?php echo $mark['mark'] . '/' . $mark['full_mark'];?>" type="text" placeholder="Separate The Grade And The Total Score By '/'" class="form-control" name="mark" id="mark">
																<small class="form-text mark">example: 90/100</small>
															</div>
															<?php
															$position = getAdmin()['position'];
															?>
															<div class="col-md-6 col-12 mb-3 mb-md-0">
																<label for="subject">Subject</label>
																<select name="subject" id="subject" class="form-select">
																	<?php
																	$subjects = getSubjects();
																	if($position == 0){ // teacher
																		if($subjects !== 0){ // found
																			
																			// loop on result
																			foreach($subjects as $subject){

																				if($subject['id'] == getAdmin()['subject']){
																					?>
																					<option
																					value="<?php echo $subject['id'];?>">
																						<?php echo $subject['subject'];?>
																					</option>
																					<?php
																				}

																			}

																		}
																	}else if($position == 1){ // admin
																		?>
																		<option value="NULL" selected disabled>Choose One</option>
																		<?php
																		// loop on result
																		foreach($subjects as $subject){

																			?>
																			<option
																				<?php if($subject['id'] == $mark['id']){echo 'selected';}?>
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
														</div>
														<div class="gap-2 d-grid">
															<button class="btn btn-success" name="update">Update</button>
														</div>
													</form>
													<script>
														const form = document.getElementById('edit-mark');
														form.onsubmit = function (e) {
															let mark = form.querySelector('input#mark'),
        													    subject = form.querySelector('select#subject');

																if(mark.value.match(/[0-9]{1,3}\/[0-9]{1,3}/g) === null){
																	e.preventDefault();
																	mark.focus();
																	mark.classList.add('input-alert');
																	form.querySelector('small.mark').classList.add('text-danger');
																}else{
																	mark.classList.remove('input-alert');
																	form.querySelector('small.mark').classList.remove('text-danger');

																	if(subject.value === 'NULL'){
																		e.preventDefault();
																		subject.focus();
																		subject.classList.add('input-alert');
																		form.querySelector('small.subject').textContent = 'Choose Subject';
																	}else{
																		subject.classList.remove('input-alert');
																		form.querySelector('small.subject').classList.remove('text-danger');
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
								<div class="alert alert-danger">Mark With This Id Is Not Found</div>
								<?php
							}

						}else{
							?>
							<div class="alert alert-danger">Please Check Your Link And Try Again</div>
							<?php
						}
					}

					?>
					<h6 class="main">Recent Added Marks</h6>
					<table class="table mb-0 table-striped text-center">
						<thead>
							<tr>
								<td>#ID</td>
								<td>Student</td>
								<td>Subject</td>
								<td>Mark</td>
								<td>Date</td>
								<td>Options</td>
							</tr>
						</thead>
						<tbody>
							<?php
							$marks = getMarks($_GET['limit'], $_GET['order']);
							if($marks !== 0){ // not empty result
								// loop on result
								foreach ($marks as $mark) {
									?>
									<tr>
										<td><?php echo $mark['id'];?></td>
										<td><?php echo getStudent($mark['student']);?></td>
										<td><?php echo getSubject($mark['subject']);?></td>
										<td><?php echo $mark['mark'] . '/' . $mark['full_mark'];?></td>
										<td><?php echo $mark['date'];?></td>
										<td>

											<a href="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&limit=' . $_GET['limit'] . '&order=' . $_GET['order'] . '&edit=' . $mark['id'];?>">
												<i class="fas me-1 fa-edit text-success" title="Edit"></i>
											</a>
											<i class="fas fa-trash text-danger delete-btn" data-id="<?php echo $mark['id'];?>" title="Delete"></i>
										</td>
									</tr>
									<?php
								}
								?>
								<script>
                                    const deleteBtns = document.querySelectorAll("i.delete-btn");
                                    for(let i = 0; i < deleteBtns.length; i++){
                                        deleteBtns[i].onclick = function () {

                                            if(confirm("Do You Want To Delete This Mark? ")){ // yes

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

							}else{
								?>
								<tr>
									<td colspan="6">
										<div class="mb-0 alert alert-info">There Is No Marks Added By You Yet</div>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
					<?php

				}else if($_GET['page'] === 'add_marks'){ // add marks page

					// handling post requests
					if($_SERVER['REQUEST_METHOD'] === 'POST'){
						addMarks();
					}

					?>
					<h6 class="main">Add Marks</h6>
					<form action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'];?>" method="post" class="mt-3" id="add-marks">
						<input type="hidden" name="student-id" id="student-id">
						<div class="row mb-3">
							<div class="col-12 position-relative">
								<label for="student">Student Name/Id</label>
								<input type="text" class="form-control" id="student" placeholder="Search Student By Name Or Id" name="student">
								<ul id="hints" style="display: none;" class="list-unstyled hints rounded border pt-0 mt-0 student-hints">
								</ul>
								<small class="err-msg student"></small>
							</div>
						</div>
						<div class="row mb-3">
							<div class="col-md-6 col-12 mb-3 mb-md-0">
								<label for="mark">Mark</label>
								<input type="text" placeholder="Separate The Grade And The Total Score By '/'" class="form-control" name="mark" id="mark">
								<small class="form-text mark">example: 90/100</small>
							</div>
							<?php
								$position  = getAdmin()['position'];
							?>
							<div class="col-md-6 col-12 mb-3 mb-md-0">
								<label for="subject">Subject</label>
								<select name="subject" id="subject" class="form-select">
									<?php
									$subjects = getSubjects();
									if($position == 0){ // teacher
										if($subjects !== 0){ // found
											
											// loop on result
											foreach($subjects as $subject){

												if($subject['id'] == getAdmin()['subject']){
													?>
													<option
													value="<?php echo $subject['id'];?>">
														<?php echo $subject['subject'];?>
													</option>
													<?php
												}

											}

										}
									}else if($position == 1){ // admin
										?>
										<option value="NULL" selected disabled>Choose One</option>
										<?php
										// loop on result
										foreach($subjects as $subject){

											?>
											<option
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
						</div>
						<div class="gap-2 d-grid">
							<button class="btn btn-success">Add</button>
						</div>
					</form>
					<script src="js/marks.js"></script>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>

<?php include 'templates/_footer.php';?>