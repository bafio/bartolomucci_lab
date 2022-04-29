<?php
header('Content-Type: text/html; charset=utf-8');
define("PROJ_ROOT", "../../");
define("MEDIA_ROOT", PROJ_ROOT."media/images/presentations/");
session_start();
if( !isset($_SESSION['user']) ) {
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/login.php' );
}

$out = <<<EOF
<!doctype html>
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
            <span>presentation's media manager</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="group container">
            <header>
                <a href="/admin/presentation/add.php">add presentation</a>
            </header>
        </div>

EOF;

$media_directories = array();
$media_scandir = array_diff(scandir(MEDIA_ROOT), array('..', '.'));
foreach($media_scandir as $dirname) {
    if( is_dir(MEDIA_ROOT.$dirname) ) {
        array_push($media_directories, $dirname);
    }
}
sort($media_directories);

foreach($media_directories as $presentation) {
    $encoded_presentation = urlencode($presentation);
    $out .= <<<EOF
        <div class="group container">
            <header>
                <span class="title">$presentation</span>
                <a href="/admin/presentation/delete.php?presentation=$encoded_presentation">delete presentation</a>
                <a href="/admin/presentation/add.php?presentation=$encoded_presentation">add image</a>
            </header>

EOF;
    $images = array_diff(scandir(MEDIA_ROOT.$presentation), array('..', '.'));
    sort($images);
    foreach($images as $image) {
        $encoded_image = urlencode($image);
        $image_path = "/".MEDIA_ROOT.$presentation."/".$image;
        $out .= <<<EOF
            <div class="item">
                <div class="edit_buttons">
                    <p><a href="/admin/presentation/delete.php?presentation=$encoded_presentation&image=$encoded_image">delete</a></p>
                </div>
                <div class="detail_box">
                    <p><img class="fancybox" src="$image_path"/></p>
                </div>
            </div>

EOF;
    }
    $out .= <<<EOF
        </div>

EOF;
}
$out .= <<<EOF
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="/js/libs/jquery-1.8.2.min.js"><\/script>')</script>
        <script src="/js/libs/jquery.fancybox.pack.js"></script>
        <script src="/js/scripts/misc.js"></script>
    </body>
</html>
EOF;
echo $out;
?>