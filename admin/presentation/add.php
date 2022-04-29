<?php
header('Content-Type: text/html; charset=utf-8');
define("PROJ_ROOT", "../../");
define("MEDIA_ROOT", PROJ_ROOT."media/images/presentations/");
session_start();
if( !isset($_SESSION['user']) ) {
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/login.php' );
}

$add_presentation = TRUE;
$presentation = '';
if( isset($_GET) && isset($_GET['presentation']) ) {
    $add_presentation = FALSE;
    $presentation = urldecode($_GET['presentation']);
}

$out = <<<EOF
<!doctype html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="/css/reset.css">
        <link rel="stylesheet" href="/css/base.css">
        <link rel="stylesheet" href="/css/admin.css">
    </head>
    <body>
        <header>
            <span><a href="/admin/">admin home</a> > </span>
            <span><a href="/admin/presentation/">presentation's media manager</a> > </span>
            <span>add</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">

EOF;
if( $add_presentation ) {
    $out .= <<<EOF
            <h3>Add presentation</h3>

EOF;
} else {
    $out .= <<<EOF
            <h3>Add image to <b>"$presentation"</b> presentation</h3>

EOF;
}

if( isset($_SESSION) && isset($_SESSION['confirm_add']) ) {
    $out .= <<<EOF
            <div class="form_result confirm">
                <p>The image was added with success.</p>
            </div>

EOF;
    unset($_SESSION['confirm_add']);
}

if( isset($_POST) && isset($_POST['sent']) ) {
    $valid = TRUE;
    $file_name = '';
    $presentation = '';
    if( isset($_POST['presentation']) ) {
        $presentation = $_POST['presentation'];
        if( empty($presentation) ) {
            $out2 = <<<EOF
            <div class="form_result">
                <p>The "presentation" field is required.</p>
            </div>

EOF;
            $out .= $out2;
            $valid = FALSE;
        }
    } else {
        $out2 = <<<EOF
        <div class="form_result">
            <p>The "presentation" field is required.</p>
        </div>

EOF;
        $out .= $out2;
        $valid = FALSE;
    }
    if( isset($_FILES["file"]) ) {
        $allowed_exts = array("gif", "jpeg", "jpg", "png");
        $allowed_mimetype = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");
        $file_name = $_FILES["file"]["name"];
        $file_ext = explode(".", $file_name);
        $extension = end($file_ext);
        if( !(in_array($_FILES["file"]["type"], $allowed_mimetype) && in_array($extension, $allowed_exts)) ) {
            $out2 = <<<EOF
            <div class="form_result">
                <p>The image file is invalid.</p>
            </div>

EOF;
            $out .= $out2;
            $valid = FALSE;
        }
    } else {
        $out2 = <<<EOF
            <div class="form_result">
                <p>The "image" field is required.</p>
            </div>

EOF;
        $out .= $out2;
        $valid = FALSE;
    }
    if( $valid ) {
        $confirm = TRUE;
        if( !is_dir(MEDIA_ROOT.$presentation) ) {
            $confirm = mkdir(MEDIA_ROOT.$presentation);
        }
        if( $confirm ) {
            $file_path = MEDIA_ROOT.$presentation."/".$file_name;
            $confirm = move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);
        }
        if( $confirm ) {
            $_SESSION['confirm_add'] = TRUE;
            $encoded_presentation = urlencode($presentation);
            header( 'Location: http://'.$_SERVER['HTTP_HOST']."/admin/presentation/add.php?presentation=$encoded_presentation" );
        } else {
            $out2 = <<<EOF
            <div class="form_result">
                <p>The image was NOT added! Some problem occurred.</p>
            </div>

EOF;
            $out .= $out2;
        }
    }
}

$out .= <<<EOF
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="sent" value="1"/>

EOF;
if( $add_presentation ) {
    $out .= <<<EOF
                <p><label for="presentation">presentation:</label><input type="text" name="presentation" required value="$presentation"/></p>

EOF;
} else {
    $out .= <<<EOF
                <input type="hidden" name="presentation" value="$presentation"/>

EOF;
}
$out .= <<<EOF
                <p><label for="file">image:</label><input type="file" name="file" required /></p>
                <p><button class="left_side_button"><a href="/admin/presentation/">go back</a></button></p>
                <p><input type="submit" value="add"/></p>
            </form>
        </div>
    </body>
</html>

EOF;
echo $out;
?>