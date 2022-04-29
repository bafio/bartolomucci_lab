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
            <span>news &amp; events</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="group container">
            <header>
                <a href="/admin/news_event/add.php">add news &amp; event</a>
            </header>

EOF;
$db = new DB(PROJ_ROOT.'data/');
$news_events = $db->query("SELECT sort, title, body FROM news_events ORDER BY sort DESC");
while( $news_event = $news_events->fetchArray() ) {
    $id = $news_event['sort'];
    $t = utf8_decode($news_event['title']);
    $b = substr(strip_tags(utf8_decode($news_event['body'])), 0, 50).' ...';
    $out .= <<<EOF
            <div class="item">
                <div class="edit_buttons">
                    <p><a href="/admin/news_event/edit.php?id=$id">edit</a></p>
                    <p><a href="/admin/news_event/delete.php?id=$id">delete</a></p>
                </div>
                <div class="detail_box">
                    <p><span class="label">title: </span>$t</p>
                    <p><span class="label">body: </span>$b</p>
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
