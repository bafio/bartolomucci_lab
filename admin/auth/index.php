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
            <span><a href="/admin/">admin home</a> > </span>
            <span>auth</span>
            <span id="auth"><? echo $_SESSION['user']; ?><a href="/admin/logout.php">log out</a></span>
        </header>
        <div class="container">
            <p class="title">
              <a href="/admin/auth/change_password.php?id=<? echo $_SESSION['userid']; ?>">change password</a>
            </p>
        </div>
    </body>
</html>