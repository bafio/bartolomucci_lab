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
        <link rel="stylesheet" href="/css/base.css">
        <link rel="stylesheet" href="/css/font-awesome.min.css">
        <link rel="stylesheet" href="/css/froala_editor.min.css">
        <link rel="stylesheet" href="/css/admin.css">
    </head>
    <body>
        <header>
            <span><a href="/admin/">admin home</a> > </span>
            <span><a href="/admin/news_event/">news &amp; events</a> > </span>
            <span>edit</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Edit "news &amp; event"</h3>

EOF;
if( isset($_SESSION) && isset($_SESSION['confirm_add']) ) {
    $out .= <<<EOF
            <div class="form_result confirm">
                <p>The "news &amp; event" was added with success.</p>
            </div>

EOF;
    unset($_SESSION['confirm_add']);
}
$id = '';
$title = '';
$body = '';
$news_event = null;
$valid = TRUE;
$db = new DB(PROJ_ROOT.'data/');
if( isset($_GET) && isset($_GET['id']) ) {
    $id = $_GET['id'];
    $news_event = $db->querySingle("SELECT * FROM news_events WHERE rowid = $id", TRUE);
    if( is_null($news_event) || empty($news_event) ) {
        $out .= <<<EOF
            <div class="form_result">
                <p>The "news &amp; event" with id "$id" does not exist.</p>
            </div>
            <button class="left_side_button"><a href="/admin/news_event/">go back</a></button>
        </div>
    </body>
</html>
EOF;
        die($out);
    }
    $title = utf8_decode($news_event['title']);
    $body = utf8_decode($news_event['body']);
} else {
    $db->close();
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/news_event/' );
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
    if( isset($_POST['body']) ) {
        $body = $_POST['body'];
    }
    if( $valid && isset($_POST['sent']) ) {
        $t = utf8_encode(SQLite3::escapeString($title));
        $b = utf8_encode(SQLite3::escapeString($body));
        if( $db->exec("UPDATE news_events SET title = '$t', body = '$b' WHERE rowid = $id") ) {
            $out .= <<<EOF
            <div class="form_result confirm">
                <p>The "news &amp; event" was edited with success.</p>
            </div>

EOF;
        } else {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "news &amp; event" was NOT edited with success! Some problem occurred.</p>
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
                <input type="hidden" name="body" value=""/>
                <div class="editor_container">
                    <label for="froala_editor">body:</label>
                    <div id="froala_editor"></div>
                </div>
                <p><button class="left_side_button"><a href="/admin/news_event/">go back</a></button></p>
                <p><button class="froala_submit_button">edit</button></p>
            </form>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="/js/libs/jquery-1.10.2.min.js"><\/script>')</script>
        <script src="/js/libs/froala_editor.min.js"></script>
        <script>
            $(function() {
                froala_editor_params = {
                    body_content: "<? echo addslashes($body); ?>",
                    callback_extra_params: {
                        username: "<? echo $_SESSION['user']; ?>",
                        section: 'news_events'
                    }
                }
            });
        </script>
        <script src="/js/scripts/admin.js"></script>
    </body>
</html>
