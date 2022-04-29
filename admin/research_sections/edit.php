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
            <span><a href="/admin/research_sections/">research sections</a> > </span>
            <span>edit</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Edit research section</h3>

EOF;
if( isset($_SESSION) && isset($_SESSION['confirm_add']) ) {
    $out .= <<<EOF
            <div class="form_result confirm">
                <p>The research section was added with success.</p>
            </div>

EOF;
    unset($_SESSION['confirm_add']);
}
$id = '';
$title = '';
$sort = '';
$research_section = null;
$valid = TRUE;
$db = new DB(PROJ_ROOT.'data/');
if( isset($_GET) && isset($_GET['id']) ) {
    $id = $_GET['id'];
    $research_section = $db->querySingle("SELECT * FROM research_sections WHERE rowid = $id", TRUE);
    if( is_null($research_section) || empty($research_section) ) {
        $out .= <<<EOF
            <div class="form_result">
                <p>The research section with id "$id" does not exist.</p>
            </div>
            <button class="left_side_button"><a href="/admin/research_sections/">go back</a></button>
        </div>
    </body>
</html>
EOF;
        die($out);
    }
    $title = utf8_decode($research_section['title']);
    $sort = $research_section['sort'];
} else {
    $db->close();
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/research_sections/' );
}
if( isset($_POST) ) {
    if( isset($_POST['title']) ) {
        $title = $_POST['title'];
        if( empty($title) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "title" field is required.</p>
            </div>

EOF;
            $title = '';
            $valid = FALSE;
        }
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
    if( $valid && isset($_POST['sent']) ) {
        $t = utf8_encode(SQLite3::escapeString($title));
        if( $db->exec("UPDATE research_sections SET title = '$t', sort = $sort WHERE rowid = $id") ) {
            $out .= <<<EOF
            <div class="form_result confirm">
                <p>The research section was edited with success.</p>
            </div>

EOF;
        } else {
            $out .= <<<EOF
            <div class="form_result">
                <p>The research section was NOT edited with success! Some problem occurred.</p>
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
                <p><label for="title">title:</label><input class="long_input" type="text" name="title" required value="<? echo $title; ?>"/></p>
                <p><label for="sort">sort:</label><input class="long_input" type="text" name="sort" required value="<? echo $sort; ?>"/></p>
                <p><button class="left_side_button"><a href="/admin/research_sections/">go back</a></button></p>
                <p><input type="submit" value="edit"/></p>
            </form>
        </div>
    </body>
</html>
