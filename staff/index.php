<?php
header('Content-Type: text/html; charset=utf-8');
define("PROJ_ROOT", "../");
define("MEDIA_ROOT", PROJ_ROOT."media/images/");
define("MEDIA_URL", "/media/images/");
require(PROJ_ROOT.'admin/db_handler.php');
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width">
        <meta name="copyright" content="Copyright (c) <?php echo date("Y"); ?> Alessandro Bartolomucci">
        <meta name="author" content="Fabio Trabucchi">

        <meta name="description" content="Bartolomucci Lab research obesity stress diabetes physiology">
        <meta name="keywords" content="Bartolomucci Bartolomucci-Lab research obesity stress diabetes diabetic molecular molecular-mechanisms stress-induced physiology pathophysiology anti-obesity anti-diabetic peptide Vgf TLQP TLQP-21 integrative-approach in-vivo in-vivo-models cellular-models molecular-techniques disease">

        <title>Bartolomucci Lab - Staff</title>

        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
          ga('create', 'UA-39685306-1', 'auto');
          ga('require', 'linkid', 'linkid.js');
          ga('require', 'displayfeatures');
          ga('send', 'pageview');
        </script>

        <link rel="stylesheet" href="/css/reset.css">
        <link rel="stylesheet" href="/css/nivo-slider/nivo-slider.css">
        <link rel="stylesheet" href="/css/base.css">
        <link rel="stylesheet" href="/css/style.css">

    </head>
    <body>
        <div id="wrap">
            <div id="header_wrap">
                <header id="top_header">
                    <div id="vcenter">
                        <div id="sitename">Bartolomucci Lab</div>
                        <div id="sub_sitename">University of Minnesota</div>
                    </div>
                </header>
                <nav id="main_nav">
                    <ul>
                        <li><a href="/">home</a></li>
                        <li class="selected"><a href="/staff/">staff</a></li>
                        <li><a href="/research/">research</a></li>
                        <li><a href="/publications/">publications</a></li>
                        <li><a href="/news_events/">news &amp; events</a></li>
                        <li><a href="/teaching/">teaching</a></li>
                        <li><a href="/links/">links</a></li>
                        <li><a href="/contacts/">contacts</a></li>
                    </ul>
                </nav>
            </div>

            <article class="c15">
                <div id="content_wrap">
                    <div id="content">

                        <section>
                            <div class="row_container shown">
                                <div class="contact_box page_curl bottom_right" id="staff_pictures">
                                    <div id="slider" class="nivoSlider">
<?php
$out = '';
$images = array_diff(scandir(MEDIA_ROOT."presentations/staff/"), array('..', '.'));
sort($images);
foreach($images as $image) {
    $image_path = MEDIA_ROOT."presentations/staff/".$image;
    $image_url = MEDIA_URL."presentations/staff/".$image;
    if( is_file($image_path) ) {
        $out .= <<<EOF
                                        <img src="$image_url" />

EOF;
    }
}
echo $out;
?>
                                    </div>
                                </div>
                            </div>
                        </section>

<?
$db = new DB(PROJ_ROOT.'data/');
$categories = array('Staff', 'Undergraduate');
$alumni_category = 'Alumni';
$slugified_alumni_category = 'alumni';
$infos = $db->querySingle("SELECT * FROM lab_info LIMIT 1", TRUE);
$address = utf8_decode($infos['address']);
$lab_phone = utf8_decode($infos['phone']);
$lab_fax = utf8_decode($infos['fax']);
$lab_address = array();
foreach(explode("\n", $address) as $s) {
    $item = trim($s);
    if( !empty($item) ) {
        array_push($lab_address, "<p>".$item."</p>");
    }
}
$lab_address = implode("", $lab_address);

