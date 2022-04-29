<?php
header('Content-Type: text/html; charset=utf-8');
define("PROJ_ROOT", "./");
define("MEDIA_ROOT", PROJ_ROOT."media/images/presentations/home/");
define("MEDIA_URL", "/media/images/presentations/home/");
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

        <title>Bartolomucci Lab</title>

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
                        <li class="selected"><a href="/">home</a></li>
                        <li><a href="/staff/">staff</a></li>
                        <li><a href="/research/">research</a></li>
                        <li><a href="/publications/">publications</a></li>
                        <li><a href="/news_events/">news &amp; events</a></li>
                        <li><a href="/teaching/">teaching</a></li>
                        <li><a href="/links/">links</a></li>
                        <li><a href="/contacts/">contacts</a></li>
                    </ul>
                </nav>
            </div>

            <article class="c20">
                <div id="content_wrap">
                    <div id="content">

                        <section>
                            <div class="row_container shown">
<?php
$out = '';
$db = new DB(PROJ_ROOT.'data/');
$body = $db->querySingle("SELECT body FROM flat_pages WHERE category = 'home'");
if( !(is_null($body) || empty($body)) ) {
    $b = utf8_decode($body);
    $out .= <<<EOF
                    $b
EOF;
}
$db->close();
echo $out;
?>
                            </div>
                        </section>

                    </div>
                </div>

                <aside>

                    <div class="box_title">Lab News</div>
                    <div class="info_box news_box">
                        <div class="news">
                            <p class="title">Recent Publication</p>
<?php
$db = new DB(PROJ_ROOT.'data/');
$news_event = $db->querySingle("SELECT title FROM publications ORDER BY year DESC, sort DESC LIMIT 1");
if( !is_null($news_event) && !empty($news_event) ) {
    $b = substr(strip_tags(utf8_decode($news_event)), 0, 100).' ...';
    echo <<<EOF
                            <p><a href="/publications/">$b</a></p>
EOF;
}
?>
                        </div>
                        <div class="news">
                            <p class="title">News &amp; Events</p>
<?php
$news_event = $db->querySingle("SELECT body FROM news_events ORDER BY sort DESC LIMIT 1");
if( !is_null($news_event) && !empty($news_event) ) {
    $b = substr(strip_tags(utf8_decode($news_event)), 0, 100).' ...';
    echo <<<EOF
                            <p><a href="/news_events/">$b</a></p>
EOF;
}
$db->close();
?>
                        </div>
                    </div>

                    <div class="info_box presentation">
                        <div id="slider" class="nivoSlider" data-pausetime="4500">
<?php
$out = '';
$images = array_diff(scandir(MEDIA_ROOT), array('..', '.'));
sort($images);
foreach($images as $image) {
    $image_path = MEDIA_ROOT.$image;
    $image_url = MEDIA_URL.$image;
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
    </body>
</html>
