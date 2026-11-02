
<?php
// Only accept form submissions using POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html#contact');
    exit;
}

// Basic anti-spam honeypot.
if (!empty($_POST['website'] ?? '')) {
    http_response_code(400);
    exit('Invalid submission.');
}

// Read and validate submitted fields.
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if (
    $name === '' ||
    strlen($name) > 100 ||
    $email === '' ||
    strlen($email) > 254 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $message === '' ||
    strlen($message) > 5000
) {
    http_response_code(400);
    exit('Please enter a valid name, email address and message.');
}

// Prevent header injection.
if (preg_match('/[\r\n]/', $email)) {
    http_response_code(400);
    exit('Invalid email address.');
}

// Recipient and sender.
// The sender should be an address belonging to your domain.
$to = 'info@sthavir.co.uk';
$from = 'info@sthavir.co.uk';

$subject = 'New website enquiry - Sthavir Engineering';

$body = "A new enquiry was submitted through your website.\n\n";
$body .= "Name: " . $name . "\n";
$body .= "Email: " . $email . "\n\n";
$body .= "Message:\n" . $message . "\n";

$headers = [
    'From' => 'Sthavir Website <' . $from . '>',
    'Reply-To' => $email,
    'Content-Type' => 'text/plain; charset=UTF-8'
];

// Send the email through the hosting server.
$sent = mail(
    $to,
    $subject,
    $body,
    $headers
);

if ($sent) {
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Enquiry Submitted</title>
      <link rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body class="container py-5">
      <h2>Thank you for contacting us!</h2>
      <p>Your enquiry has been submitted. We will get back to you as soon as possible.</p>
      <a class="btn btn-primary" href="index.html">Return to Website</a>
    </body>
    </html>';
} else {
    http_response_code(500);
    echo 'Sorry, we could not send your enquiry. Please try again later or email info@sthavir.co.uk directly.';
}
?>
