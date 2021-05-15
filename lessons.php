<?php
$pageName = 'courses';
$login = true;
require 'includes/init.php';

if (!isset($_GET['course'])) {
    header('location:courses.php');
}

// function to check avilability of the course
function getCourse()
{
    global $conn;

    // check course
    $getCourse = $conn->prepare("SELECT
    id, title
    FROM
    courses
    WHERE
    id = ?");
    $getCourse->execute([$_GET['course']]);
    if ($getCourse->rowCount() > 0) {
        return $getCourse->fetch();
    } else {
        return 0;
    }
}

if (getCourse() == 0) { // not found
    header('location:courses.php');
}

// function to get videos
function getVideos()
{
    global $conn;

    // get videos function
    $getVideos = $conn->prepare("SELECT
    id, link, title, description
    FROM courses_videos
    WHERE course = ?
    ORDER BY id ASC");
    $getVideos->execute([$_GET['course']]);
    if ($getVideos->rowCount() > 0) {
        return $getVideos->fetchAll();
    } else {
        return 0;
    }
}

// function to get video
function getVideo($video)
{
    global $conn;

    // get video
    $getVideo = $conn->prepare("SELECT
    title, description, id
    FROM courses_videos
    WHERE link = ?");
    $getVideo->execute([
        'https://www.youtube.com/embed/' . $video
    ]);

    if($getVideo->rowCount() > 0){
        return $getVideo->fetch();
    }else{
        return 0;
    }
}

$videos = getVideos();

$video = getVideo($_GET['video']);

if ($videos !== 0) { // found
    if (!isset($_GET['video'])) {
        $video_link = $videos[0]['link'];
        $video = @end(explode('/', parse_url($video_link)['path']));
        header('location:' . $_SERVER['PHP_SELF'] . '?course=' . $_GET['course'] . '&video=' . $video);
    } else {
        if($video === 0){
            header('location:courses.php');
        }
    }
}else{
    header('location:courses.php');
}

?>

<div class="container my-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
            <li class="breadcrumb-item"><a href="#"><?php echo getCourse()['title']; ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $video['title'];?></li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-lg-7 col-md-6 col-12 mb-3 mb-md-0" style="font-family: cairo;">
            <div class="rounded border p-1 px-2" style="padding-top: 10px !important;">
                <iframe class="mb-0 pb-0" width="100%" height="350px" src="https://www.youtube.com/embed/<?php echo $_GET['video'];?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <div class="video-info">
                <h4 class="fw-bold mt-2 mb-0"><?php echo $video['title'];?></h4>
                <p class="text-black-50 mt-0"><?php echo $video['description'];?></p>
            </div>
        </div>
        <div class="col-lg-5 col-md-6 col-12" style="max-height: 400px; overflow: auto;">
            <h6><strong>Other Videos</strong></h6>
            <?php
            foreach ($videos as $item) {
                if($item['link'] != 'https://www.youtube.com/embed/' . $_GET['video']){
                    ?>
                    <div class="video-card rounded position-relative border mb-3 p-1 px-2" style="padding-top: 10px !important;">
                        <a href="<?php echo $_SERVER['PHP_SELF'] . '?course=' . $_GET['course'] . '&video=' . @end(explode('/', parse_url($item['link'])['path']));?>" class="override d-block" style="width: 100%;
                            height: 100%;
                            position: absolute;
                            top: 0px;
                            right: 0px;
                            left: 0px;
                            bottom: 0px;
                            z-index: 5555;
                            cursor: pointer;"></a>
                        <div class="row">
                            <div class="col-lg-5 col-md-6 col-12 mb-3 mb-md-0">
                                <iframe class="mb-0 pb-0" width="100%" height="100px" src="<?php echo $item['link'];?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                            <div class="col-lg-7 col-md-6 col-12">
                                <div class="video-info">
                                    <small>
                                        <h6 class="fw-bold mt-2"><?php echo $item['title'];?></h6>
                                        <p class="text-black-50"><?php echo $item['description'];?></p>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>

<?php include 'templates/_footer.php'; ?>