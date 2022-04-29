<?php
header('Content-Type: text/html; charset=utf-8');
define("PROJ_ROOT", "../../");
define("MEDIA_ROOT", PROJ_ROOT."media/images/staff/");
require(PROJ_ROOT.'admin/db_handler.php');
session_start();
if( !isset($_SESSION['user']) ) {
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/login.php' );
}
$allowed_categories = array('Staff', 'Undergraduate', 'Alumni');
$category = '';
if( isset($_GET) && isset($_GET['category']) ) {
    $category = utf8_decode($_GET['category']);
    if( !in_array($category, $allowed_categories) ) {
        header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/staff/' );
    }
} else {
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/staff/' );
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
            <span><a href="/admin/staff/">staff</a> > </span>
            <span>add</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Add $category</h3>

EOF;

$name = '';
$title = '';
$subtitle = '';
$email = '';
$phone = '';
$skype = '';
$bio = '';
$resp = '';
$file_name = '';

$db = new DB(PROJ_ROOT.'data/');
$valid = TRUE;
if( isset($_POST) ) {
    if( isset($_POST['category']) ) {
        $category = $_POST['category'];
        if( empty($category) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "category" field is required.</p>
            </div>

EOF;
            $category = '';
            $valid = FALSE;
        } else {
            if( !in_array($category, $allowed_categories) ) {
                $out .= <<<EOF
            <div class="form_result">
                <p>The category "$category" is not allawed.</p>
            </div>

EOF;
                $category = '';
                $valid = FALSE;
            }
        }
    }
    if( isset($_POST['name']) ) {
        $name = $_POST['name'];
        if( empty($name) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "name" field is required.</p>
            </div>

EOF;
            $name = '';
            $valid = FALSE;
        }
    }
    if( isset($_POST['title']) ) {
        $title = $_POST['title'];
    }
    if( isset($_POST['subtitle']) ) {
        $subtitle = $_POST['subtitle'];
    }
    if( isset($_POST['email']) ) {
        $email = $_POST['email'];
    }
    if( isset($_POST['phone']) ) {
        $phone = $_POST['phone'];
    }
    if( isset($_POST['skype']) ) {
        $skype = $_POST['skype'];
    }
    if( isset($_POST['bio']) ) {
        $bio = $_POST['bio'];
    }
    if( isset($_POST['resp']) ) {
        $resp = $_POST['resp'];
    }
    if( isset($_FILES["file"]) && !empty($_FILES["file"]["name"]) ) {
        $allowed_exts = array("gif", "jpeg", "jpg", "png");
        $allowed_mimetype = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");
        $file_name = $_FILES["file"]["name"];
        $file_ext = explode(".", $file_name);
        $extension = end($file_ext);
        $file_name = sha1(microtime()).'.'.$extension;
        if( !(in_array($_FILES["file"]["type"], $allowed_mimetype) && in_array($extension, $allowed_exts)) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The image file is invalid.</p>
            </div>

EOF;
            $file_name = '';
            $valid = FALSE;
        }
    }
    if( $valid && isset($_POST['sent']) ) {
        $c    = utf8_encode(SQLite3::escapeString($category));
        $t    = utf8_encode(SQLite3::escapeString($title));
        $n    = utf8_encode(SQLite3::escapeString($name));
        $s    = utf8_encode(SQLite3::escapeString($subtitle));
        $e    = utf8_encode(SQLite3::escapeString($email));
        $p    = utf8_encode(SQLite3::escapeString($phone));
        $sk   = utf8_encode(SQLite3::escapeString($skype));
        $b    = utf8_encode(SQLite3::escapeString($bio));
        $r    = utf8_encode(SQLite3::escapeString($resp));
        $sort = $db->querySingle("SELECT MAX(sort)+1 FROM staff WHERE category = '$c'");
        if( is_null($sort) ) {
            $sort = 1;
        }
        $confirm = TRUE;
        if( !empty($file_name) ) {
            $file_path = MEDIA_ROOT.$file_name;
            $confirm = move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);
        }
        if( $confirm ) {
            if( $db->exec("INSERT INTO staff (category, name, title, subtitle, email, phone, skype, bio, resp, image_path, sort) VALUES ('$c', '$n', '$t', '$s', '$e', '$p', '$sk', '$b', '$r', '$file_name', $sort)") ) {
                $id = $db->lastInsertRowID();
                $_SESSION['confirm_add'] = TRUE;
                $db->close();
                header( 'Location: http://'.$_SERVER['HTTP_HOST']."/admin/staff/edit.php?id=$id" );
            } else {
                $out .= <<<EOF
                <div class="form_result">
                    <p>The staff was NOT added! Some problem occurred.</p>
                </div>

EOF;
            }
        } else {
            $out .= <<<EOF
            <div class="form_result">
                <p>The staff was NOT added! Some problem occurred when uploading the image.</p>
            </div>

EOF;
        }
    }
}
$db->close();
echo $out;
?>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="sent" value="1"/>
                <input type="hidden" name="category" value="<? echo $category; ?>"/>
                <p><label for="name">name:</label><input class="long_input" type="text" name="name" required value="<? echo $name; ?>"/></p>
                <p><label for="title">title:</label><input class="long_input" type="text" name="title" value="<? echo $title; ?>"/></p>
                <p><label for="subtitle">subtitle:</label><input class="long_input" type="text" name="subtitle" value="<? echo $subtitle; ?>"/></p>
                <p><label for="email">email:</label><input class="long_input" type="email" name="email" value="<? echo $email; ?>"/></p>
                <p><label for="phone">phone number:</label><input class="long_input" type="tel" name="phone" value="<? echo $phone; ?>"/></p>
                <p><label for="skype">skype id:</label><input class="long_input" type="text" name="skype" value="<? echo $skype; ?>"/></p>
                <p><label for="bio">short bio:</label><textarea cols="50" rows="10" name="bio"><? echo $bio; ?></textarea></p>
                <p><label for="resp">responsabilities:</label><textarea cols="50" rows="10" name="resp"><? echo $resp; ?></textarea></p>
                <p><label for="file">image:</label><input type="file" name="file"/></p>
                <p><button class="left_side_button"><a href="/admin/staff/">go back</a></button></p>
                <p><input type="submit" value="add"/></p>
            </form>
        </div>
    </body>
</html>