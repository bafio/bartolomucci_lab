<?php
define("PROJ_ROOT", "../../");
define("IMG_DIR", 'media/images/');
require(PROJ_ROOT.'admin/db_handler.php');
$section = '';
$image = '';
if( isset($_POST) && isset($_POST['username']) && !empty($_POST['username']) &&
    isset($_POST['section']) && !empty($_POST['section']) &&
    isset($_POST['image']) && !empty($_POST['image']) ) {
    $username = $_POST['username'];
    $db = new DB(PROJ_ROOT.'data/');
    $user = $db->querySingle("SELECT rowid FROM users WHERE username = '$username'", false);
    $db->close();
    if( is_null($user) ) {
        die(http_response_code(401));
    }
    $section = $_POST['section'];
    $image = basename($_POST['image']);
} else {
    die(http_response_code(400));
}
if( unlink(PROJ_ROOT.IMG_DIR.$section.'/'.$image)) {
    die(http_response_code(200));
}
die(http_response_code(400));
?>