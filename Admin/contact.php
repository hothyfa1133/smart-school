<?php
$forAdmin = true;
$pageName = 'contact';
require 'includes/init.php';

// redirect if wrong page
$allowed_ordering = ['DESC', 'ASC'];
if(empty($_GET['limit']) || !is_numeric($_GET['limit']) || !in_array($_GET['order'], $allowed_ordering)){header("location:" . $_SERVER['PHP_SELF'] . "?limit=10&order=DESC");}

// get messages from db
function getMessages ($order = 10, $limit = 'DESC') {
    global $conn;
    
    // getting messages
    $getMessages = $conn->prepare("SELECT
    id, f_name, l_name, email, phone, comment, date
    FROM contact
    ORDER BY date $order
    LIMIT $limit");
    $getMessages->execute();
    if($getMessages->rowCount() > 0){
        return $getMessages->fetchAll();
    }else{
        return 0;
    }
}

// handling post requests
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(array_key_exists("delete_id", $_POST)){
        if(is_numeric($_POST['delete_id'])){
            
            $deleteStmt = $conn->prepare("DELETE
            FROM
            contact
            WHERE
            id = ?");
            $deleteStmt->execute([$_POST['delete_id']]);
            echo $deleteStmt->rowCount();
            
        }else{
            echo 01;
        }
    }
}
?>

<div class="container mt-3">
    <div class="row">
        <div class="col-12">
            <div class="rounded bg-white border p-3 mb-3">
                <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-12 mb-3 mb-lg-0 mx-md-auto mx-lg-0">
                            <label for="limit">Limit</label>
                            <input type="number" name="limit" placeholder="Limit" id="limit" class="form-control" value="<?php echo $_GET['limit'];?>">
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 mb-3 mb-lg-0 mx-md-auto mx-lg-0">
                            <label for="order">Order</label>
                            <select name="order" id="order" class="form-select">
                                <option value="NULL" disabled>Choose One</option>
                                <option <?php if($_GET['order'] === 'DESC'){echo 'selected';}?> value="DESC">DESC</option>
                                <option <?php if($_GET['order'] === 'ASC'){echo 'selected';}?> value="ASC">ASC</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 mb-3 mb-lg-0 mx-md-auto mx-lg-0">
                            <div class="gap-2 d-grid">
                                <button class="btn btn-success position-relative" style="top: 23px;">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="rounded bg-white border p-3">
                <h6 class="main">contacting messages</h6>
                <div class="row">
                   <?php
                    $messages = getMessages($_GET['order'], $_GET['limit']);
                    if($messages !== 0){ // !empty result
                        
                        // loop on result
                        foreach($messages as $message){
                            ?>
                            <div class="col-md-4 col-sm-6 col-12 mt-3">
                                <div class="contacting-card bg-light p-2 rounded border position-relative">
                                    <?php echo $message['comment'];?>
                                    <div class="icons-container">
                                        <i class="fas fa-trash delete" data-id="<?php echo $message['id'];?>" title="Delete"></i>
                                    </div>
                                    <div class="rounded bg-white border p-2 position-absolute info-panel">
                                        <p class="mb-1">Name: <?php echo $message['f_name'] . ' ' . $message['l_name'];?></p>
                                        <p class="mb-1">Email: <?php echo $message['email'];?></p>
                                        <p class="mb-1">Phone: <?php echo $message['phone'];?></p>
                                        <p class="mb-1">Date: <?php echo $message['date'];?></p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        
                        ?>
                        <script>
                            const deleteBtns = document.querySelectorAll(".contacting-card .icons-container i.delete");

                            for(let i = 0; i < deleteBtns.length; i++){
                                deleteBtns[i].onclick = function () {
                                    var deleteOb = new XMLHttpRequest();
                                    deleteOb.open("POST", "<?php echo $_SERVER['PHP_SELF'];?>");
                                    deleteOb.onload = function () {
                                        if(this.readyState === 4 && this.status === 200){
                                            if (this.responseText == 01){
                                                alert("Error With Message Id");
                                            }else{
                                                location.reload();
                                            }
                                        }else{
                                            alert("Unexpected Error Has Happened");
                                        }
                                    }
                                    deleteOb.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                    deleteOb.send("delete_id=" + this.dataset.id);
                                }
                            }
                        </script>
                        <?php
                        
                    }else{ // empty result
                        ?>
                        <div class="col-12">
                            <div class="alert alert-info my-2">There Is No Messages Yet</div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/_footer.php';?>