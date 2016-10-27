<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head><title>Received a message on <?php echo $site_name; ?>.</title></head>
    <body>
        <div style="max-width: 800px; margin: 0; padding: 30px 0;">
            <p>You receive a message from a user <?= $from_user ?></p>
            <p><a href="<?= $site_url ?>"><?= $site_url ?></a></p>
        </div>
    </body>
</html>