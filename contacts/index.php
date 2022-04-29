<?php
header('Content-Type: text/html; charset=utf-8');
define("PROJ_ROOT", "../");
require(PROJ_ROOT.'admin/db_handler.php');
$db = new DB(PROJ_ROOT.'data/');
$infos = $db->querySingle("SELECT * FROM lab_info LIMIT 1", TRUE);
$a = utf8_decode($infos['address']);
$phone = utf8_decode($infos['phone']);
$fax = utf8_decode($infos['fax']);
$email = utf8_decode($infos['email']);
$gmap_url = utf8_decode($infos['gmap_url']);
$db->close();
$address = array();
foreach(explode("\n", $a) as $s) {
    $item = trim($s);
    if( !empty($item) ) {
        array_push($address, "<p>".$item."</p>");
    }
}
$address = implode("", $address);
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

        <title>Bartolomucci Lab - Contacts</title>

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
                        <li><a href="/staff/">staff</a></li>
                        <li><a href="/research/">research</a></li>
                        <li><a href="/publications/">publications</a></li>
                        <li><a href="/news_events/">news &amp; events</a></li>
                        <li><a href="/teaching/">teaching</a></li>
                        <li><a href="/links/">links</a></li>
                        <li class="selected"><a href="/contacts/">contacts</a></li>
                    </ul>
                </nav>
            </div>

            <article class="flat no_bg">
                <section>
                    <div class="map_container">
                        <div class="info-container">
                            <div>
                                <p>Bartolomucci Lab</p>
                                <? echo $address; ?>
                            </div>
                        </div>
                        <div class="info-container">
                            <div>
                                <p>Contact:</p>
                                <p>phone: <? echo $phone; ?></p>
                                <p>fax: <? echo $fax; ?></p>
                                <p>email: <a href="mailto:<? echo $email; ?>" target="_blank"><? echo $email; ?></a></p>
                            </div>
                        </div>
                        <div class="map clearfix">
                            <iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="<? echo $gmap_url; ?>"></iframe>
                        </div>
                    </div>
                </section>
            </article>

            <footer class="clearfix">
                <p>The views and opinions expressed in this page are strictly those of the page author.</p>
                <p>The contents of this page have not been reviewed or approved by the University of Minnesota.</p>
                <p>Author &amp; Copyright: © <?php echo date("Y"); ?> Alessandro Bartolomucci --- Website designed by: Fabio Trabucchi</p>
            </footer>
        </div>
    </body>
</html>
