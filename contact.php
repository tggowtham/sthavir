
<?php
declare(strict_types=1);

// Handle POST requests only.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html#contact', true, 303);
    exit;
}

// Prevent cached responses.
header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');

// Simple spam protection: reject submissions with the hidden field filled.
if (!empty($_POST['website'] ?? '')) {
    header('Location: index.html?status=success#contact', true, 303);
    exit;
}

// Read and validate submitted values.
$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if (
    $name === '' ||
    strlen($name) > 100 ||
    $email === '' ||
    strlen($email) > 254 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    preg_match('/[\r\n]/', $email) ||
    $message === '' ||
    strlen($message) > 5000
) {
    http_response_code(400);
    echo 'Please enter a valid name, email address and message. '
       . '<a href="index.html#contact">Return to the contact form</a>.';
    exit;
}

// Email configuration.
$to = 'info@sthavir.co.uk';
$subject = 'New Website Enquiry - Sthavir Engineering Solutions';

// Keep the sender address on your own domain.
// The visitor's email is used as Reply-To.
$from = 'info@sthavir.co.uk';

$body = "A new enquiry was submitted through the website.\n\n";
$body .= "Name: " . $name . "\n";
$body .= "Email: " . $email . "\n\n";
$body .= "Message:\n" . $message . "\n";

$headers = [
    'From' => 'Sthavir Engineering Solutions <' . $from . '>',
    'Reply-To' => $email,
    'Content-Type' => 'text/plain; charset=UTF-8'
];

// Attempt to send the email.
try {
    $sent = mail($to, $subject, $body, $headers);
} catch (Throwable $e) {
    $sent = false;
}

if ($sent) {
    header('Location: index.html?status=success#contact', true, 303);
    exit;
}

http_response_code(500);
echo 'Sorry, your enquiry could not be sent. '
   . 'Please email info@sthavir.co.uk directly.';
?>
