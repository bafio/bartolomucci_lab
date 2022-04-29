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
            <span><a href="/admin/research_contents/">research contents</a> > </span>
            <span>edit</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Edit research content</h3>

EOF;
if( isset($_SESSION) && isset($_SESSION['confirm_add']) ) {
    $out .= <<<EOF
            <div class="form_result confirm">
                <p>The research content was added with success.</p>
            </div>

EOF;
    unset($_SESSION['confirm_add']);
}
$id = '';
$title = '';
$body = '';
$section = '';
$sort = '';
$item = null;
$valid = TRUE;
$db = new DB(PROJ_ROOT.'data/');
if( isset($_GET) && isset($_GET['id']) ) {
    $id = $_GET['id'];
    $item = $db->querySingle("SELECT * FROM research_contents WHERE rowid = $id", TRUE);
    if( is_null($item) || empty($item) ) {
        $out .= <<<EOF
            <div class="form_result">
                <p>The research content with id "$id" does not exist.</p>
            </div>
            <button class="left_side_button"><a href="/admin/research_contents/">go back</a></button>
        </div>
    </body>
</html>
EOF;
        die($out);
    }
    $title = utf8_decode($item['title']);
    $body = utf8_decode($item['body']);
    $sort = $item['sort'];
    $section = $item['section'];
} else {
    $db->close();
    header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/research_contents/' );
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
    if( isset($_POST['section']) ) {
        $section = $_POST['section'];
        if( ! is_numeric($section) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "section" field is required (numeric value).</p>
            </div>

EOF;
            $section = '';
            $valid = FALSE;
        }
    }
    if( $valid && isset($_POST['sent']) ) {
        $t = utf8_encode(SQLite3::escapeString($title));
        $b = utf8_encode(SQLite3::escapeString($body));
        if( $db->exec("UPDATE research_contents SET title = '$t', body = '$b', sort = $sort, section = $section WHERE rowid = $id") ) {
            $out .= <<<EOF
            <div class="form_result confirm">
                <p>The research content was edited with success.</p>
            </div>

EOF;
        } else {
            $out .= <<<EOF
            <div class="form_result">
                <p>The research content was NOT edited with success! Some problem occurred.</p>
            </div>

EOF;
        }
    }
}
echo $out;
?>

            <form action="" method="post">
                <input type="hidden" name="sent" value="1"/>
                <select class="long_input" name="section" required>
<?php
$out = '';
$research_sections = $db->query('SELECT rowid, title FROM research_sections ORDER BY sort ASC');
while( $research_section = $research_sections->fetchArray() ) {
    $t = utf8_decode($research_section['title']);
    $id = $research_section['rowid'];
    $selected = '';
    if( $id == $section ) {
        $selected = ' selected';
    }
    $out .= <<<EOF
                    <option value="$id"$selected>$t</option>
EOF;
}
echo $out;
$db->close();
?>
                </select>
                <p><label for="sort">sort:</label><input class="long_input" type="text" name="sort" required value="<? echo $sort; ?>"/></p>
                <p><label for="title">title:</label><input class="long_input" type="text" name="title" required value="<? echo $title; ?>"/></p>
                <input type="hidden" name="body" value=""/>
                <div class="editor_container">
                    <label for="froala_editor">body:</label>
                    <div id="froala_editor"></div>
                </div>
                <p><button class="left_side_button"><a href="/admin/research_contents/">go back</a></button></p>
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
                        section: 'research_contents'
                    }
                }
            });
        </script>
        <script src="/js/scripts/admin.js"></script>
    </body>
</html>
