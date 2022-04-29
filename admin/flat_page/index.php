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
            <span>flat pages</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="group container">
            <header>
                <a href="/admin/flat_page/add.php">add flat page</a>
            </header>
        </div>

EOF;
$db = new DB(PROJ_ROOT.'data/');
$categories = $db->query('SELECT category FROM flat_pages ORDER BY category ASC');
while( $category = $categories->fetchArray() ) {
    $c = utf8_decode($category['category']);
    $out .= <<<EOF
        <div class="group container">
            <header>
                <span class="title">$c</span>
            </header>

EOF;
    $flat_pages = $db->query("SELECT rowid, body FROM flat_pages WHERE category = '$c'");
    while( $flat_page = $flat_pages->fetchArray() ) {
        $id = $flat_page['rowid'];
        $b = substr(strip_tags(utf8_decode($flat_page['body'])), 0, 50).' ...';
        $out .= <<<EOF
            <div class="item">
                <div class="edit_buttons">
                    <p><a href="/admin/flat_page/edit.php?id=$id">edit</a></p>
                    <p><a href="/admin/flat_page/delete.php?id=$id">delete</a></p>
                </div>
                <div class="detail_box">
                    <p><span class="label">body: </span>$b</p>
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