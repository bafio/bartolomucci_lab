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
            <span><a href="/admin/publication/">publications</a> > </span>
            <span>edit publication</span>
            <span id="auth"><a href="/admin/auth">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Edit publication</h3>

EOF;
if( isset($_SESSION) && isset($_SESSION['confirm_add']) ) {
    $out .= <<<EOF
            <div class="form_result confirm">
                <p>The publication was added with success.</p>
            </div>

EOF;
    unset($_SESSION['confirm_add']);
}
$id = '';
$year = '';
$title = '';
$url = '';
$authors = '';
$info = '';
$sort = '';
$publication = null;
$valid = TRUE;
$db = new DB(PROJ_ROOT.'data/');
if( isset($_GET) && isset($_GET['id']) ) {
    $id = $_GET['id'];
    $publication = $db->querySingle("SELECT * FROM publications WHERE rowid = $id", TRUE);
    if( is_null($publication) || empty($publication) ) {
        $out .= <<<EOF
            <div class="form_result">
                <p>The publication with id "$id" does not exist.</p>
            </div>
            <button class="left_side_button"><a href="/admin/publication/">go back</a></button>
        </div>
    </body>
</html>
EOF;
        die($out);
    }
    $year = $publication['year'];
    $title = utf8_decode($publication['title']);
    $url = utf8_decode($publication['url']);
    $authors = utf8_decode($publication['authors']);
    $info = utf8_decode($publication['info']);
    $sort = $publication['sort'];
} else {
    $db->close();
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/publication/' );
}
if( isset($_POST) ) {
    if( isset($_POST['year']) ) {
        $year = $_POST['year'];
        if( ! is_numeric($year) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "year" field is required (numeric value).</p>
            </div>

EOF;
            $year = '';
            $valid = FALSE;
        }
    }
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
    if( isset($_POST['url']) ) {
        $url = $_POST['url'];
        if( empty($url) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "url" field is required.</p>
            </div>

EOF;
            $url = '';
            $valid = FALSE;
        }
    }
    if( isset($_POST['authors']) ) {
        $authors = $_POST['authors'];
        if( empty($authors) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "authors" field is required.</p>
            </div>

EOF;
            $authors = '';
            $valid = FALSE;
        }
    }
    if( isset($_POST['info']) ) {
        $info = $_POST['info'];
        if( empty($info) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "info" field is required.</p>
            </div>

EOF;
            $info = '';
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
        $u = utf8_encode(SQLite3::escapeString($url));
        $a = utf8_encode(SQLite3::escapeString($authors));
        $i = utf8_encode(SQLite3::escapeString($info));
        if( $db->exec("UPDATE publications SET year = $year, title = '$t', url = '$u', authors = '$a', info = '$i', sort = $sort WHERE rowid = $id") ) {
            $out .= <<<EOF
            <div class="form_result confirm">
                <p>The publication was edited with success.</p>
            </div>

EOF;
        } else {
            $out .= <<<EOF
            <div class="form_result">
                <p>The publication was NOT edited with success! Some problem occurred.</p>
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
                <p><label for="year">year:</label><input class="long_input" type="text" name="year" required value="<? echo $year; ?>"/></p>
                <p><label for="title">title:</label><input class="long_input" type="text" name="title" required value="<? echo $title; ?>"/></p>
                <p><label for="url">url:</label><input class="long_input" type="url" name="url" required value="<? echo $url; ?>"/></p>
                <p><label for="authors">authors:</label><input class="long_input" type="text" name="authors" required value="<? echo $authors; ?>"/></p>
                <p><label for="info">info:</label><input class="long_input" type="text" name="info" required value="<? echo $info; ?>"/></p>
                <p><label for="sort">sort:</label><input class="long_input" type="text" name="sort" required value="<? echo $sort; ?>"/></p>
                <p><button class="left_side_button"><a href="/admin/publication/">go back</a></button></p>
                <p><input type="submit" value="edit"/></p>
            </form>
        </div>
    </body>
</html>