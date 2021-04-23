<?php
$forAdmin = true;
$pageName = 'subjects';
require 'includes/init.php';

// redirect if wrong page
$allowed_links = ['subjects', 'add_subject'];
if(!in_array($_GET['page'], $allowed_links)){header('location:' . $_SERVER['PHP_SELF'] . '?page=subjects');}

// function to get subjects
function getSubjects () {
	global $conn;

	// getting subjects
	$getSubjects = $conn->prepare("SELECT
		id, subject
		FROM
		subjects
		ORDER BY id ASC");
	$getSubjects->execute();
	if($getSubjects->rowCount() > 0){ // found
		return $getSubjects->fetchAll();
	}else{
		return 0;
	}
}

// function to get teachers numbers of subject
function teNumber ($subject) {
	global $conn;

	// get teachers number of the subject
	$getCount = $conn->prepare("SELECT
		COUNT(id)
		FROM
		teachers
		WHERE
		subject = ?");
	$getCount->execute([$subject]);
	return $getCount->fetchColumn();
}

// function to add subject
function addSubject () {
	global $conn;

	// validating
	$name = ucfirst(trim(htmlentities($_POST['name'])));
	if(preg_match('/^([a-zA-Z ]){2,}$/', $name)){

		// inserting
		try {
			$insertStmt = $conn->prepare('INSERT
				INTO
				subjects(subject)
				VALUES(?)');
			$insertStmt->execute([$name]);
			if($insertStmt->rowCount() > 0){
				echo message('Subject Has Added Succesfully', true);
			}else{
				echo message('Subject Has Not Added Succesfully');
			}
		} catch (PDOException $e) {
			echo message('Unexpected Error Has Happened');
		}

	}
}

// function to get subject by id
function getSubject ($id) {
	global $conn;

	// get subject by id
	$getSubject = $conn->prepare('SELECT
		subject
		FROM
		subjects
		WHERE
		id = ?');
	$getSubject->execute([$id]);
	if($getSubject->rowCount() > 0){
		return $getSubject->fetchColumn();
	}else{
		return 0;
	}
}

// function to update subject
function updateSubject () {
	global $conn;

	// validating
	$name = ucfirst(trim(htmlentities($_POST['name'])));
	if(preg_match('/^([a-zA-Z ]){2,}$/', $name)){

		// updating
		try {
			$updateStmt = $conn->prepare('UPDATE
				subjects
				SET
				subject = ?
				WHERE id = ?');
			$updateStmt->execute([$name, $_POST['id']]);
			if($updateStmt->rowCount() > 0){
				echo message('Subject Has Updated Succesfully', true);
			}else{
				echo message('Subject Has Not Updated Succesfully');
			}
		} catch (PDOException $e) {
			echo message('Unexpected Error Has Happened');
		}

	}
}


?>

<div class="container mt-3">
	<div class="row">
		<div class="col-lg-3 col-md-4 col-12 mb-3 mb-md-0">
			<div class="bg-white rounded border page-links p-2">
				<ul class="list-unstyled pe-0 mb-0">
					<li <?php if($_GET['page'] === 'subjects'){echo 'class="active"';}?>>
						<a href="<?php echo $_SERVER['PHP_SELF'] . '?page=subjects';?>">All Subjects</a>
					</li>
					<li <?php if($_GET['page'] === 'add_subject'){echo 'class="active"';}?>>
						<a href="<?php echo $_SERVER['PHP_SELF'] . '?page=add_subject';?>">Add Subject</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="col-lg-9 col-md-8 col-12">
			<div class="bg-white rounded border p-3">
				<?php
				if ($_GET['page'] === 'subjects') { // subjects page

					// handling post requests
					if($_SERVER['REQUEST_METHOD'] === 'POST'){
						if(array_key_exists("edit_subject", $_POST)){
							updateSubject();
						}else if(array_key_exists("delete_id", $_POST)){
							$deleteStmt = $conn->prepare("DELETE
                            FROM
                            subjects
                            WHERE
                            id = ?");
                            $deleteStmt->execute([$_POST['delete_id']]);
						}
					}

					if(isset($_GET['edit'])){
						if(is_numeric($_GET['edit']) && getSubject($_GET['edit']) !== 0){

							?>
							<div class="full-page">
								<div class="container">
									<div class="row">
										<div class="content col-lg-6 col-md-8 col-12 mx-auto">
											<div class="bg-white rounded border p-3">
												<h6 class="main">Edit Subject</h6>
												<form action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'];?>" method="post" id="edit-course">
													<input type="hidden" name="id" value="<?php echo $_GET['edit'];?>">
													<div class="mt-3">
														<label for="name">Subject Name</label>
														<input value="<?php echo getSubject($_GET['edit']);?>" type="text" class="form-control" id="name" name="name" autofocus placeholder="Subject Name">
														<small class="err-msg name"></small>
													</div>
													<div class="mt-3 gap-2 d-grid">
														<button class="btn btn-success" name="edit_subject">Update</button>
													</div>
												</form>
												<script>
													const form = document.getElementById('edit-course');
													form.onsubmit = function (e) {
														let name = form.querySelector('input#name');
														if(name.value.match(/^([a-zA-Z ]{2,})$/g) === null){
															e.preventDefault();
															name.focus();
															name.classList.add('input-alert');
															form.querySelector('small.name').textContent = 'Subject Nae Must Contain 2 Charachters At Least';
														}
													}
												</script>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php

						}else{
							?>
							<div class="alert alert-danger">Error In Subject ID</div>
							<?php
						}
					}

					?>
					<h6 class="main">All Subjects</h6>
					<table class="table table-striped text-center mb-0">
						<thead>
							<tr>
								<td>#ID</td>
								<td>Sub Name</td>
								<td>Sub Teachers Count</td>
								<td>Options</td>
							</tr>
						</thead>
						<tbody>
							<?php
							$subjects = getSubjects();
							if($subjects !== 0){ // not empty result

								// loop on result
								foreach ($subjects as $subject) {
									?>
									<tr>
										<td><?php echo $subject['id'];?></td>
										<td><?php echo $subject['subject'];?></td>
										<td><?php echo teNumber($subject['id']);?></td>
										<td>
											<a href="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&edit=' . $subject['id'];?>">
												<i class="fas fa-edit text-success" title="Edit"></i>
											</a>
											<i
											data-id="<?php echo $subject['id'];?>"
											class="delete-btns fas fa-trash text-danger ms-1"
											title="Delete"></i>
										</td>
									</tr>
									<?php
								}

								?>
								<script>
                                    const btns = document.querySelectorAll("i.delete-btns");

                                    for (let i = 0; i < btns.length; i++) {
                                        btns[i].onclick = function () {
                                            var deleteObj = new XMLHttpRequest();
                                            deleteObj.open("POST", "<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'];?>");
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

							}else{
								?>
								<tr>
									<td colspan="4">
										<div class="mb-0 alert alert-warning">There Is Now Subjects</div>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
					<?php
				}else if($_GET['page'] === 'add_subject'){

					// handling post requests
					if($_SERVER['REQUEST_METHOD'] === 'POST'){
						if(array_key_exists("add_subject", $_POST)){
							addSubject();
						}
					}

					?>
					<h6 class="main">Add Subject</h6>
					<form action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'];?>" method="post" id="add-course">
						<div class="mt-3">
							<label for="name">Subject Name</label>
							<input type="text" class="form-control" id="name" name="name" autofocus placeholder="Subject Name">
							<small class="err-msg name"></small>
						</div>
						<div class="mt-3 gap-2 d-grid">
							<button class="btn btn-success" name="add_subject">Add</button>
						</div>
					</form>
					<script>
						const form = document.getElementById('add-course');
						form.onsubmit = function (e) {
							let name = form.querySelector('input#name');
							if(name.value.match(/^([a-zA-Z ]{2,})$/g) === null){
								e.preventDefault();
								name.focus();
								name.classList.add('input-alert');
								form.querySelector('small.name').textContent = 'Subject Nae Must Contain 2 Charachters At Least';
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