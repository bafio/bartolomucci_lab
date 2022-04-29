<?php
define("PROJ_ROOT", "../../");
define("IMG_DIR", 'media/images/');
require(PROJ_ROOT.'admin/db_handler.php');
$section = '';
if( isset($_POST) && isset($_POST['username']) && !empty($_POST['username']) && isset($_POST['section']) && !empty($_POST['section']) ) {
    $username = $_POST['username'];
    $db = new DB(PROJ_ROOT.'data/');
    $user = $db->querySingle("SELECT rowid FROM users WHERE username = '$username'", false);
    $db->close();
    if( is_null($user) ) {
        die(http_response_code(401));
    }
    $section = $_POST['section'];
} else {
    die(http_response_code(400));
}

$allowed_exts = array("gif", "jpeg", "jpg", "png");
$allowed_mimetype = array("image/gif", "image/jpeg", "image/jpg", "image/pjpeg", "image/x-png", "image/png");
$temp_name = explode(".", $_FILES["file"]["name"]);
$extension = end($temp_name);
$name = '';
if( in_array($_FILES["file"]["type"], $allowed_mimetype) && in_array($extension, $allowed_exts) ) {
    $name = sha1(microtime()).'.'.$extension;
    move_uploaded_file($_FILES["file"]["tmp_name"], PROJ_ROOT.IMG_DIR.$section.'/'.$name);
    $response = new StdClass;
    $response->link = PROJ_ROOT.IMG_DIR.$section.'/'.$name;
    echo stripslashes(json_encode($response));
} else {
    die(http_response_code(400));
}
?>