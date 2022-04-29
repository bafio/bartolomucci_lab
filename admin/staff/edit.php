<?php
header('Content-Type: text/html; charset=utf-8');
define("PROJ_ROOT", "../../");
define("MEDIA_ROOT", PROJ_ROOT."media/images/staff/");
define("MEDIA_URL", "/media/images/staff/");
require(PROJ_ROOT.'admin/db_handler.php');
session_start();
if( !isset($_SESSION['user']) ) {
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/login.php' );
}

$id = '';
$category = '';
$name = '';
$title = '';
$subtitle = '';
$email = '';
$phone = '';
$skype = '';
$bio = '';
$resp = '';
$file_name = '';
$sort = '';
$current_img = '';
$delete_img = FALSE;
$allowed_categories = array('Staff', 'Undergraduate', 'Alumni');

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
            <span>edit</span>
            <span id="auth"><a href="/admin/auth">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">

EOF;

$db = new DB(PROJ_ROOT.'data/');
if( isset($_GET) && isset($_GET['id']) ) {
    $id = $_GET['id'];
    $person = $db->querySingle("SELECT * FROM staff WHERE rowid = $id", TRUE);
    if( is_null($person) || empty($person) ) {
        $out .= <<<EOF
            <div class="form_result">
                <p>The staff with id "$id" does not exist.</p>
            </div>
            <button class="left_side_button"><a href="/admin/staff/">go back</a></button>
        </div>
    </body>
</html>
EOF;
        $db->close();
        die($out);
    }
    $category = utf8_decode($person['category']);
    $name = utf8_decode($person['name']);
    $title = utf8_decode($person['title']);
    $subtitle = utf8_decode($person['subtitle']);
    $email = utf8_decode($person['email']);
    $phone = utf8_decode($person['phone']);
    $skype = utf8_decode($person['skype']);
    $bio = utf8_decode($person['bio']);
    $resp = utf8_decode($person['resp']);
    $current_img = utf8_decode($person['image_path']);
    $sort = $person['sort'];
    if( in_array($category, $allowed_categories) ) {
        $out .= <<<EOF
            <h3>Edit $category</h3>

EOF;
    } else {
        $out .= <<<EOF
            <div class="form_result">
                <p>The category "$category" is not allawed.</p>
            </div>
            <button class="left_side_button"><a href="/admin/staff/">go back</a></button>
        </div>
    </body>
</html>
EOF;
        $db->close();
        die($out);
    }
} else {
    $db->close();
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/staff/' );
}

if( isset($_SESSION) && isset($_SESSION['confirm_add']) ) {
    $out .= <<<EOF
            <div class="form_result confirm">
                <p>The staff was added with success.</p>
            </div>

EOF;
    unset($_SESSION['confirm_add']);
}

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
    if( isset($_POST['sort']) ) {
        $sort = $_POST['sort'];
        if( ! is_numeric($sort) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "sort" field is required (numeric value).</p>
            </div>

EOF;
            $sort = '';
            $valid = FALSE;
        }
    }
    if( isset($_POST['delete_img']) ) {
        $delete_img = ($_POST['delete_img'] == 'yes');
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
        } else {
            $delete_img = TRUE;
        }
    }
    if( $valid && isset($_POST['sent']) ) {
        $c  = utf8_encode(SQLite3::escapeString($category));
        $n  = utf8_encode(SQLite3::escapeString($name));
        $t  = utf8_encode(SQLite3::escapeString($title));
        $s  = utf8_encode(SQLite3::escapeString($subtitle));
        $e  = utf8_encode(SQLite3::escapeString($email));
        $p  = utf8_encode(SQLite3::escapeString($phone));
        $sk = utf8_encode(SQLite3::escapeString($skype));
        $b  = utf8_encode(SQLite3::escapeString($bio));
        $r  = utf8_encode(SQLite3::escapeString($resp));
        if( $delete_img ) {
            unlink(MEDIA_ROOT.$current_img);
            $current_img = '';
        }
        $confirm = TRUE;
        if( !empty($file_name) ) {
            $current_img = $file_name;
            $file_path = MEDIA_ROOT.$file_name;
            $confirm = move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);
        }
        if( $confirm ) {
            $i = utf8_encode(SQLite3::escapeString($current_img));
            if( $db->exec("UPDATE staff SET category = '$c', name = '$n', title = '$t', subtitle = '$s', email = '$e', phone = '$p', skype = '$sk', bio = '$b', resp = '$r', image_path = '$i', sort = $sort WHERE rowid = $id") ) {
                $out .= <<<EOF
                <div class="form_result confirm">
                    <p>The staff was edited with success.</p>
                </div>

EOF;
            } else {
                $out .= <<<EOF
                <div class="form_result">
                    <p>The staff was NOT edited with success! Some problem occurred.</p>
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
                <p>
                    <label for="category">category:</label>
                    <select name="category" required>
<?
foreach($allowed_categories as $c) {
    $selected = "";
    if($category == $c) {
        $selected = " selected";
    }
    echo <<<EOF
                        <option value="$c"$selected>$c</option>
EOF;
}
?>
                    </select>
                </p>
                <p><label for="name">name:</label><input class="long_input" type="text" name="name" required value="<? echo $name; ?>"/></p>
                <p><label for="title">title:</label><input class="long_input" type="text" name="title" value="<? echo $title; ?>"/></p>
                <p><label for="subtitle">subtitle:</label><input class="long_input" type="text" name="subtitle" value="<? echo $subtitle; ?>"/></p>
                <p><label for="email">email:</label><input class="long_input" type="email" name="email" value="<? echo $email; ?>"/></p>
                <p><label for="phone">phone number:</label><input class="long_input" type="tel" name="phone" value="<? echo $phone; ?>"/></p>
                <p><label for="skype">skype id:</label><input class="long_input" type="text" name="skype" value="<? echo $skype; ?>"/></p>
                <p><label for="bio">short bio:</label><textarea cols="50" rows="10" name="bio" ><? echo $bio; ?></textarea></p>
                <p><label for="resp">responsabilities:</label><textarea cols="50" rows="10" name="resp" ><? echo $resp; ?></textarea></p>
<?
if( !empty($current_img) ) {
    $current_img_url = MEDIA_URL.$current_img;
    echo <<<EOF
                <p>
                    <label for="delete_img">delete current image:</label>
                    <span><span>no</span><input type="radio" name="delete_img" value="no" checked /></span>
                    <span><span>yes</span><input type="radio" name="delete_img" value="yes" /><img src="$current_img_url" width="120" /><span>
                </p>
EOF;
}
?>
                <p><label for="file">image:</label><input type="file" name="file" /></p>
                <p><label for="sort">sort:</label><input type="number" name="sort" value="<? echo $sort; ?>"/></p>
                <p><button class="left_side_button"><a href="/admin/staff/">go back</a></button></p>
                <p><input type="submit" value="edit"/></p>
            </form>
        </div>
    </body>
</html>