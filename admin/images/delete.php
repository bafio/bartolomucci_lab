<?php
header('Content-Type: text/html; charset=utf-8');
define("PROJ_ROOT", "../../");
define("MEDIA_ROOT", PROJ_ROOT."media/images/generic/");
session_start();
if( !isset($_SESSION['user']) ) {
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/login.php' );
}

$out = <<<EOF
<html lang="en">
    <head>
        <link rel="stylesheet" href="/css/reset.css">
        <link rel="stylesheet" href="/css/fancybox/jquery.fancybox.css">
        <link rel="stylesheet" href="/css/base.css">
        <link rel="stylesheet" href="/css/admin.css">
    </head>
    <body>
        <header>
            <span><a href="/admin/">admin home</a> > </span>
            <span><a href="/admin/image/">image's media manager</a> > </span>
            <span>delete</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Delete image</h3>

EOF;
$out2 = '';
$out3 = <<<EOF
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="/js/libs/jquery-1.8.2.min.js"><\/script>')</script>
        <script src="/js/libs/jquery.fancybox.pack.js"></script>
        <script src="/js/scripts/misc.js"></script>
    </body>
</html>
EOF;
if( isset($_GET) && isset($_GET['image']) ) {
    $encoded_image = $_GET['image'];
    $image = urldecode($encoded_image);
    $image_path = "/".MEDIA_ROOT.$image;
    $out2 = <<<EOF
            <div class="form_result">
                <p>Are you sure you want to delete the image with name: "$image"?</p>
            </div>
            <div class="detail_box">
                <p><img class="fancybox" src="$image_path"/></p>
            </div>
            <form action="" method="post">
                <button class="left_side_button"><a href="/admin/images/">no, go back</a></button>
                <p><input type="submit" value="yes, delete"/></p>
                <input type="hidden" name="image" value="$encoded_image"/>
            </form>

EOF;
} else {
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/images/' );
}

if( isset($_POST) && isset($_POST['image']) ) {
    $encoded_image = $_POST['image'];
    $image = urldecode($encoded_image);
    if( unlink(MEDIA_ROOT.$image) ) {
        $out2 = <<<EOF
            <div class="form_result confirm">
                <p>The image was deleted with success.</p>
            </div>
            <button class="left_side_button"><a href="/admin/images/">go back</a></button>

EOF;
    } else {
        $out2 = <<<EOF
        <div class="form_result">
            <p>The image was NOT deleted! Some problem occurred.</p>
        </div>
        <button class="left_side_button"><a href="/admin/images/">go back</a></button>

EOF;
    }
}
echo $out.$out2.$out3;
?>
