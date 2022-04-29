<?php
header('Content-Type: text/html; charset=utf-8');
define("PROJ_ROOT", "../../");
require(PROJ_ROOT.'admin/db_handler.php');
session_start();
if( !isset($_SESSION['user']) ) {
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/login.php' );
}
$out = <<<EOF
<html lang="en">
    <head>
        <link rel="stylesheet" href="/css/reset.css">
        <link rel="stylesheet" href="/css/base.css">
        <link rel="stylesheet" href="/css/admin.css">
    </head>
    <body>
        <header>
            <span><a href="/admin/">admin home</a> > </span>
            <span><a href="/admin/research_contents/">research contents</a> > </span>
            <span>delete</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Delete research contents</h3>

EOF;
$out2 = '';
$out3 = <<<EOF
        </div>
    </body>
</html>
EOF;
$id = '';
$db = new DB(PROJ_ROOT.'data/');
if( isset($_GET) && isset($_GET['id']) ) {
    $id = $_GET['id'];
    $title = $db->querySingle("SELECT title FROM research_contents WHERE rowid = $id");
    if( is_null($title) ) {
        $out2 = <<<EOF
            <div class="form_result">
                <p>The research content with id "$id" does not exist.</p>
            </div>
            <button class="left_side_button"><a href="/admin/research_contents/">go back</a></button>

EOF;
    } else {
        $title = utf8_decode($title);
        $out2 = <<<EOF
            <div class="form_result">
                <p>Are you sure you want to delete the research content with title: "$title"?</p>
            </div>
            <form action="" method="post">
                <button class="left_side_button"><a href="/admin/research_contents/">no, go back</a></button>
                <p><input type="submit" value="yes, delete"/></p>
                <input type="hidden" name="id" value="$id"/>
            </form>

EOF;
    }
} else {
    $db->close();
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/research_contents/' );
}
if( isset($_POST) && isset($_POST['id']) ) {
    $id = $_POST['id'];
    if( $db->exec("DELETE FROM research_contents WHERE rowid = $id") ) {
        $out2 = <<<EOF
            <div class="form_result confirm">
                <p>The research content was deleted with success.</p>
            </div>
            <button class="left_side_button"><a href="/admin/research_contents/">go back</a></button>

EOF;
    } else {
        $out2 = <<<EOF
            <div class="form_result">
                <p>The research content was NOT deleted! Some problem occurred.</p>
            </div>
            <button class="left_side_button"><a href="/admin/research_contents/">go back</a></button>

EOF;
    }
}
$db->close();
echo $out.$out2.$out3;
?>
