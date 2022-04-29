<?php
header('Content-Type: text/html; charset=utf-8');
define("PROJ_ROOT", "../../");
define("MEDIA_URL", "media/images/generic/");
define("MEDIA_ROOT", PROJ_ROOT.MEDIA_URL);
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
            <span>images manager</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="group container">
            <header>
                <a href="/admin/images/add.php">add image</a>
            </header>
        </div>
        <div class="group container">
            <header>
                <span class="title">images</span>
            </header>

EOF;
$images = array_diff(scandir(MEDIA_ROOT), array('..', '.'));
sort($images);
foreach($images as $image) {
    $encoded_image = urlencode($image);
    $image_path = "/".MEDIA_ROOT.$image;
    $image_url = "http://".$_SERVER['HTTP_HOST']."/".MEDIA_URL.$image;
    $out .= <<<EOF
            <div class="item">
                <div class="edit_buttons">
                    <p><a href="/admin/images/delete.php?image=$encoded_image">delete</a></p>
                    <p>image url: $image_url</p>
                </div>
                <div class="detail_box">
                    <p><img class="fancybox" src="$image_path"/></p>
                </div>
            </div>

EOF;
}
$out .= <<<EOF
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="/js/libs/jquery-1.8.2.min.js"><\/script>')</script>
        <script src="/js/libs/jquery.fancybox.pack.js"></script>
        <script src="/js/scripts/misc.js"></script>
    </body>
</html>
EOF;
echo $out;
?>
