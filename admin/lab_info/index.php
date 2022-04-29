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
            <span>lab info</span>
            <span id="auth"><a href="/admin/auth/">{$_SESSION['user']}</a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <h3>Edit lab info</h3>

EOF;
$address = '';
$phone = '';
$fax = '';
$email = '';
$gmap_url = '';
$valid = TRUE;
$db = new DB(PROJ_ROOT.'data/');

if( isset($_POST['sent']) ) {
    if( isset($_POST['address']) ) {
        $address = utf8_decode($_POST['address']);
        if( empty($address) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "address" field is required.</p>
            </div>

EOF;
            $address = '';
            $valid = FALSE;
        }
    }
    if( isset($_POST['phone']) ) {
        $phone = utf8_decode($_POST['phone']);
        if( empty($phone) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "phone" field is required.</p>
            </div>

EOF;
            $phone = '';
            $valid = FALSE;
        }
    }
    if( isset($_POST['fax']) ) {
        $fax = utf8_decode($_POST['fax']);
        if( empty($fax) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "fax" field is required.</p>
            </div>

EOF;
            $fax = '';
            $valid = FALSE;
        }
    }
    if( isset($_POST['email']) ) {
        $email = utf8_decode($_POST['email']);
        if( empty($email) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "email" field is required.</p>
            </div>

EOF;
            $email = '';
            $valid = FALSE;
        }
    }
    if( isset($_POST['gmap_url']) ) {
        $gmap_url = utf8_decode($_POST['gmap_url']);
        if( empty($gmap_url) ) {
            $out .= <<<EOF
            <div class="form_result">
                <p>The "google map URL" field is required.</p>
            </div>

EOF;
            $gmap_url = '';
            $valid = FALSE;
        }
    }
    if( $valid && isset($_POST['sent']) ) {
        $a = utf8_encode(SQLite3::escapeString($address));
        $p = utf8_encode(SQLite3::escapeString($phone));
        $f = utf8_encode(SQLite3::escapeString($fax));
        $e = utf8_encode(SQLite3::escapeString($email));
        $g = utf8_encode(SQLite3::escapeString($gmap_url));
        $db->exec("DELETE FROM lab_info");
        if( $db->exec("INSERT INTO lab_info (address, phone, fax, email, gmap_url) VALUES ('$a', '$p', '$f', '$e', '$g')") ) {
            $out .= <<<EOF
            <div class="form_result confirm">
                <p>The lab info were edited with success.</p>
            </div>

EOF;
        } else {
            $out .= <<<EOF
            <div class="form_result">
                <p>The lab info were NOT edited with success! Some problem occurred.</p>
            </div>

EOF;
        }
    }
} else {
    $infos = $db->querySingle("SELECT * FROM lab_info LIMIT 1", TRUE);
    $address = utf8_decode($infos['address']);
    $phone = utf8_decode($infos['phone']);
    $fax = utf8_decode($infos['fax']);
    $email = utf8_decode($infos['email']);
    $gmap_url = utf8_decode($infos['gmap_url']);
}
$out .= <<<EOF
            <form action="" method="post">
                <input type="hidden" name="sent" value="1"/>
                <p><label for="address">address:</label><textarea rows="5" cols="50" name="address" required>$address</textarea></p>
                <p><label for="phone">phone:</label><input class="long_input" type="tel" name="phone" required value="$phone"/></p>
                <p><label for="fax">fax:</label><input class="long_input" type="tel" name="fax" required value="$fax"/></p>
                <p><label for="email">email:</label><input class="long_input" type="email" name="email" required value="$email"/></p>
                <p><label for="gmap_url">google map URL:</label><input class="long_input" type="url" name="gmap_url" required value="$gmap_url"/></p>
                <p><button class="left_side_button"><a href="/admin/">go back</a></button></p>
                <p><input type="submit" value="edit"/></p>
            </form>

        </div>

    </body>
</html>
EOF;
echo $out;
$db->close();
?>