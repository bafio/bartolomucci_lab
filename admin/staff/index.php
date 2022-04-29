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
            <span>staff</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>

EOF;
$db = new DB(PROJ_ROOT.'data/');
$allowed_categories = array('Staff', 'Undergraduate', 'Alumni');
foreach($allowed_categories as $category) {
    $out .= <<<EOF
        <div class="group container">
            <header>
                <span class="title">$category</span>
                <a href="/admin/staff/add.php?category=$category">add</a>
            </header>

EOF;
    $people = $db->query("SELECT rowid, name, title FROM staff WHERE category = '$category' ORDER BY sort ASC");
    while( $person = $people->fetchArray() ) {
        $id = $person['rowid'];
        $n = utf8_decode($person['name']);
        $t = utf8_decode($person['title']);
        $out .= <<<EOF
            <div class="item">
                <div class="edit_buttons">
                    <p><a href="/admin/staff/edit.php?id=$id">edit</a></p>
                    <p><a href="/admin/staff/delete.php?id=$id">delete</a></p>
                </div>
                <div class="detail_box">
                    <p><span class="label">name: </span>$n, $t</p>
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