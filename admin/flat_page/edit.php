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
            <span><a href="/admin/flat_page/">flat page</a> > </span>
            <span>edit</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Edit flat page</h3>

EOF;
if( isset($_SESSION) && isset($_SESSION['confirm_add']) ) {
    $out .= <<<EOF
            <div class="form_result confirm">
                <p>The flat page was added with success.</p>
            </div>

EOF;
    unset($_SESSION['confirm_add']);
}
$id = '';
$category = '';
$body = '';
$item = null;
$valid = TRUE;
$db = new DB(PROJ_ROOT.'data/');
if( isset($_GET) && isset($_GET['id']) ) {
    $id = $_GET['id'];
    $item = $db->querySingle("SELECT * FROM flat_pages WHERE rowid = $id", TRUE);
    if( is_null($item) || empty($item) ) {
        $out .= <<<EOF
            <div class="form_result">
                <p>The flat page with id "$id" does not exist.</p>
            </div>
            <button class="left_side_button"><a href="/admin/flat_page/">go back</a></button>
        </div>
    </body>
</html>
EOF;
        die($out);
    }
    $category = utf8_decode($item['category']);
    $body = utf8_decode($item['body']);
} else {
    $db->close();
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/flat_page/' );
}
if( isset($_POST) ) {
    if( isset($_POST['category']) ) {
        $category = $_POST['category'];
        if( empty($category) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "category" field is required.</p>
            </div>

EOF;
            $category = '';
            $valid = FALSE;
        }
    }
    if( isset($_POST['body']) ) {
        $body = $_POST['body'];
        if( empty($body) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "body" field is required.</p>
            </div>

EOF;
            $body = '';
            $valid = FALSE;
        }
    }
    if( $valid && isset($_POST['sent']) ) {
        $c = utf8_encode(SQLite3::escapeString($category));
        $b = utf8_encode(SQLite3::escapeString($body));
        if( $db->exec("UPDATE flat_pages SET category = '$c', body = '$b' WHERE rowid = $id") ) {
            $out .= <<<EOF
            <div class="form_result confirm">
                <p>The flat page was edited with success.</p>
            </div>

EOF;
        } else {
            $out .= <<<EOF
            <div class="form_result">
                <p>The flat page was NOT edited with success! Some problem occurred.</p>
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
                <p><label for="category">category:</label><input class="long_input" type="text" name="category" required value="<? echo $category; ?>"/></p>
                <input type="hidden" name="body" value=""/>
                <div class="editor_container">
                    <label for="froala_editor">body:</label>
                    <div id="froala_editor"></div>
                </div>
                <p><button class="left_side_button"><a href="/admin/flat_page/">go back</a></button></p>
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
                        section: 'flat_pages'
                    }
                }
            });
        </script>
        <script src="/js/scripts/admin.js"></script>
    </body>
</html>