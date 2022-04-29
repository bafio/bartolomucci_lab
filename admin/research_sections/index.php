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
            <span>research sections</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="group container">
            <header>
                <a href="/admin/research_sections/add.php">add research section</a>
            </header>
        </div>
        <div class="group container">
EOF;
$db = new DB(PROJ_ROOT.'data/');
$research_sections = $db->query('SELECT rowid, title, sort FROM research_sections ORDER BY sort ASC');
while( $research_section = $research_sections->fetchArray() ) {
    $t = utf8_decode($research_section['title']);
    $s = $research_section['sort'];
    $id = $research_section['rowid'];
    $out .= <<<EOF
            <div class="item">
                <div class="edit_buttons">
                    <p><a href="/admin/research_sections/edit.php?id=$id">edit</a></p>
                    <p><a href="/admin/research_sections/delete.php?id=$id">delete</a></p>
                </div>
                <div class="detail_box">
                    <p><span class="label">title: </span>$t</p>
                    <p><span class="label">sort: </span>$s</p>
                </div>
            </div>

EOF;
    }
    $out .= <<<EOF
        </div>
    </body>
</html>
EOF;
echo $out;
$db->close();
?>
