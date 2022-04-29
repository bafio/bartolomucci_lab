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
            <span>add</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Add "news &amp; event"</h3>

EOF;
$db = new DB(PROJ_ROOT.'data/');
$title = '';
$body = '';
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
    if( isset($_POST['body']) ) {
        $body = $_POST['body'];
    }
    if( $valid && isset($_POST['sent']) ) {
        $t = utf8_encode(SQLite3::escapeString($title));
        $b = utf8_encode(SQLite3::escapeString($body));
        if( $db->exec("INSERT INTO news_events (title, body) VALUES ('$t', '$b')") ) {
            $id = $db->lastInsertRowID();
            $_SESSION['confirm_add'] = TRUE;
            $db->close();
            header( 'Location: http://'.$_SERVER['HTTP_HOST']."/admin/news_event/edit.php?id=$id" );
        } else {
            $out2 = <<<EOF
            <div class="form_result">
                <p>The "news &amp; event" was NOT added! Some problem occurred.</p>
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
                <input type="hidden" name="body" value=""/>
                <div class="editor_container">
                    <label for="froala_editor">body:</label>
                    <div id="froala_editor"></div>
                </div>
                <p><button class="left_side_button"><a href="/admin/news_event/">go back</a></button></p>
                <p><button class="froala_submit_button">add</button></p>
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
