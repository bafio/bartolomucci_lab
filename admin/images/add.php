<?php
header('Content-Type: text/html; charset=utf-8');
define("PROJ_ROOT", "../../");
define("MEDIA_ROOT", PROJ_ROOT."media/images/generic/");
session_start();
if( !isset($_SESSION['user']) ) {
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/login.php' );
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
            <span><a href="/admin/images/">images's media manager</a> > </span>
            <span>add</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Add image</h3>
EOF;

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
    if( isset($_FILES["file"]) ) {
        $allowed_exts = array("gif", "jpeg", "jpg", "png");
        $allowed_mimetype = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");
        $file_name = $_FILES["file"]["name"];
        $file_ext = explode(".", $file_name);
        $extension = end($file_ext);
        if( !(in_array($_FILES["file"]["type"], $allowed_mimetype) && in_array($extension, $allowed_exts)) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The image file is invalid.</p>
            </div>

EOF;
            $valid = FALSE;
        }
    } else {
        $out .= <<<EOF
            <div class="form_result">
                <p>The "image" field is required.</p>
            </div>

EOF;
        $valid = FALSE;
    }
    if( $valid ) {
        $confirm = TRUE;
        if( !is_dir(MEDIA_ROOT) ) {
            $confirm = mkdir(MEDIA_ROOT);
        }
        if( $confirm ) {
            $file_path = MEDIA_ROOT.$file_name;
            $confirm = move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);
        }
        if( $confirm ) {
            $_SESSION['confirm_add'] = TRUE;
            header( 'Location: http://'.$_SERVER['HTTP_HOST']."/admin/images/add.php" );
        } else {
            $out .= <<<EOF
            <div class="form_result">
                <p>The image was NOT added! Some problem occurred.</p>
            </div>

EOF;
        }
    }
}

$out .= <<<EOF
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="sent" value="1"/>
                <p><label for="file">image:</label><input type="file" name="file" required /></p>
                <p><button class="left_side_button"><a href="/admin/images/">go back</a></button></p>
                <p><input type="submit" value="add"/></p>
            </form>
        </div>
    </body>
</html>

EOF;
echo $out;
?>
