<?php
$pageName = 'contact';
require 'includes/init.php';

// function to recieve contacting message
function contactRequest () {
    global $conn;
    
    $fName      = trim(htmlentities($_POST['first_name']));
    $lName      = trim(htmlentities($_POST['last_name']));
    $email      = trim(htmlentities($_POST['email']));
    $phone      = trim(htmlentities($_POST['phone']));
    $comment    = trim(htmlentities($_POST['comment']));
    $errors     = []; // errors container
    
    // check inputs
    if(!preg_match("/^[a-zA-Z]{2,}$/", $fName)){$errors[] = 'please write your first name correctly';}
    if(!preg_match("/^[a-zA-Z]{2,}$/", $lName)){$errors[] = 'please write your last name correctly';}
    if(!preg_match("/^\S+@\S+\.\S+$/", $email)){$errors[] = 'please write correct email';}
    if(!preg_match("/^[0-9]{4,}$/", $phone)){$errors[] = 'please write correct phone number';}
    if(empty($comment)){$errors[] = 'comment must contain 5 charachters at least';}
    
    if(empty($errors)){ // valid inputs
        
        try{
            $insertStmt = $conn->prepare("INSERT INTO
            contact
            (f_name, l_name, email, phone, comment, date)
            VALUES
            (?, ?, ?, ?, ?, NOW())");
            $insertStmt->execute([$fName, $lName, $email, $phone, $comment]);
            if($insertStmt->rowCount() > 0){ // success
                echo message("your comment has sent successfully", true);
            }else{ // error
                echo message("your message hasn't sent successfully");
            }
        }
        catch(PDOException $e){
            echo message("message has not sent succesfully, it might be dublicated");
        }
        
    }else{ // invalid inputs
        foreach($errors as $error){
            echo message(ucfirst($error));
        }
    }
    
}
if($_SERVER['REQUEST_METHOD'] === 'POST'){contactRequest();}
?>

<div class="all-title-box">
    <div class="container text-center">
        <h1 >Contact<span class="m_1">we here to serve you </span></h1>
    </div>
</div>
<div id="contact" class="section wb">
    <div class="container">
        <div class="section-title text-center">
            <h3>Need Help? Sure we are Online!</h3>
            <p class="lead">Let us give you more details about the School. Please fill out the form below. </p>
        </div><!-- end title -->
        <div class="contact_form">
            <div id="message"></div>
            <form id="contact-form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                <div class="row row-fluid">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" autofocus>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name">
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Your Email">
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Your Phone">
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <textarea class="form-control" name="comment" id="comment" rows="6" placeholder="Give us more details.."></textarea>
                    </div>
                    <div class="text-center pd">
                        <button type="submit" id="submit" class="btn btn-light btn-radius btn-brd grd1 btn-block">Send</button>
                    </div>
                </div>
            </form>
        </div>
    </div><!-- end col -->
</div><!-- end section -->

<?php include 'templates/_footer.php';?>