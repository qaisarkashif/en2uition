<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head><title>Reset password on the site <?php echo $site_name; ?></title></head>
    <body>
        <div style="max-width: 800px; margin: 0; padding: 30px 0;">
            <p>You created a temporary password:</p>
            <p><strong>password: <?= $rand_pwd ?></strong></p>
            <p></p>
            <p>Use this password to log into the site.</p>
            <p>Don't forget to change the password in your settings once logged in.</p>
        </div>
    </body>
</html>