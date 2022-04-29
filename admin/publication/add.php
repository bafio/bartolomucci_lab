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
            <span>add</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Add publication</h3>

EOF;
$db = new DB(PROJ_ROOT.'data/');
$year = $db->querySingle("SELECT MAX(year)+1 FROM publications");
$title = '';
$url = '';
$authors = '';
$info = '';
$valid = TRUE;
if( isset($_GET) && isset($_GET['year']) ) {
    $year = $_GET['year'];
}
if( isset($_POST) ) {
    if( isset($_POST['year']) ) {
        $year = $_POST['year'];
        if( ! is_numeric($year) ) {
            $out2 = <<<EOF
            <div class="form_result">
                <p>The "year" field is required (numeric value).</p>
            </div>

EOF;
            $out .= $out2;
            $year = '';
            $valid = FALSE;
        }
    }
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
    if( isset($_POST['url']) ) {
        $url = $_POST['url'];
        if( empty($url) ) {
            $out2 = <<<EOF
            <div class="form_result">
                <p>The "url" field is required.</p>
            </div>

EOF;
            $out .= $out2;
            $url = '';
            $valid = FALSE;
        }
    }
    if( isset($_POST['authors']) ) {
        $authors = $_POST['authors'];
        if( empty($authors) ) {
            $out2 = <<<EOF
            <div class="form_result">
                <p>The "authors" field is required.</p>
            </div>

EOF;
            $out .= $out2;
            $authors = '';
            $valid = FALSE;
        }
    }
    if( isset($_POST['info']) ) {
        $info = $_POST['info'];
        if( empty($info) ) {
            $out2 = <<<EOF
            <div class="form_result">
                <p>The "info" field is required.</p>
            </div>

EOF;
            $out .= $out2;
            $info = '';
            $valid = FALSE;
        }
    }
    if( $valid && isset($_POST['sent']) ) {
        $t = utf8_encode(SQLite3::escapeString($title));
        $u = utf8_encode(SQLite3::escapeString($url));
        $a = utf8_encode(SQLite3::escapeString($authors));
        $i = utf8_encode(SQLite3::escapeString($info));
        $s = $db->querySingle("SELECT MAX(sort)+1 FROM publications WHERE year = '$year'");
        if( is_null($s) ) {
            $s = 1;
        }
        if( $db->exec("INSERT INTO publications (year, title, url, authors, info, sort) VALUES ($year, '$t', '$u', '$a', '$i', $s)") ) {
            $id = $db->lastInsertRowID();
            $_SESSION['confirm_add'] = TRUE;
            $db->close();
            header( 'Location: http://'.$_SERVER['HTTP_HOST']."/admin/publication/edit.php?id=$id" );
        } else {
            $out2 = <<<EOF
            <div class="form_result">
                <p>The publication was NOT added! Some problem occurred.</p>
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
                <p><label for="year">year:</label><input class="long_input" type="text" name="year" required value="<? echo $year; ?>"/></p>
                <p><label for="title">title:</label><input class="long_input" type="text" name="title" required value="<? echo $title; ?>"/></p>
                <p><label for="url">url:</label><input class="long_input" type="url" name="url" required value="<? echo $url; ?>"/></p>
                <p><label for="authors">authors:</label><input class="long_input" type="text" name="authors" required value="<? echo $authors; ?>"/></p>
                <p><label for="info">info:</label><input class="long_input" type="text" name="info" required value="<? echo $info; ?>"/></p>
                <p><button class="left_side_button"><a href="/admin/publication/">go back</a></button></p>
                <p><input type="submit" value="add"/></p>
            </form>
        </div>
    </body>
</html>