<?php
header('Content-Type: text/html; charset=utf-8');
define("PROJ_ROOT", "../");
require(PROJ_ROOT.'admin/db_handler.php');
session_start();
if( isset($_SESSION['user']) ) {
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/' );
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
            <span>log in</span>
        </header>
        <div class="container">
            <div id="login">

EOF;
$username = '';
$password = '';
$userid = NULL;
if( isset($_POST) && isset($_POST['username']) && isset($_POST['password']) ) {
    $username = $_POST['username'];
    $db = new DB(PROJ_ROOT.'data/');
    $user = $db->querySingle("SELECT rowid, password FROM users WHERE username = '$username'", TRUE);
    $password = $user['password'];
    $userid = $user['rowid'];
    $db->close();
    if( is_null($password) || crypt($_POST['password'], $password) != $password ) {
        $out2 = <<<EOF
                <div class="form_result">
                    <p>The username/password are NOT correct!</p>
                    <p>please try again</p>
                </div>

EOF;
        $out .= $out2;
    } else {
        $_SESSION['user'] = $username;
        $_SESSION['userid'] = $userid;
        header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/' );
    }
}
echo $out;
?>
                <form action="" method="post">
                    <p><label for="username">username:</label><input type="text" name="username" required /></p>
                    <p><label for="password">password:</label><input type="password" name="password" required /></p>
                    <p><input type="submit" value="login"/></p>
                </form>
            </div>
        </div>
    </body>
</html>