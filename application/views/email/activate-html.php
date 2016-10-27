<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head><title>Welcome to <?php echo $site_name; ?>!</title></head>
    <body>
        <div style="max-width: 800px; margin: 0; padding: 30px 0;">
            <h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">Welcome to <?php echo $site_name; ?>!</h2>
            Thanks for joining <?php echo $site_name; ?>.
            To verify your email address, please follow this link:<br />
            <br />
            <big style="font: 16px/18px Arial, Helvetica, sans-serif;"><b><a href="<?php echo site_url('activate/' . $user_id . '/' . $email_key); ?>" style="color: #3366cc;">Finish your registration...</a></b></big><br />
            <br />
            Link doesn't work? Copy the following link to your browser address bar:<br />
            <nobr><a href="<?php echo site_url('activate/' . $user_id . '/' . $email_key); ?>" style="color: #3366cc;"><?php echo site_url('activate/' . $user_id . '/' . $email_key); ?></a></nobr><br />
            <br />
            <br />
            <br />
            Have fun!<br />
            The <?php echo $site_name; ?> Team
        </div>
    </body>
</html>
