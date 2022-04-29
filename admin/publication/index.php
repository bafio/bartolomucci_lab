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
            <span>publications</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="group container">
            <header>
                <a href="/admin/publication/add.php">add publication</a>
            </header>
        </div>

EOF;
$db = new DB(PROJ_ROOT.'data/');
$years = $db->query('SELECT DISTINCT(year) FROM publications ORDER BY year DESC');
while( $year = $years->fetchArray() ) {
    $y = $year['year'];
    $out .= <<<EOF
        <div class="group container">
            <header>
                <span class="title">$y</span>
                <a href="/admin/publication/add.php?year=$y">add publication</a>
            </header>

EOF;
    $publications = $db->query("SELECT rowid, title, url, authors, info FROM publications WHERE year = $y ORDER BY sort DESC");
    while( $publication = $publications->fetchArray() ) {
        $id = $publication['rowid'];
        $t = utf8_decode($publication['title']);
        $u = utf8_decode($publication['url']);
        $a = utf8_decode($publication['authors']);
        $i = utf8_decode($publication['info']);
        $out .= <<<EOF
            <div class="item">
                <div class="edit_buttons">
                    <p><a href="/admin/publication/edit.php?id=$id">edit</a></p>
                    <p><a href="/admin/publication/delete.php?id=$id">delete</a></p>
                </div>
                <div class="detail_box">
                    <p><span class="label">title: </span>$t</p>
                    <p><span class="label">url: </span><a target="_blank" href="$u">$u</a></p>
                    <p><span class="label">authors: </span>$a</p>
                    <p><span class="label">info: </span>$i</p>
                </div>
            </div>

EOF;
    }
    $out .= <<<EOF
        </div>

EOF;
}
$out .= <<<EOF
    </body>
</html>
EOF;
echo $out;
$db->close();
?>