<?php
header('Content-Type: text/html; charset=utf-8');
define("PROJ_ROOT", "../../");
require(PROJ_ROOT.'admin/db_handler.php');
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
            <span><a href="/admin/auth/">auth</a> > </span>
            <span>change password</span>
            <span id="auth">{$_SESSION['user']}<a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Change password</h3>

EOF;
$password = '';
$userid = '';
$db = new DB(PROJ_ROOT.'data/');
if( isset($_GET) && isset($_GET['id']) ) {
    $id = $_GET['id'];
    $user = $db->querySingle("SELECT rowid, password FROM users WHERE rowid = $id", TRUE);
    if( is_null($user) || empty($user) ) {
        $out .= <<<EOF
            <div class="form_result">
                <p>The user with id "$id" does not exist.</p>
            </div>
            <button class="left_side_button"><a href="/admin/auth/">go back</a></button>
        </div>
    </body>
</html>
EOF;
        $db->close();
        die($out);
    } else {
        $password = $user['password'];
        $userid = $user['rowid'];
    }
} else {
    $db->close();
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/' );
}
$old_password = '';
$new_password1 = '';
$new_password2 = '';
$valid = TRUE;
if( isset($_POST) && isset($_POST['sent']) ) {
    if( isset($_POST['old_password']) ) {
        $old_password = $_POST['old_password'];
    }
    if( isset($_POST['new_password1']) ) {
        $new_password1 = $_POST['new_password1'];
    }
    if( isset($_POST['new_password2']) ) {
        $new_password2 = $_POST['new_password2'];
    }
    if( empty($old_password) ) {
        $valid = FALSE;
        $out .= <<<EOF
            <div class="form_result">
                <p>The "old password" field is required.</p>
            </div>

EOF;
    } else {
        if( crypt($old_password, $password) != $password ) {
            $valid = FALSE;
            $out .= <<<EOF
            <div class="form_result">
                <p>The "old password" doesn't match your current password.</p>
            </div>

EOF;
        }
    }
    if( empty($new_password1) ) {
        $valid = FALSE;
        $out .= <<<EOF
            <div class="form_result">
                <p>The "new password" field is required.</p>
            </div>

EOF;
    }
    if( empty($new_password2) ) {
        $valid = FALSE;
        $out .= <<<EOF
        <div class="form_result">
            <p>The "repeat new password" field is required.</p>
        </div>

EOF;
    }
    if( $valid && $new_password1 != $new_password2 ) {
        $valid = FALSE;
        $out .= <<<EOF
            <div class="form_result">
                <p>The passwords you entered in "new password" and "repeat new password" fields don't match.</p>
            </div>

EOF;
    }
    if( $valid && strlen($new_password1) < 6 ) {
        $valid = FALSE;
        $out .= <<<EOF
            <div class="form_result">
                <p>The new password must be at least 6 character long.</p>
            </div>

EOF;
    }
    if( $valid ) {
        $hashed_password = crypt($new_password1);
        if( $db->exec("UPDATE users SET password = '$hashed_password' WHERE rowid = $userid") ) {
            $db->close();
            header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/logout.php' );
        } else {
            $out .= <<<EOF
            <div class="form_result">
                <p>The password change was NOT done with success! Some problem occurred.</p>
            </div>

EOF;
        }
    }
}
$db->close();
echo $out;
?>

            <form action="" method="post">
                <input type="hidden" name="sent" value="1"/>
                <p><label class="long_label" for="old_password">old password:</label><input type="password" name="old_password" required /></p>
                <p><label class="long_label" for="new_password1">new password:</label><input type="password" name="new_password1" required /></p>
                <p><label class="long_label" for="new_password2">repeat new password:</label><input type="password" name="new_password2" required /></p>
                <p><button class="left_side_button"><a href="/admin/auth/">go back</a></button></p>
                <p><input type="submit" value="change"/></p>
            </form>
        </div>
    </body>
</html>