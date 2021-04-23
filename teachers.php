<?php
$pageName = 'teachers';
require 'includes/init.php';

// get teachers function
function getTeachers () {
    global $conn;

    // get teachers
    $getTeachers = $conn->prepare("SELECT
        image, name, subject, fb_link, phone, gender
        FROM
        teachers
        WHERE visibility = 1
        ORDER BY id DESC");
    $getTeachers->execute();
    if($getTeachers->rowCount() > 0){
        return $getTeachers->fetchAll();
    }else{
        return 0;
    }
}

// get subject name function
function getSubName ($subject) {
    global $conn;

    // get subject name
    $getSub = $conn->prepare("SELECT
        subject
        FROM
        subjects
        WHERE
        id = ?");
    $getSub->execute([$subject]);
    if($getSub->rowCount() > 0){ // found
        return $getSub->fetchColumn();
    }else{
        return 'UNKNOWN';
    }
}
?>
  
<div class="all-title-box">
    <div class="container text-center">
        <h1>Teachers<span class="m_1">Pride In Excellence</span></h1>
    </div>
</div>
<div id="teachers" class="section wb">
    <div class="container">
        <div class="row">
            <?php
            $teachers = getTeachers();
            if($teachers !== 0){ // not empty result

                // loop on result
                foreach ($teachers as $teacher) {
                    if(empty($teacher['image'])){
                        if($teacher['gender'] == 0){ // male
                            $image = 'images/team-02.png';
                        }else if($teacher['gender'] == 1){ // female
                            $image = 'images/team-01.png';
                        }
                    }else{
                        $image = 'images/teachers/' . $teacher['image'];
                    }
                    ?>
                    <div class="col-lg-3 col-md-6 col-12 mb-3">
                        <div class="our-team">
                            <div class="team-img">
                                <div style="max-height: 230px;" class="overflow-hidden">
                                    <img src="<?php echo $image;?>" style="width: 100%;" alt="<?php echo $teacher['name'];?>">
                                </div>
                                <div class="social">
                                    <ul>
                                        <li>
                                            <a href="<?php echo $teacher['fb_link'];?>" class="fa fa-facebook"></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="team-content">
                                <h3 class="title text-capitalize"><?php echo $teacher['name'];?></h3>
                                <span class="post"><?php echo getSubName($teacher['subject']);?></span>
                                <span class="post">Phone: <?php echo $teacher['phone'];?></span>
                            </div>
                        </div>
                    </div>
                    <?php
                }

            }else{ // empty result
                // code gone here
            }
            ?>
        </div><!-- end row -->
    </div><!-- end container -->
</div><!-- end section -->	

<?php include 'templates/_footer.php';?>