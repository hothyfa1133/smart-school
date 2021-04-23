<?php
$forAdmin = true;
$pageName = 'news';
require 'includes/init.php';

// redirect if wrong page
$allowed_links = ['news', 'add_news'];
if(!in_array($_GET['page'], $allowed_links)){header("location:" . $_SERVER['PHP_SELF'] . '?page=news');}

// get news function
function getNews ($limit = 10, $order = 'DESC') {
	global $conn;

	// getting news
	$getNews = $conn->prepare("SELECT
		id, image, title, description, date
		FROM news
		ORDER BY id $order
		LIMIT $limit");
	$getNews->execute();
	if($getNews->rowCount() > 0){
		return $getNews->fetchAll();
	}else{
		return 0;
	}
}

// add news request
function addNews () {
	global $conn;

	// validate inputs
	$title 			= trim(htmlentities($_POST['title']));
	$description 	= trim(htmlentities($_POST['description']));
	$image 			= $_FILES['image'];
	$errors 		= [];

	if(!preg_match("/^(.){4,}$/", $title)){$errors[] = 'Title Must Contain 4 Charachters At Least';}
	if(strlen($description) < 10){$errors[] = 'Description Must Contain 10 Charachters At Least';}
	if(empty($image['name'])){
		$errors[] = 'You Must Upload Image';
	}else{ // image not empty

		// validate image

		// extension
        @$extension = strtolower(end(explode(".", $image['name'])));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if(!in_array($extension, $allowed_extensions)){ // error in extension
            $errors[] = 'Images Allowed Extinsions Is <br>' . implode(", ", $allowed_extensions) . ' Only';
        }

		// size
		$maxSize = 5;
		if($image['size'] / 1048576 > $maxSize){$errors[] = 'Max Image Size Is ' . $maxSize . ' Megabytes';}

		// errors
		if($image['error'] > 0){$errors[] = 'Error During Uploading Image';}

	}


	if(empty($errors)){ // check true
		
		// upload image
		try {
			$imageName = rand(1000, 80000) . '_' . $image['name'];
			move_uploaded_file($image['tmp_name'], "../images/news/" . $imageName);
		} catch (Exception $e) {
			echo message("Error During Moving The Image");
		}

		try {
			$insertStmt = $conn->prepare("INSERT
				INTo
				news
				(image, title, description, date)
				VALUES
				(?, ?, ?, NOW())");
			$insertStmt->execute([$imageName, $title, $description]);
			if($insertStmt->rowCount() > 0){
				echo message("Item Has Added Succesfully", true);
			}else{
				echo message("Item Has Not Added Succesfully");
			}
		} catch (PDOException $e) {
			echo message("Unexpected Error, This Content Might Be Dublicated");
		}

	}else{ // check false
		
		// loop on errors
		foreach ($errors as $error) {
			echo message($error);
		}

	}

}

// get news by id function
function getItem ($id) {
	global $conn;

	// get item by id
	$getItem = $conn->prepare("SELECT
		title, description, date, image
		FROM news
		WHERE
		id = ?");
	$getItem->execute([$id]);
	if($getItem->rowCount() > 0){ // check true
		return $getItem->fetch();
	}else{ // check false
		return 0;
	}
}

// update item function
function updateItem ($id) {
	global $conn;

	// validate inputs
	$title 			= trim(htmlentities($_POST['title']));
	$description 	= trim(htmlentities($_POST['description']));
	$image 			= $_FILES['image'];
	$errors 		= [];

	if(!preg_match("/^(.){4,}$/", $title)){$errors[] = 'Title Must Contain 4 Charachters At Least';}
	if(strlen($description) < 10){$errors[] = 'Description Must Contain 10 Charachters At Least';}

	if($image['name'] !== ""){ // has update image
        
        // extension
        @$extension = strtolower(end(explode(".", $image['name'])));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if(!in_array($extension, $allowed_extensions)){ // error in extension
            $errors[] = 'Images Allowed Extinsions Is <br>' . implode(", ", $allowed_extensions) . ' Only';
        }
        
        // size
        $maxSize = 5;
        if($image['size'] / 1048576 > $maxSize){$errors[] = 'Max Image Size Is ' . $maxSize . ' Megabytes';}
        
        // errors
        if($image['error'] > 0){$errors[] = 'Error During Uploading Image';}
        
    }


	if(empty($errors)){ // check true

		// upload image
        try{
            if($image['name'] === ""){ // empty
                $imageName = $_POST['old_image'];
            }else{
                
                if(file_exists("../images/news/" . $_POST['old_image'])){
                    unlink("../images/news/" . $_POST['old_image']);
                }
                
                $imageName = rand(1000, 80000) . '_' . $image['name'];
                move_uploaded_file($image['tmp_name'], "../images/news/" . $imageName);
                
            }
        }
        catch(Exception $e){
            echo message("Error During Uploading Image");
        }

		try {
			$insertStmt = $conn->prepare("UPDATE
				news
				SET
				image = ?,
				title = ?,
				description = ?
				WHERE
				id = ?
			");
			$insertStmt->execute([$imageName, $title, $description, $id]);
			if($insertStmt->rowCount() > 0){
				echo message("Item Has Updated Succesfully", true);
			}else{
				echo message("Item Has Not Updated Succesfully");
			}
		} catch (PDOException $e) {
			echo message("Unexpected Error Has Happened");
		}

	}else{ // check false
		
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
					<li <?php if($_GET['page'] === 'news'){echo 'class="active"';}?>>
						<a href="<?php echo $_SERVER['PHP_SELF'] . '?page=news'?>">Recent News</a>
					</li>
					<li <?php if($_GET['page'] === 'add_news'){echo 'class="active"';}?>>
						<a href="<?php echo $_SERVER['PHP_SELF'] . '?page=add_news'?>">Add News</a>
					</li>
				</ul>
			</div>
			<?php
			if($_GET['page'] === 'news'){

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
			<div class="bg-white rounded border p-3">
				<?php
				if($_GET['page'] === 'news'){ // recent news page

					// handling post requests
					if($_SERVER['REQUEST_METHOD'] === 'POST'){
						if(array_key_exists("edit_item", $_POST)){ // update item request
							updateItem($_POST['id']);
						}else if(array_key_exists("delete_id", $_POST)){ // delete item api

							// for ajax

							// get image and delete it
							$getImage = $conn->prepare("SELECT
								image
								FROM
								news
								WHERE
								id = ?");
							$getImage->execute([$_POST['delete_id']]);
							$d_image = $getImage->fetchColumn();

							if(file_exists("../images/news/" . $d_image)){
								unlink("../images/news/" . $d_image);
							}

							// delete item
							$deleteStmt = $conn->prepare("DELETE
								FROM
								news
								WHERE
								id = ?");
							$deleteStmt->execute([$_POST['delete_id']]);

						}
					}

					// edit news page
					if(isset($_GET['edit'])){ // edit news page
						if(is_numeric($_GET['edit'])){
							$item = getItem($_GET['edit']);
							if($item !== 0){

								?>
								<div class="full-page">
									<div class="container">
										<div class="row">
											<div class="col-lg-6 col-md-8 col-12 content mx-md-auto mx-0">
												<div class="bg-white rounded border p-3">
													<h6 class="main">Edit Item</h6>
													<form action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&limit=' . $_GET['limit'] . '&order=' . $_GET['order'];?>" method="post" id="edit-item" enctype="multipart/form-data">
														<input type="hidden" name="old_image" value="<?php echo $item['image'];?>">
														<input type="hidden" name="id" value="<?php echo $_GET['edit'];?>">
														<div class="mt-3">
															<label for="title">Title</label>
															<input type="text" name="title" autofocus placeholder="Title" class="form-control" id="title" value="<?php echo $item['title'];?>">
															<small class="err-msg title"></small>
														</div>
														<div class="mt-3">
															<label for="description">Description</label>
															<textarea name="description" placeholder="Description" id="description" cols="30" rows="5" class="form-control"><?php echo nl2br($item['description']);?></textarea>
															<small class="err-msg description"></small>
														</div>
														<div class="mt-3">
															<label for="image">Upload Image</label>
															<input class="form-control" type="file" id="image" name="image">
															<small class="err-msg image"></small>
														</div>
														<div class="mt-3">
															<div class="gap-2 d-grid">
																<button class="btn btn-success" name="edit_item">Add</button>
															</div>
														</div>
													</form>
													<script>
														const form = document.getElementById("edit-item");
														form.onsubmit = function (e) {

															// validate inputs
															let title 			= form.querySelector("input#title"),
																description 	= form.querySelector("textarea#description");

															if(title.value.match(/^(.){4,}$/g) === null){
																e.preventDefault();
																title.classList.add("input-alert");
																form.querySelector("small.title").textContent = 'Title Must Contain 4 Charachters At Least';
																title.focus();
															}else{
																title.classList.remove("input-alert");
																form.querySelector("small.title").textContent = '';

																if(description.value.length < 10){
																	e.preventDefault();
																	description.classList.add("input-alert");
																	form.querySelector("small.description").textContent = 'Description Must Contain 10 Charachters At Least';
																	description.focus();
																}else{
																	description.classList.remove("input-alert");
																	form.querySelector("small.description").textContent = '';
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
								<div class="alert alert-danger">Item With This Id Is Not Found</div>
								<?php
							}
						}else{
							?>
							<div class="alert alert-danger">Please Check Link And Try Again, Error In Id</div>
							<?php
						}
					}

					?>
					<h6 class="main mb-0">Recent News</h6>
					<div class="row">
						<?php
						$news = getNews($_GET['limit'], $_GET['order']);
						if($news !== 0){ // not empty result

							// loop in result
							foreach ($news as $item) {
								?>
								<div class="col-lg-4 col-sm-6 col-12 mt-3">
									<div class="bg-light rounded border p-2">
										<div class="news-image overflow-hidden">
											<img src="<?php echo '../images/news/' . $item['image'];?>" alt="<?php echo $item['title'] . ' Image';?>" style="width: 100%;" class="mb-3">
											<h6 class="fw-bold"><?php echo $item['title'];?></h6>
											<p class="mb-0">
												<small><?php echo nl2br($item['description']);?></small>
											</p>
											<div class="item-info overflow-hidden">
												<small class="form-text float-start">
													<small><?php echo $item['date'];?></small>
												</small>
												<small class="float-end pt-1">
													<i class="fas fa-trash text-danger delete-item" data-id="<?php echo $item['id'];?>" title="Delete"></i>
													<a href="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&limit=' . $_GET['limit'] . '&order=' . $_GET['order'] . '&edit=' . $item['id'];?>">
														<i class="fas fa-edit text-success" title="Edit"></i>
													</a>
												</small>
											</div>
										</div>
									</div>
								</div>
								<?php
							}

						}else{
							?>
							<div class="col-12">
								<div class="alert alert-info my-2 mb-0">There Is No News Yet</div>
							</div>
							<?php
						}
						?>
					</div>
					<script>
						// ajax
						const btns = document.querySelectorAll("i.delete-item");
						for (let i = 0; i < btns.length; i++) {
							btns[i].onclick = function () {
								var deleteObj = new XMLHttpRequest();
								deleteObj.open("POST", "<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'] . '&limit=' . $_GET['limit'] . '&order=' . $_GET['order'];?>");
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

				}else if($_GET['page'] === 'add_news'){ // add new news page

					// handling post requests
					if($_SERVER['REQUEST_METHOD'] === 'POST'){
						if(array_key_exists("add_news", $_POST)){ // add news request
							addNews();
						}
					}

					?>
					<h6 class="main">Add News</h6>
					<form action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . $_GET['page'];?>" method="post" id="add-news" enctype="multipart/form-data">
						<div class="mt-3">
							<label for="title">Title</label>
							<input type="text" name="title" autofocus placeholder="Title" class="form-control" id="title">
							<small class="err-msg title"></small>
						</div>
						<div class="mt-3">
							<label for="description">Description</label>
							<textarea name="description" placeholder="Description" id="description" cols="30" rows="5" class="form-control"></textarea>
							<small class="err-msg description"></small>
						</div>
						<div class="mt-3">
							<label for="image">Upload Image</label>
							<input class="form-control" type="file" id="image" name="image">
							<small class="err-msg image"></small>
						</div>
						<div class="mt-3">
							<div class="gap-2 d-grid">
								<button class="btn btn-success" name="add_news">Add</button>
							</div>
						</div>
					</form>
					<script>
						const form = document.getElementById("add-news");
						form.onsubmit = function (e) {

							// validate inputs
							let title 			= form.querySelector("input#title"),
								description 	= form.querySelector("textarea#description"),
								image 			= form.querySelector("input#image");

							if(title.value.match(/^(.){4,}$/g) === null){
								e.preventDefault();
								title.classList.add("input-alert");
								form.querySelector("small.title").textContent = 'Title Must Contain 4 Charachters At Least';
								title.focus();
							}else{
								title.classList.remove("input-alert");
								form.querySelector("small.title").textContent = '';

								if(description.value.length < 10){
									e.preventDefault();
									description.classList.add("input-alert");
									form.querySelector("small.description").textContent = 'Description Must Contain 10 Charachters At Least';
									description.focus();
								}else{
									description.classList.remove("input-alert");
									form.querySelector("small.description").textContent = '';

									if(image.value === ""){
										e.preventDefault();
										image.classList.add("input-alert");
										form.querySelector("small.image").textContent = 'You Must Upload Image';
										image.focus();
									}else{
										image.classList.remove("input-alert");
										form.querySelector("small.image").textContent = '';
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