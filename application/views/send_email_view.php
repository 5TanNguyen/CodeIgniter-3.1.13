<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email</title>
</head>

<body>
    <h2>Send Email</h2>
    <?php echo form_open('EmailController/send_email'); ?>

    <label for="from_email">From (Your Email):</label><br>
    <input type="email" name="from_email" required><br><br>

    <label for="to_email">To (Recipient Email):</label><br>
    <input type="email" name="to_email" required><br><br>

    <label for="subject">Subject:</label><br>
    <input type="text" name="subject" required><br><br>

    <label for="message">Message:</label><br>
    <textarea name="message" rows="5" required></textarea><br><br>

    <input type="submit" value="Send Email">

    <?php echo form_close(); ?>
</body>

</html>