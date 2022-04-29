<? session_start(); if( !isset($_SESSION['user']) ) { header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/admin/login.php' ); } ?>
<!doctype html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="/css/reset.css">
        <link rel="stylesheet" href="/css/base.css">
        <link rel="stylesheet" href="/css/admin.css">
    </head>
    <body>
        <header>
            <span>admin home</span>
            <span id="auth"><a href="/admin/auth/"><? echo $_SESSION['user']; ?></a><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <p class="title"><a href="/admin/staff/">staff</a></p>
            <p class="title"><a href="/admin/research_sections/">research sections</a></p>
            <p class="title"><a href="/admin/research_contents/">research contents</a></p>
            <p class="title"><a href="/admin/publication/">publications</a></p>
            <p class="title"><a href="/admin/news_event/">news &amp; events</a></p>
            <p class="title"><a href="/admin/flat_page/">flat pages</a></p>
            <p class="title"><a href="/admin/presentation/">presentation's media manager</a></p>
            <p class="title"><a href="/admin/images/">images's media manager</a></p>
            <p class="title"><a href="/admin/lab_info/">lab info</a></p>
        </div>
    </body>
</html>
