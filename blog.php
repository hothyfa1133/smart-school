<?php
$pageName = 'news';
require 'includes/init.php';

// get news function
function getNews () {
    global $conn;

    // get news
    $getNews = $conn->prepare("SELECT
        image, title, description, date
        FROM
        news
        ORDER BY id DESC");
    $getNews->execute();
    if($getNews->rowCount() > 0){ // found
        return $getNews->fetchAll();
    }else{ // not found
        return 0;
    }
}
?>

<div class="all-title-box">
    <div class="container text-center">
        <h1>
           News
           <span class="m_1">Exclusive school news for students, parents and guests </span>
        </h1>
    </div>
</div>
<div id="overviews" class="section wb">
    <div class="container">
        <div class="section-title row text-center">
            <div class="col-md-8 offset-md-2">
                <p class="lead">Working collaboratively to ensure every student achieves academically, socially, and emotionally!</p>
            </div>
        </div><!-- end title -->
        <hr class="invis"> 
        <div class="row"> 
            <?php
            $news = getNews();
            if($news !== 0){ // not empty result

                // loop on result
                foreach ($news as $item) {
                    ?>
                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                        <div class="blog-item">
                            <div class="image-blog">
                                <img src="<?php echo 'images/news/' . $item['image'];?>" alt="<?php echo $item['title'] . ' Image';?>" class="img-fluid">
                            </div>
                            <div class="meta-info-blog">
                                <span>
                                    <i class="fa fa-calendar"></i>
                                    <span><?php echo $item['date'];?></span>
                                </span>
                            </div>
                            <div class="blog-title">
                                <h2><?php echo $item['title'];?></h2>
                            </div>
                            <div class="blog-desc">
                                <p><?php echo nl2br($item['description']);?></p>
                            </div>
                        </div>
                    </div>
                    <?php
                }

            }else{ // empty result
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