$out = '';
foreach($categories as $category) {
    $results = $db->query("SELECT * FROM staff WHERE category = '$category' ORDER BY sort ASC");
    $out .= <<<EOF
                        <section>

EOF;
    while( $row = $results->fetchArray() ) {
        $name = utf8_decode($row['name']);
        $slugified_name = slugify($name);
        $title = utf8_decode($row['title']);
        $subtitle = utf8_decode($row['subtitle']);
        $email = utf8_decode($row['email']);
        $phone = utf8_decode($row['phone']);
        $skype = utf8_decode($row['skype']);
        $bio = utf8_decode($row['bio']);
        $resp = utf8_decode($row['resp']);
        $image = utf8_decode($row['image_path']);
        $pi = $row['pi'];
        $out .= <<<EOF

                            <div class="row_container hidden">

EOF;
        $with_image = '';
        $image_url = '';
        $out_img = '';
        if( !empty($image) ) {
            $with_image = ' with_image';
            $image_url = MEDIA_URL."staff/".$image;
            $out_img = <<<EOF
                                    <div class="img-container">
                                        <img src="$image_url" alt="$name"/>
                                    </div>

EOF;
        }
        $out .= <<<EOF
                                <div class="contact_box page_curl bottom_right$with_image" id="$slugified_name">

EOF;
        $out .= $out_img;
        $out_name = $name;
        if( !empty($title) ) {
            $out_name .= ", ".$title;
        }
        if( !empty($subtitle) ) {
            $out_name .= " <span>(".$subtitle.")</span>";
        }
        $out .= <<<EOF
                                    <div class="info-container">
                                        <p class="name">$out_name</p>

EOF;
        if( $pi ) {
            $out .= <<<EOF
                                        $lab_address

EOF;
        }

        $out_contact = <<<EOF
                                        <div>
                                            <p>Contact:</p>

EOF;
        $out_contact_content = '';
        if( $pi ) {
            $out_contact_content .= <<<EOF
                                            <p>lab phone: $lab_phone</p>
                                            <p>lab fax: $lab_fax</p>

EOF;
        }
        if( !empty($phone) ) {
            $out_contact_content .= <<<EOF
                                            <p>phone: $phone</p>

EOF;
        }
        if( !empty($email) ) {
            $out_contact_content .= <<<EOF
                                            <p>email: <a href="mailto:$email" target="_blank">$email</a></p>

EOF;
        }
        if( !empty($skype) ) {
            $out_contact_content .= <<<EOF
                                            <p>skype: <a href="skype:$skype?chat">$skype</a></p>

EOF;
        }
        if( !empty($out_contact_content) ) {
            $out_contact .= $out_contact_content;
            $out_contact .= <<<EOF
                                        </div>

EOF;
            $out .= $out_contact;
        }

        if( !empty($bio) ) {
            $out .= <<<EOF
                                        <div>
                                            <p>Short Bio:</p>

EOF;
            foreach(explode("\n", $bio) as $s) {
                $item = trim($s);
                if( !empty($item) ) {
                    $out .= <<<EOF
                                            <p>$item</p>

EOF;
                }
            }
            $out .= <<<EOF
                                        </div>

EOF;
        }
        if( !empty($resp) ) {
            $out .= <<<EOF
                                        <div>
                                            <p>Research and responsibility in the lab:</p>

EOF;
            foreach(explode("\n", $resp) as $s) {
                $item = trim($s);
                if( !empty($item) ) {
                    $out .= <<<EOF
                                            <p>$item</p>

EOF;
                }
            }
            $out .= <<<EOF
                                        </div>

EOF;
        }
        $out .= <<<EOF
                                    </div>
                                </div>
                            </div>

EOF;
    }
    $out .= <<<EOF
                        </section>

EOF;
}

$results = $db->query("SELECT * FROM staff WHERE category = 'Alumni' ORDER BY sort ASC");
$out .= <<<EOF
                        <section>
                            <div class="row_container hidden">
                                <div id="$slugified_alumni_category">
                                    <div class="contact_box info-container page_curl bottom_right">

EOF;

while( $row = $results->fetchArray() ) {
    $name = utf8_decode($row['name']);
    $title = utf8_decode($row['title']);
    $subtitle = utf8_decode($row['subtitle']);
    $email = utf8_decode($row['email']);
    $phone = utf8_decode($row['phone']);
    $skype = utf8_decode($row['skype']);
    $bio = utf8_decode($row['bio']);
    $resp = utf8_decode($row['resp']);
    $image = utf8_decode($row['image_path']);
    $pi = $row['pi'];
    $out .= <<<EOF
                                        <div class="alumni_box">

EOF;
    $out_name = $name;
    if( !empty($title) ) {
        $out_name .= ", ".$title;
    }
    if( !empty($subtitle) ) {
        $out_name .= "<span>(".$subtitle.")</span>";
    }
    $out .= <<<EOF
                                            <p class="name">$out_name</p>

EOF;
    if( !empty($resp) ) {
        foreach(explode("\n", $resp) as $s) {
            $item = trim($s);
            if( !empty($item) ) {
                $out .= <<<EOF
                                            <p>$item</p>

EOF;
            }
        }
    }
    $out .= <<<EOF
                                        </div>

EOF;
}
echo $out;
?>
                                    </div>
                                </div>
                            </div>
                        </section>

                    </div>
                </div>

                <aside>
                    <nav id="side_nav">

                        <ul>
                            <li class="selected"><a href="#staff_pictures">Staff Pictures</a></li>
                        </ul>
<?
$out = "";
foreach($categories as $category) {
    $results = $db->query("SELECT name FROM staff WHERE category = '$category' ORDER BY sort ASC");
    $out .= <<<EOF
                        <h3>$category</h3>
                        <ul>

EOF;
    while( $row = $results->fetchArray() ) {
        $name = $row['name'];
        $slugified_name = slugify($name);
        $out .= <<<EOF
                                <li><a href="#$slugified_name">$name</a></li>

EOF;
    }
    $out .= <<<EOF
                        </ul>

EOF;
}
$out .= <<<EOF
                        <h3 id="$slugified_alumni_category-anchor">$alumni_category</h3>
                        <ul class="hidden">
                            <li><a id="$slugified_alumni_category-anchor_target" href="#$slugified_alumni_category"></a></li>
                        </ul>

EOF;
echo $out;
?>
                    </nav>
                </aside>

            </article>

            <footer class="clearfix">
                <p>The views and opinions expressed in this page are strictly those of the page author.</p>
                <p>The contents of this page have not been reviewed or approved by the University of Minnesota.</p>
                <p>Author &amp; Copyright: © <?php echo date("Y"); ?> Alessandro Bartolomucci --- Website designed by: Fabio Trabucchi</p>
            </footer>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="/js/libs/jquery-1.8.2.min.js"><\/script>')</script>
        <script src="/js/libs/jquery.nivo.slider.pack.js"></script>
        <script src="/js/scripts/misc.js"></script>
        <script src="/js/scripts/side_nav.js"></script>
    </body>
</html>
