<?php
$forAdmin = true;
$pageName = 'activation';
require 'includes/init.php';

// get unactivate students
function getUnactivate () {
    global $conn;

    // get unavtive students
    $getStudents = $conn->prepare('SELECT
    id, name, email, grade, student_id
    FROM
    students
    WHERE
    status = 0
    ORDER BY id DESC');
    $getStudents->execute();
    if($getStudents->rowCount() > 0){
        return $getStudents->fetchAll();
    }else{
        return 0;
    }
}

// activate function
function activateStudent () {
    global $conn;

    // activate student
    $activate = $conn->prepare('UPDATE
    students
    SET
    status = 1
    WHERE
    id = ?');
    $activate->execute([$_POST['id']]);

    if($activate->rowCount() > 0){
        echo message('Student Has Activated Succesfully', true);
    }else{
        echo message('Student Has Not Activated Succesfully');
    }
}

// delete function
function deleteStudent () {
    global $conn;

    // delete student
    $deleteStudent = $conn->prepare('DELETE
    FROM
    students
    WHERE
    id = ?');
    $deleteStudent->execute([$_POST['id']]);

    if($deleteStudent->rowCount() > 0){
        echo message('Student Has Deleted Succesfully', true);
    }else{
        echo message('Student Has Not Deleted Succesfully');
    }
}

// handling post requests
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(array_key_exists('action', $_POST) && array_key_exists('id', $_POST)){
        if(is_numeric($_POST['id'])){
            if($_POST['action'] === 'delete'){
                deleteStudent();
            }else if($_POST['action'] === 'approve'){
                activateStudent();
            }
        }
    }
}
?>
<div class="container mt-3">
    <div class="border p-3 rounded bg-white">
        <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" id="actions-form">
            <input type="hidden" name="action" id="action">
            <input type="hidden" name="id" id="id">
        </form>
        <table class="table table-striped text-center mb-0">
            <thead>
                <tr>
                    <td>#ID</td>
                    <td>Name</td>
                    <td>Email</td>
                    <td>Grade</td>
                    <td>Student Id</td>
                    <td>Options</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $students = getUnactivate();
                if($students !== 0){ // not empty result
                    foreach ($students as $student) {
                        ?>
                        <tr>
                            <td><?php echo $student['id'];?></td>
                            <td><?php echo $student['name'];?></td>
                            <td><?php echo $student['email'];?></td>
                            <td><?php echo $student['grade'];?></td>
                            <td><?php echo $student['student_id'];?></td>
                            <td>
                                <i data-id="<?php echo $student['id'];?>" data-action="delete" class="delete-btns fas fa-trash text-danger" title="Delete"></i>
                                <i data-id="<?php echo $student['id'];?>" data-action="approve" class="approve-btns fas fa-check text-info ms-2" style="cursor: pointer;" title="Approve"></i>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
        <script>
            const form = document.getElementById('actions-form'),
                  deleteBtns = document.querySelectorAll('i.delete-btns');
                  approveBtns = document.querySelectorAll('i.approve-btns');

            for(let i = 0; i < deleteBtns.length; i++){
                deleteBtns[i].onclick = function () {
                    let action = form.querySelector('input#action'),
                        id = form.querySelector('input#id');

                    action.value = deleteBtns[i].dataset.action;
                    id.value = deleteBtns[i].dataset.id;

                    form.submit();
                }
            }

            for(let i = 0; i < approveBtns.length; i++){
                approveBtns[i].onclick = function () {
                    let action = form.querySelector('input#action'),
                        id = form.querySelector('input#id');

                    action.value = approveBtns[i].dataset.action;
                    id.value = approveBtns[i].dataset.id;

                    form.submit();
                }
            }
        </script>
    </div>
</div>
<?php include 'templates/_footer.php';?>