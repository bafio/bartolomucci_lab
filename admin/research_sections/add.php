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
            <span>add</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Add a new research section</h3>

EOF;
$db = new DB(PROJ_ROOT.'data/');
$title = '';
$valid = TRUE;
if( isset($_POST) ) {
    if( isset($_POST['title']) ) {
        $title = $_POST['title'];
        if( empty($title) ) {
            $out2 = <<<EOF
            <div class="form_result">
                <p>The "title" field is required.</p>
            </div>

EOF;
            $out .= $out2;
            $title = '';
            $valid = FALSE;
        }
    }
    if( $valid && isset($_POST['sent']) ) {
        $t = utf8_encode(SQLite3::escapeString($title));
        $s = $db->querySingle("SELECT MAX(sort)+1 FROM research_sections");
        if( is_null($s) ) {
            $s = 1;
        }
        if( $db->exec("INSERT INTO research_sections (title, sort) VALUES ('$t', $s)") ) {
            $id = $db->lastInsertRowID();
            $_SESSION['confirm_add'] = TRUE;
            $db->close();
            header( 'Location: http://'.$_SERVER['HTTP_HOST']."/admin/research_sections/edit.php?id=$id" );
        } else {
            $out2 = <<<EOF
            <div class="form_result">
                <p>The research section was NOT added! Some problem occurred.</p>
            </div>

EOF;
            $out .= $out2;
        }
    }
}
$db->close();
echo $out;
?>
            <form action="" method="post">
                <input type="hidden" name="sent" value="1"/>
                <p><label for="title">title:</label><input class="long_input" type="text" name="title" required value="<? echo $title; ?>"/></p>
                <p><button class="left_side_button"><a href="/admin/research_sections/">go back</a></button></p>
                <p><input type="submit" value="add"/></p>
            </form>
        </div>
    </body>
</html>
